<?php
class Convert
{
    // stdobject to array
    public static function otoa(object $o)
    {
        $r = (array)$o;
        foreach($r as $k => &$v){ if(is_object($v)) $v = static::otoa($v); }
        return $r;
    }

    public static function atoo(array $a)
    {
        foreach($a as $k=>$v) if(is_array($v)) $a[$k] = static::atoo($v);
        return (object)$a;
    }

    public static function atoh(array $a)
    {
        $t = "";
        foreach($a as $k=>$v) if(is_array($a[$k])) $t.=self::atoh($a[$k]); else $t.=str_replace(" ","+",$k)."=".str_replace(" ","+",$v)."&";
        return substr($t,0,strlen($t)-1);
    }

    public static function xtoo($xml)
    {
        return (object)simplexml_load_string($xml);
    }

    public static function otoc($obj, $delimiter=";", $endline="\n")
    {
        if(!$obj || !sizeof((array)$obj)) return Core::response(0, "Convert::obj2csv => No obj given");
        $csv = "";
        foreach($obj as $k=>$line){
            $csv .= $k . $delimiter;
            if($line){
                if(is_array($line) || is_object($line)){
                    foreach($line as $cell){
                        if(is_array($cell) || is_object($cell)) $csv .= preg_replace("/[\n\r]/", "", _As::json($cell));
                        else $csv .= preg_replace("/[\n\r]/", "", $cell);
                        $csv .= $delimiter;
                    }
                } else $csv .= $line;
            }
            $csv .= $endline;
        }
        return $csv;
    }

    public static function json($input)
    {
        if(!is_string($input)) return json_encode($input);
        return json_decode($input);
    }

    public static function base($input, $json=true)
    {
        if(is_string($input)) return $json ? self::json(base64_decode($input)) : base64_decode($input);
        else return $json ? base64_encode(self::json($input)) : base64_decode($input);
    }

    public static function encrypt(String $file, $input)
    {
        $key = App::config()->encrypt_key;
        $cipher = App::config()->encrypt_cipher;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        if(is_object($input)||is_array($input)) $input = self::json($input);
        $final = openssl_encrypt($input, $cipher, $key, $options=0, $iv, $tag);
        IO::write($file . "_key", $iv . PHP_EOL . $tag);
        IO::write($file, $final);
        return $final;
    }

    public static function decrypt(String $file)
    {
        $keys = explode(PHP_EOL, IO::read($file . "_key"));
        return openssl_decrypt(IO::read($file), App::config()->encrypt_cipher,  App::config()->encrypt_key, $options=0, $keys[0], $keys[1]);
    }

}
