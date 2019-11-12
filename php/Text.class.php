<?php
define("LEFT", true);
define("RIGHT",false);
define("REVERSE",true);
define("CUT",true);
define("WHERE",true);


class Vector {
    public static function each(Array $arr, Clousure $fn) {
        foreach($arr as $k=>$v) {
            $fn($v, $k);
        }
    }
}


class Text{
    
    private $text = "";

    private $results = [];

    private static $sufix = [
        "ada", "ez", "eza", "ança", "ismo", "ância", "mento", "ção", "são", "dão", "tude", "ença", "ura"
        , "ário", "ária", "eiro", "eira", "ista", "or", "nte", "aria", "ário", "eiro", "il", "or", "tério"
        , "tório", "aço", "ada", "agem", "al", "ame", "ario", "aria", "edo", "eria", "io", "ume", "ite"
        , "oma", "ato", "eto", "ito", "ina", "ol", "ite", "ito", "ema", "io", "ismo"
        , "r", "er", "eres", "l", "is", "eis", "ils", "ção", "ções", "ar", "ando", "andado", "andada"
        , "ada", "ado", "adas", "ados", "s"
    ];

    private static $prepositions = [
        "a", "o", "ante", "após", "até", "com", "contra", "de", "desde", "em", "entre", "para", "por", "perante", "sem", "sob", "sobre"
        , "trás", "afora", "como", "conforme", "consoante", "durante", "exceto", "feito", "fora", "mediante", "menos", "salvo", "segundo", "senão"
        , "tirante", "visto", "aquele", "àquele" , "do", "uma" , "duma", "isto" , "disto", "as" , "nas", "um" , "num"
        , "essa" , "nessa", "pelo", "as" , "pelas", "ao", "os" , "aos", "onde", "aonde"
    ];

    private static $accented = [
        'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E'
        , 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U'
        , 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c'
        , 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o'
        , 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
    ];

    public static function sparse(String $tx, $offset=10, $dir=LEFT) {
        while(strlen($tx) < $offset) $tx = $dir ? $tx . " " : " " . $tx;
        return substr($tx, 0, $offset);
    }

    public static function word_body(String $arg) {
        foreach(self::$sufix as $sf) {
            if(substr($arg, strlen($arg) - strlen($sf), strlen($arg)) == $sf) {
                return substr($arg, 0, strlen($arg) - strlen($sf));
            }
        }       
        return $arg;
    }

    public static function remove_accented($text){
        return strtr($text, self::$accented);
    }

    public static function calc(String $text, $iniset=1) {
        $initial = [];
        $final   = [];
        $text = self::remove_accented($text);
        $arr = preg_split("/[\s,.\n\r\[\]\(\)\{\}0-9\/\'\"\`\':;\-\^\“_=\*]+/", strtolower($text), NULL, PREG_SPLIT_NO_EMPTY/PREG_SPLIT_OFFSET_CAPTURE);

        foreach($arr as $offset=>$word) {
            $tmp = self::word_body($word);
            if(strlen($tmp)>=$iniset && !in_array($tmp, self::$prepositions)) {
                if(!isset($initial[$tmp])) $initial[$tmp] = [ "founds" => [] ];
                if(!isset($initial[$tmp]["founds"][$word])) $initial[$tmp]["founds"][$word] = [];
                $initial[$tmp]["founds"][$word][] = $offset;
            }
        }

        // print_r($initial);
        
        foreach ($initial as $word => $content) {
            $num = 0;
            foreach($content["founds"] as $found) {
                foreach($found as $offsets) $num += is_array($offsets) ? sizeof($offsets) : 1;
            }
            $initial[$word]["sum"] = $num;
        }
        $sorted = array_map(function($arr) { return $arr["sum"]; }, $initial);
        
        arsort($sorted);

        // print_r($sorted);

        foreach($sorted as $word=>$offset) $final[] = json_decode(json_encode(["name"=>$word]+$initial[$word]));

        // print_r($final);

        return $final;
    }

    public function results(){
        return $this->results;
    }

    public function print($rev=false, $whr = false, $cut=false){
        
        $arr = $rev ? array_reverse($this->results, true) : $this->results;

        foreach($arr as $id=>$obj)
        {
            $print = self::sparse($id+1 . "º", 6) . " -> " . self::sparse($obj->name, 24, RIGHT) . self::sparse($obj->sum, 4, RIGHT) . "x ";
            if($whr){
                $print .= "| ";
                foreach((array)$obj->founds as $word=>$pos) $print .= $word . "(" . sizeof($pos) . ") ";
            }
            echo ($cut ? self::sparse($print . "...", 164) : $print) . PHP_EOL;
        }

        return $this;
    }

    public static function load(String $tx, $iniset=1){
        return (new Text($tx, [ "iniset" => $iniset ]));
    }

    private function generate_results($tx, $iniset=1){
        if(is_file($tx)) $this->text = file_get_contents($tx);
        $this->results = $this->calc($this->text, $iniset);
        return $this;
    }

    public function __construct(String $tx, Array $cfg = []){
        if($tx) $this->generate_results($tx, isset($cfg["iniset"]) ? $cfg["iniset"] : 1);
    }
};

$text = Text::load("tmp/sonegacao.txt", 4)->print(REVERSE, WHERE, CUT);
// $text = Text::load("tmp/sonegacao.txt", 4)->print(REVERSE, WHERE);
// $text = Text::load("tmp/sonegacao.txt", 4)->print(REVERSE);
// $text = Text::load("tmp/sonegacao.txt", 4)->print();