<?php
class IO {

    private $root_folder;

    public static function root($path=null){ 
        $tmp = __DIR__;
        while(!file_exists($tmp . DS . "ROOT")) $tmp .= DS . "..";
        $tmp .= DS . ($path ? $path : '');
        return $tmp;
    }

    public function uri($uri){
        return IO::root() . DS .  $uri;
    }

    public static function scripts($file = null){ 
        $pre = "<script type='text/javascript' src='/lib/js/";
        $pos = "'></script>";
        if($file!==SCAN) echo $pre . $file . ".js" . $pos;
        else foreach(IO::scan("lib/js","js") as $file) echo $pre . $file . $pos;
    }

    public static function js($file = null){
        $pre = "<script type='text/javascript' src='webroot/js/";
        $pos = "'></script>";
        if($file!==SCAN) echo $pre . $file . ".js" . $pos;
        else foreach(IO::scan("webroot/js","js") as $file) echo $pre . $file . $pos;
    }
    
    public static function stylesheets($file = null){ 
        $pre = "<link rel='stylesheet' href='/lib/css/";
        $pos = "'/>";
        if($file!==SCAN) echo $pre . $file . ".css" . $pos;
        else foreach(IO::scan("lib/css","css") as $file) echo $pre . $file . $pos;
    }

    public static function css($file = null){
        $pre = "<link rel='stylesheet' href='webroot/css/";
        $pos = "'/>";
        if($file!==SCAN) echo $pre . $file . ".css" . $pos;
        else foreach(IO::scan("webroot/css","css") as $file) echo $pre . $file . $pos;
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
        if(substr($f,0,1)==DS) $f = IO::root() . $f;
        else $f = IO::root() . App::dir() . $f;
        // echo $f;
        return $f&&is_file($f) ? file_get_contents($f) : "";
    }

    public static function write($f,$content,$mode=REPLACE){
        if(substr($f,0,1)==DS) $f = IO::root() . $f;
        else $f = IO::root() . App::dir() . $f;
        $tmp = explode(DS,$f);
        $tmp = implode(DS,array_slice($tmp,0,sizeof($tmp)-1));
        umask(111);
        if(!is_dir($tmp)) mkdir($tmp,2777,true);
        $tmp = ($mode == APPEND ? IO::read($f) : "") . $content;
        file_put_contents($f,$content);
        // echo "<pre>$f\n$tmp";die;
        return is_file($f) ? 1 : 0;
    }

    public static function log($content){
        IO::fwrite(IO::root("logs" . DS . User::logged() . "-default.log"));
    }

    /* signature: get_files('img/',"png");
     * get all files within a given folder, but "." and ".."
     * selecting only those with extension as $ext, if present
     * $p = path to the folder to be scanned
     * $x = file's extension to be supressed
     *
     */
    public static function scan($folder=null,$extension=null,$root=true){
        if(substr($folder,0,1)==DS) $folder = IO::root() . $folder;
        if($folder===null || !@\is_dir($folder)) return [];
        $tmp = @\scandir($folder);
        $result = [];
        if($tmp){
            foreach($tmp as $t){
                if(!($t=="." || $t=="..")){
                    if($extension!==null){ if(substr($t,strlen($extension)*-1)==$extension) $result[] = $t; }
                    else $result[] = $t;
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
        if(!$p || !\is_dir($p)) return;
        if(substr($p,strlen($p)-1)!=="/") $p .= "/";
        $files = \glob($p.'*', GLOB_MARK);
        foreach($files as $file){
            if (\is_dir($file)) IO::rmf($file);
            else \unlink($file);
        }
        if(\is_dir($p)) \rmdir($p);
    }

    /* signature: rem_file('var/config.json');
     * removes only a single file, not a folder
     * $p = path to the file to be removed from server
     *
     */
    public function rm($p=null){ if($p===null) return; return \unlink($p); }

    public function cpr($f,$t) { 
        $dir = opendir($f); 
        mkdir($t);
        while($file = readdir($dir)){ 
            if($file!='.'&&$file!='..'){ 
                if(is_dir($f.'/'.$file)) copy_folder($f.'/'.$file,$t.'/'.$file); 
                else copy($f.'/'.$file, $t.'/'.$file); 
            }
        }
        closedir($dir);
        return \if_dir($t) ? true : false;
    }

    public function mv($f,$t){ if($this->copy_folder($f,$t)) \rem_folder($f); }

    public function debug($anything=null){
        if($anything!==null) Debug::show(); 
        else print_r($anything);
    }

    public function __construct(){
        $this->root_folder = $this->root();
    }
}