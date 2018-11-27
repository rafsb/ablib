<?php
class IO {

    private $root_folder;

    public function root($path=null){ 
        $tmp = __DIR__;
        while(!file_exists($tmp . DIRECTORY_SEPARATOR . "ROOT")) $tmp .= DIRECTORY_SEPARATOR . "..";
        $tmp .= DIRECTORY_SEPARATOR . ($path ? $path : '');
        return $tmp;
    }

    public function js($scan = false){ 
        $folder = IO::root("lib" . DIRECTORY_SEPARATOR . "js" .DIRECTORY_SEPARATOR);
        return $scan ? IO::scan($folder) : $folder; 
    }

    
    public function css($scan = false){ 
        $folder = IO::root("lib" . DIRECTORY_SEPARATOR . "css" .DIRECTORY_SEPARATOR);
        return $scan ? IO::scan($folder) : $folder; 
    }

    public function jin($path=null,$obj=null,$mode=REPLACE)
    {
        $s = false;
        if($path && $obj){
            $f = fopen($path,'w');
            if(IO::fwrite($f,json_encode($obj,JSON_PRETTY_PRINT))){ $s = 1; }
            fclose($f);
        }
        return $s;
    }

    /* signature: jin('var/config.json');
     * reads a __JSON object from file on server
     * $p = path to save the file with archive name
     *
     */
    public function jout($path){ return json_decode(file_get_contents($path)); }

    public function fread($f){ return $f ? file_get_contents($f) : null; }

    public function fwrite($f,$content,$mode=APPEND){ 
        $tmp = ($mode == APPEND ? IO::fread($f) : "") . $content;
        file_put_contents($f,$tmp);
        return 1;
    }

    public function log($content){
        IO::fwrite(IO::root("var" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . User::logged() . "-default.log"));
    }

    /* signature: get_files('img/',"png");
     * get all files within a given folder, but "." and ".."
     * selecting only those with extension as $ext, if present
     * $p = path to the folder to be scanned
     * $x = file's extension to be supressed
     *
     */
    public function scan($folder=null,$extension=null){
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
        //print_r($p);
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

    public function copy_entire_folder($f,$t) { 
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

    public function __construct(){
        $this->root_folder = $this->root();
    }
}