<?php
class Request {
    ## perform isertion into $_SESSION array, giving values to new fields
    ## on success case, it returns the given value, else 0 is the answer
    ## i.e: sess("us\er","abox"), is the same as $_SESSION["projectName_user"] = "abox"

    public function sess($f,$v=null){
        if($v!==null) $_SESSION[$f] = $v;
        return (isset($_SESSION[$f]) ? $_SESSION[$f] : 0);
    }

    public function usess($f){ if(isset($_SESSION[$f])) unset($_SESSION[$f]); }

    ## perform isertion into $_COOKIE array, giving values to new fields
    ## on success case, it returns the given value, else 0 is the answer
    ## i.e: cook("user","abox"), is the same as $_SESSION["projectName_user"] = "abox"
    public function cook($f,$v=null){
        if($v!==null) setcookie("SP-".$f,$v,(int)(1000*60*60*30*365),"/");
        return (isset($_COOKIE["SP-".$f])?$_COOKIE["SP-".$f]:0);
    }

    ## reads the $_POST array arguments into the page it"s included, but not all of them, only those inside "obj"
    ## "obj" is the parameter that works like a bridge from fn.js to fn.php
    ## $_POST["obj"] may contain many conn inside, passing a argument, it return the selected field, if it"s setted
    public function in($f=null){
        $tmp = otoa(json_decode(file_get_contents("php://input")));
        if($f!==null){
            if(isset($tmp["obj"])) return (!empty($tmp["obj"][$f]) ? $tmp["obj"][$f] : null);
            else return(!empty($tmp[$f]) ? $tmp[$f] : null);
        }else{
            if(!empty($tmp["obj"])) return $tmp["obj"];
            else return(!empty($tmp) ? $tmp : null);
        }
    }

    ## reads the $_POST array arguments into the page it"s included, but not all of them, only those inside "obj"
    ## "obj" is the parameter that works like a bridge from fn.js to fn.php
    ## $_POST["obj"] may contain many conn inside, passing a argument, it return the selected field, if it"s setted
    public function post($f=null)
    {
        if($f!==null)
            if(isset($_POST["obj"])) return (!empty($_POST["obj"][$f]) ? $_POST["obj"][$f] : null);
            else return(!empty($_POST[$f]) ? $_POST[$f] : null);
        else
            if(!empty($_POST["obj"])) return $_POST["obj"];
            else return $_POST?$_POST:null;
    }

    public function get($f=null){
        if($f!==null) return (isset($_GET[$f]) ? $_GET[$f] : null);
        else return(isset($_GET) ? $_GET : null);
    }
}