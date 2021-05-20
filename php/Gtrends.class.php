<?php
class Gtrends extends Activity
{

    private static function parse_query_string(String $q)
    {
        return preg_replace("/\s/", "_", implode(",", preg_split("/[|&+,]/", $q, -1, PREG_SPLIT_NO_EMPTY)));
    }

    private static function sum(Array $in)
    {
        $final = [];
        Vector::each($in, function($data) use (&$final){
            Vector::each($data, function($value, $date) use (&$final){
                if(!isset($final[$date])) $final[$date] = 0;
                $final[$date] += $value;
            });
        });
        return (object)[ implode("+", Vector::extract($in, function($v, $name){ return $name; })) => $final ];
    }

    protected static function parse_rules($q, $results)
    {
        if(empty($results)) return [];
        $final = [];
        $search_terms = [];
        Vector::each(preg_split("/[|,]/", $q, -1, PREG_SPLIT_NO_EMPTY), function($item) use (&$search_terms){ $search_terms[] = preg_split("/[&+]/", $item); });
        Vector::each($search_terms, function($grp) use ($results, &$final){
            if(sizeof($grp)>1)
            {
                $tmp = [];
                foreach($grp as $item) $tmp[$item] = $results[$item];
                $final[] = self::sum($tmp);
            } else $final[] = [ $grp[0] => $results[$grp[0]] ];
        });
        
        return $final;
    }

    private static function execute_request(String $q, String $date, $date_length = 10)
    {
        $all = Vector::extract(core::bin("gtrends/fetch.sh", [ 
            "--items=" . self::parse_query_string($q)
            , "--date={$date}"
        ]), function($line){ return preg_split("/[,]/", $line); });
        if(empty($all)) return [];
        $head = $all[0];
        $all = array_slice($all, 1);
        $final = [];
        Vector::each($all, function($line) use (&$final, $head, $date_length){
            if(empty($line)) return;
            $date = substr($line[0], 0, $date_length);
            Vector::each(array_slice($line, 1, sizeof($line)-2), function($value, $i) use (&$final, $head, $date){
                $player = $head[$i+1];
                if(!isset($final[$player])) $final[$player] = [];
                if(!isset($final[$player][$date])) $final[$player][$date] = 0;
                $final[$player][$date] += (int)$value;
            });
        });
        return $final;
    }

    protected static function request(String $q, String $date, int $date_offset)
    {
        return self::parse_rules($q, self::execute_request($q, $date, $date_offset));
    } 

    public function fetch(String $q=null, String $date=null, String $hash=null)
    {
        if(!self::get_hash($hash)) return core::Response(0, "GTrends::fetch -> no valid hash given");        
        $args = (object)Request::in();
        $q = $q ? $q : (isset($args->q) ? $args->q : false);
        if(!$q) return core::response(0, "gtrends::fetch -> no valid query given");
        $date = $date ? $date : (isset($args->date) ? $args->date : "now_7-d");
        return self::request($q, $date, $date == "today_5-y" || $date == "today_12-m" ? 7 : 10);
    }

    public function render()
    {
        if(!self::get_hash($hash)) return core::Response(0, "GTrends::render -> no valid hash given");        
        $args = (object)Request::in();
        $q = isset($args->q) ? $args->q : false;
        if(!$q) return core::response(0, "gtrends::render -> no valid query given");
        $date = isset($args->date) ? $args->date : "now_7-d";
        return self::request($q, $date, $date == "today_5-y" || $date == "today_12-m" ? 7 : 10);
    }
    
}