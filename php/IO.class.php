<?php
class IO {

    public static function root($path=null){ 
        $tmp = __DIR__;
        while(!file_exists($tmp . DS . "ROOT")) $tmp .= DS . "..";
        $tmp .= DS . ($path ? $path : '');
        return $tmp;
    }

    public static function js($file = null, $mode=CLIENT){
        $pre = "<script type='text/javascript' src='" . ($mode==APP ? "lib" : "webroot") . "/js/";
        $pos = "'></script>";
        if($file!==SCAN) echo $pre . $file . ".js" . $pos;
        else foreach(IO::scan(($mode==APP ? "lib" : "webroot") . DS . "js","js") as $file) echo $pre . $file . $pos;
    }

    public static function css($file = null, $mode=CLIENT){
        $pre = "<link rel='stylesheet' href='" . ($mode==APP ? "lib" : "webroot") . "/css/";
        $pos = "'/>";
        if($file!==SCAN) echo $pre . $file . ".css" . $pos;
        else foreach(IO::scan(($mode==APP ? "lib" : "webroot") . DS . "css","css") as $file) echo $pre . $file . $pos;
    }

     public static function jin($path=null,$obj=null){
        // print_r($obj); die;
        if($path===null) return Core::response(-1,"No path given");
        if($obj===null) return Core::response(-2,"No object given");
        // echo "<pre>"; print_r($obj);
        return IO::write($path,json_encode($obj),$mode,$default_path);
    }

    /* signature: jin('var/config.json');
     * reads a __JSON object from file on server
     * $p = path to save the file with archive name
     *
     */
    public static function jout($path){ 
        // echo "<pre>" . var_dump(IO::read($path));
        return json_decode(IO::read($path)); 
    }

    public static function read($f){ 
        if(substr($f,0,1)!=DS) $f = IO::root() . $f;
        // echo $f;
        return $f&&is_file($f) ? file_get_contents($f) : "";
    }

    public static function write($f,$content,$mode=REPLACE){
        if(substr($f,0,1)!=DS) $f = IO::root() . $f;

        // echo $f;
        // die;

        $tmp = explode(DS,$f);
        $tmp = implode(DS,array_slice($tmp,0,sizeof($tmp)-1));
        if(!is_dir($tmp)) mkdir($tmp,0777,true);
        @chmod($tmp,0777);
        $tmp = ($mode == APPEND ? IO::read($f) : "") . $content;
        file_put_contents($f,$content);
        @chmod($f,0777);
        // echo "<pre>$f\n$tmp";die;
        return is_file($f) ? 1 : 0;
    }

    public static function log($content){
        IO::write(IO::root("logs" . DS . User::logged() . "-default.log"));
    }

    /* signature: get_files('img/',"png");
     * get all files within a given folder, but "." and ".."
     * selecting only those with extension as $ext, if present
     * $p = path to the folder to be scanned
     * $x = file's extension to be supressed
     *
     */
    public static function scan($folder=null,$extension=null, $withfolders=true){
        if(substr($folder,0,1)!=DS) $folder = IO::root() . $folder;
        if($folder===null || !\is_dir($folder)) return [];
        $tmp = \scandir($folder);
        // var_dump($tmp);

        $result = [];
        if($tmp){
            foreach($tmp as $t){
                if(!($t=="." || $t=="..")){
                    if($extension!==null){ 
                        if(substr($t,strlen($extension)*-1)==$extension) $result[] = $t; 
                    }
                    else if($withfolders||!is_dir($folder.DS.$t)) $result[] = $t;
                }
            }
        }
        return $result;
    }

    /* signature: rem_folder('var/config.json');
     * removes a folder even if not empty
     * $p = path to the folder to be removed from server
     *
     */
    public function rmf($p=0){
        if(substr($p,0,1)!=DS) $p = IO::root() . $p;
        if(!$p || !\is_dir($p)) return;
        if(substr($p,strlen($p)-1)!=DS) $p .= DS;
        $files = \glob($p.'*', GLOB_MARK);
        foreach($files as $file){
            if (\is_dir($file)) IO::rmf($file);
            else \unlink($file);
        }
        if(\is_dir($p)) @\rmdir($p);
    }

    public static function mkf($dir,$perm=0644){
        if(substr($dir,0,1)!=DS) $dir = IO::root() . $dir;
        // umask(002);
        if(!is_dir($dir)) mkdir($dir,$perm,true);
        chmod($dir,$perm);
    }
    /* signature: rem_file('var/config.json');
     * removes only a single file, not a folder
     * $p = path to the file to be removed from server
     *
     */
    public function rm($p=null){ if($p===null) return; if(substr($p,0,1)!=DS) $p = IO::root() . $p; return \unlink($p); }

    public function cpr($f,$t) {
        if(substr($f,0,1)!=DS) $f = IO::root() . $f;
        $dir = opendir($f); 
        if(!is_dir($t)) mkdir($t,0764,true);
        chmod($t,0775);
        while($file = readdir($dir)){ 
            if($file!='.'&&$file!='..'){ 
                if(is_dir($f.'/'.$file)) IO::cpr($f.'/'.$file, $t.'/'.$file); 
                else copy($f.'/'.$file, $t.'/'.$file);
                chmod($t.'/'.$file,0775);
            }
        }
        closedir($dir);
        return \is_dir($t) ? true : false;
    }

    public function mv($f,$t){
        if(substr($f,0,1)!=DS) $f = IO::root() . $f;
        if(substr($t,0,1)!=DS) $t = IO::root() . $t;
        if($this->cpr($f,$t)) IO::rmf($f);
    }

    public function debug($anything=null){
        if($anything!==null) Debug::show(); 
        else print_r($anything);
    }
}