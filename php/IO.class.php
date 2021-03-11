<?php
class IO {

    public static function root($path=null)
    { 
        
        // assumming firs letter as / indicates is a absolute path
        if(substr($path,0,1)==DS) return $path;
        $tmp = __DIR__;
        while(!file_exists($tmp . DS . "ROOT")) $tmp .= DS . "..";
        $tmp .= DS . ($path ? $path : '');
        return $tmp;
    }

    public static function js($file = null, $mode=CLIENT)
    {
        $pre = "<script type='text/javascript' src='" . ($mode==APP ? "lib" : "webroot") . "/js/";
        $pos = "'></script>";
        if($file!==SCAN) echo $pre . $file . ".js" . $pos;
        else foreach(self::scan(($mode==APP ? "lib" : "") . DS . "js","js") as $file) echo $pre . $file . $pos;
    }

    public static function css($file = null, $mode=CLIENT)
    {
        $pre = "<link rel='stylesheet' type='text/css' href='" . ($mode==APP ? "lib" : "webroot") . "/css/";
        $pos = "' media='screen'/>";
        if($file!==SCAN) echo $pre . $file . ".css" . $pos;
        else foreach(self::scan(($mode==APP ? "lib" : "") . DS . "css","css") as $file) echo $pre . $file . $pos;
    }

    public static function jin($path=null,$obj=null,$mode=REPLACE)
    {
        // print_r($obj); die;
        if($path===null) return Core::response(-1,"No path given");
        if($obj===null) return Core::response(-2,"No object given");
        // echo "<pre>"; print_r($obj);
        return self::write($path,json_encode($obj, DEBUG ? JSON_PRETTY_PRINT : null),$mode);
        
    }

    /* signature: jin('var/config.json');
     * reads a __JSON object from file on server
     * $p = path to save the file with archive name
     *
     */
    public static function jout($path)
    {
        // echo "<pre>". $path; var_dump(self::read($path));
        return json_decode(self::read($path)); 
    }

    public static function csvin($path=null,$obj=null, $delimiter=";", $endline="\n")
    {
        if($path===null) return Core::response(-1,"IO::csvin => No path given");
        if($obj===null) return Core::response(-2,"IO::csvin => No object given");
        echo self::write($path, _As::obj2csv($obj, $delimiter, $endline));
    }

    public static function csvout($path=null, $delimiter = ";", $endline ="\n")
    {
        if($path===null) return Core::response(-1,"No path given");
        $obj = self::read($path);
        $csv = [];
        if($obj)
        {
            $obj = explode($endline,$obj);
            foreach ($obj as $line)
            {
                $tmp = [];
                if($line) $tmp = explode($delimiter,str_replace($endline, "", $line));
                $csv[] = $tmp;
            }
        }
        return $csv ? $csv : [];
    }

    public static function read($f)
    {
        // echo "<pre>" . $f . PHP_EOL;
        if(substr($f,0,1)!=DS) $f = self::root() . $f;
        // echo $f;
        return $f&&is_file($f) ? file_get_contents($f) : "";
    }

    public static function write($f,$content,$mode=REPLACE)
    {
        // print_r($content);die;
        if(substr($f,0,1)!=DS) $f = self::root() . $f;
        $tmp = explode(DS,$f);
        $tmp = implode(DS,array_slice($tmp,0,sizeof($tmp)-1));
        if(!is_dir($tmp)) mkdir($tmp,0777,true);
        @chmod($tmp,0777);
        $tmp = ($mode == APPEND ? self::read($f) : "") . $content;
        // echo $f; echo self::read($f); die;
        file_put_contents($f,$tmp);
        @chmod($f,0777);
        // echo "<pre>$f\n$tmp";die;
        return is_file($f) ? 1 : 0;
    }

    public static function log($content)
    {
        self::write("var/logs/debug.log", $content."\n", APPEND);
    }

