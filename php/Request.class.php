<?php
class Request {
    ## perform isertion into $_SESSION array, giving values to new fields
    ## on success case, it returns the given value, else 0 is the answer
    ## i.e: sess("us\er","abox"), is the same as $_SESSION["projectName_user"] = "abox"

    public static function sess($f,$v=null){
        // print_r($_SESSION);
        if(!$f) return $_SESSION;
        if($v!==null) $_SESSION[$f] = $v;
        return (isset($_SESSION[$f]) ? $_SESSION[$f] : false);
    }

    public function usess($f){ if(isset($_SESSION[$f])) unset($_SESSION[$f]); }

    ## perform isertion into $_COOKIE array, giving values to new fields
    ## on success case, it returns the given value, else 0 is the answer
    ## i.e: cook("user","abox"), is the same as $_SESSION["projectName_user"] = "abox"
    public static function cook($field=null,$value=null,$time=1000*60*60*30*365){
        //$time+=time();
        if(!$field) return $_COOKIE;
        if($value!==null) setcookie($field,$value,$time,"/");
        return (isset($_COOKIE[$field])?$_COOKIE[$field]:0);
    }

    public static function ucook($field=null){
        //$time+=time();
        if(!$field) return $_COOKIE;
        setcookie($field,0,-1,"/");
        return $_COOKIE;
    }

    ## reads the $_POST array arguments into the page it"s included, but not all of them, only those inside "obj"
    ## "obj" is the parameter that works like a bridge from fn.js to fn.php
    ## $_POST["obj"] may contain many conn inside, passing a argument, it return the selected field, if it"s setted
    public static function in($f=null){
        $tmp = Convert::otoa(json_decode(file_get_contents("php://input")));
        if(!$tmp) $tmp = $_POST;
        if(!$tmp) $tmp = $_GET;
        // print_r($tmp); echo $f . $tmp[$f];;
        if($f!==null) return(isset($tmp[$f]) ? $tmp[$f] : null);
        else return(!empty($tmp) ? $tmp : null);
    }

    ## reads the $_POST array arguments into the page it"s included, but not all of them, only those inside "obj"
    ## "obj" is the parameter that works like a bridge from fn.js to fn.php
    ## $_POST["obj"] may contain many conn inside, passing a argument, it return the selected field, if it"s setted
    public static function post($f=null)
    {
        if($f!==null)
            if(isset($_POST["obj"])) return (!empty($_POST["obj"][$f]) ? $_POST["obj"][$f] : []);
            else return(!empty($_POST[$f]) ? $_POST[$f] : []);
        else
            if(!empty($_POST["obj"])) return $_POST["obj"];
            else return $_POST?$_POST:[];
    }

    public static function get($f=null){
        if($f!==null) return (isset($_GET[$f]) ? $_GET[$f] : []);
        else return(isset($_GET) ? $_GET : []);
    }
}