<?php
class Text {

    public static function sparse(String $tx, $offset=10, $dir=LEFT) {
        while(strlen($tx) < $offset) $tx = $dir ? $tx . " " : " " . $tx;
        return substr($tx, 0, $offset);
    }

    public static function flex(String $text) {
        if(!Request::sess("CL_WORD_SUFIX")) Request::sess("CL_WORD_SUFIX", IO::jout("etc/text.d/sufix.json"));
        $sufix = Request::sess("CL_WORD_SUFIX");

        // print_r($sufix); die;

        // foreach ($sufix as &$f) $f = '/' . preg_quote($f, '/') . '\b/';

        // print_r($sufix); die;

        // return preg_replace($sufix, '', $text);

        $final = [];

        if(strpos($text," ")!==false) $text = explode(" ", $text);
        else $text = [$text];

        foreach($text as $word){
            foreach($sufix as $sf) {
                if(substr($word, strlen($word) - strlen($sf), strlen($word)) == $sf) {
                    $final[] = substr($word, 0, strlen($word) - strlen($sf));
                    break;
                }
            }       
        }
        
        return implode(" ", $final);
    }

    public static function raccent(String $text){
        if(!Request::sess("CL_WORD_ACCENT")) Request::sess("CL_WORD_ACCENT", IO::jout("etc/text.d/accent.json"));
        $accent = Convert::otoa(Request::sess("CL_WORD_ACCENT"));
        // foreach($accent as &$acc) $acc = Convert::otoa($acc); 
        // print_r($accent); die;
        return strtr($text, $accent);
    }

    public static function rprep(String $text) {
        if(!Request::sess("CL_WORD_PREPOSITION")) Request::sess("CL_WORD_PREPOSITION", IO::jout("etc/text.d/preposition.json"));
        $prep = Request::sess("CL_WORD_PREPOSITION");
        foreach ($prep as &$p) $p = '/\b' . preg_quote($p, '/') . '\b/';
        return preg_replace($prep, '', $text);
    }

    public static function normalize(String $text, $rem_prepositions = false){
        $text = self::flex($text);
        $text = self::raccent($text);
        // print_r($text); die;
        if($rem_prepositions) $text = self::rprep($text);
        return $text;
    }

    public static function rank(String $text, $iniset=1) {
        
        $text = self::raccent($text, true);
        $initial = preg_split("/[\s,.\n\r\[\]\(\)\{\}0-9\/\'\"\`\':;\-\^\“_=\*]+/", $text, NULL, PREG_SPLIT_NO_EMPTY/PREG_SPLIT_OFFSET_CAPTURE);        
        $final   = [];

        // echo $iniset;print_r($initial); die;

        foreach($initial as $offset=>$word) {
            $tword = self::flex(strtolower($word));
            if(strlen($word)>=$iniset) {
                if(!isset($final[$tword])) $final[$tword] = [ "founds" => [] ];
                if(!isset($final[$tword]["founds"][$initial[$offset]])) $final[$tword]["founds"][$initial[$offset]] = [];
                $final[$tword]["founds"][$initial[$offset]][] = $offset;
            }
        }

        foreach ($final as $word => $content) {
            $num = 0;
            foreach($content["founds"] as $found) {
                foreach($found as $offsets) $num += is_array($offsets) ? sizeof($offsets) : 1;
            }
            $final[$word]["sum"] = $num;
        }
        $sorted = array_map(function($arr) { return $arr["sum"]; }, $final);
        arsort($sorted);
        
        $result  = [];
        foreach($sorted as $word=>$offset) if($word&&$offset) $result[] = json_decode(json_encode(["name"=>$word]+$final[$word]));

        return $result;
    }

    public static function frank(String $text, $iniset=1){
        $text = IO::read($text);
        return self::rank($text, $iniset);
    }

    public static function print(String $text, $iniset=1, $reverse=false, $where = false, $cut=false) {

        $text = self::rank($text, $iniset);
        $text = $reverse ? array_reverse($text, true) : $text;

        // print_r($text); die;
        
        foreach($text as $id=>$obj){
            $print = self::sparse($id+1 . "º", 6) . " -> " . self::sparse($obj->name, 24, RIGHT) . self::sparse($obj->sum, 4, RIGHT) . "x ";
            if($where){
                $print .= "| ";
                foreach((array)$obj->founds as $word=>$pos) $print .= $word . "(" . sizeof($pos) . ") ";
            }
            echo ($cut ? self::sparse($print . "...", 164) : $print) . PHP_EOL;
        }

    }

    public static function fprint(String $text, $iniset=1, $reverse=false, $where = false, $cut=false){
        $text = IO::read($text);
        self::print($text, $iniset, $reverse, $where, $cut);
    }

};

// $text = Text::fprint("tmp/sonegacao.txt", 4, REVERSE, WHERE, CUT);