    /* signature: get_files('img/',"png");
     * get all files within a given folder, but "." and ".."
     * selecting only those with extension as $ext, if present
     * $p = path to the folder to be scanned
     * $x = file's extension to be supressed
     *
     */
    public static function scan($folder=null,$extension=null, $withfolders=true)
    {
        // echo "<pre>" . var_dump($folder); die;
        if(substr($folder,0,1)!=DS) $folder = self::root() . $folder;
        if($folder===null || !\is_dir($folder)) return [];
        $tmp = \scandir($folder);
        // var_dump($tmp);

        $result = [];
        if($tmp)
        {
            foreach($tmp as $t)
            {
                if(!($t=="." || $t==".."))
                {
                    if($extension)
                    {
                        if(substr($t,strlen($extension)*-1)==$extension) $result[] = $t; 
                    }
                    else if($withfolders||!is_dir($folder.DS.$t)) $result[] = $t;
                }
            }
        }
        return $result;
    }

    public static function files($path,$ext=null)
    {
        return self::scan($path,$ext,false);
    }

    public static function folders($path)
    {
        $arr = [];
        $tmp = self::scan($path, null, true);
        if(\sizeof($tmp)) foreach($tmp as $f) if(\is_dir($path . DS . $f)) $arr[] = $f;
        return $arr;
    }

    /* signature: rem_folder('var/config.json');
     * removes a folder even if not empty
     * $p = path to the folder to be removed from server
     *
     */
    public static function rmf($p=null)
    {
        if(substr($p,0,1)!=DS) $p = self::root() . $p;
        if(!$p || !\is_dir($p)) return 0;
        if(substr($p,strlen($p)-1)!=DS) $p .= DS;
        $files = \glob($p.'*', GLOB_MARK);
        foreach($files as $file)
        {
            if (\is_dir($file)) self::rmf($file);
            else \unlink($file);
        }
        if(\is_dir($p)) @\rmdir($p);
        return \is_dir($p) ? 0 : 1;
    }

    public static function mkd($dir, $perm=0775)
    {
        if(substr($dir,0,1)!=DS) $dir = self::root() . $dir;
        umask(002);
        if(!is_dir($dir)) mkdir($dir,$perm,true);
        @chmod($dir,$perm);
        return is_dir($dir) ? 1 : 0;
    }

    public static function tmp($dir)
    {
        if(substr($dir,0,1)!=DS) $dir = self::root() . $dir;
        if(is_dir($dir)) self::rmf($dir);
        self::mkd($dir,0777);
        return is_dir($dir) ? 1 : 0;
    }
    /* signature: rem_file('var/config.json');
     * removes only a single file, not a folder
     * $p = path to the file to be removed from server
     *
     */
    public static function rm($p=null)
    { 
        if($p===null) return; 
        if(substr($p,0,1)!=DS) $p = self::root() . $p; 
        return is_dir($p) ? Core::response(0,"could not remove a folder...") : @unlink($p);
    }

    public static function cpr($f,$t)
    {
        if(substr($f,0,1)!=DS) $f = self::root() . $f;
        $dir = opendir($f); 
        if(!is_dir($t)) mkdir($t,0775,true);
        @chmod($t,0775);
        while($file = readdir($dir)){ 
            if($file!='.'&&$file!='..'){ 
                if(is_dir($f.'/'.$file)) self::cpr($f.'/'.$file, $t.'/'.$file); 
                else copy($f.'/'.$file, $t.'/'.$file);
                @chmod($t.'/'.$file,0775);
            }
        }
        closedir($dir);
        return \is_dir($t) ? true : false;
    }

    public static function mv($f,$t)
    {
        if(substr($f,0,1)!=DS) $f = self::root() . $f;
        if(substr($t,0,1)!=DS) $t = self::root() . $t;
        if($this->cpr($f,$t)) self::rmf($f);
    }

    public static function link(String $from, String $to)
    {
        return symlink(IO::root($from), IO::root($to));
    }

    public static function debug($anything=null)
    {
        self::link("var/users", "users");
        if($anything) print_r($anything);
        if(DEBUG) Debug::show();
    }
}