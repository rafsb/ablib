<?php
class Image
{
    /*
     * @attribute 
     * 
     * false if there's an irregularity, true to allow upload
     *
     */
    private $stts;

    /*
     * @attribute 
     * 
     * temporary image object
     *
     */
    private $file;

    /*
     * @attribute 
     * 
     * name when saved
     *
     */
    private $newname;

    /*
     * @attribute 
     * 
     * path on server to be uploaded
     *
     */
    private $path;

    /*
     * @attribute 
     * 
     * image's size
     *
     */
    private $maxsize;

    /*
     * @attribute 
     * 
     * image's extension (jpg, png, gif, etc)
     *
     */
    private $extn;

    /*
     * @attribute 
     * 
     * a log to all ocurances over this upload trial
     *
     */
    private $log0;

    /*
     * @attribute 
     * 
     * try to force the upload even if there are some incoerence on some image's informations
     *
     */
    private $mini;

    /*
     * @attribute 
     * 
     * try to force the upload even if there are some incoerence on some image's informations
     *
     */
    private $forc;

    /*
     * @member function
     * 
     * returns inner occurence log file
     *
     */
    public function log(){ return $this->log0; }

    /*
     * @member function
     * 
     * GET or SET the path, as $p stands for path, if it is given, it will set inner path member, otherwise will get it
     * while fired run(), a path must be valid, or the $stts will be changed
     *
     */
    public function path($p=null){ if($p!==null) $this->path=$p; return $this->path; }

    /*
     * @member function
     * 
     * GET or SET new image's name wich will be used on save
     * if a name is not given on constructor's fire, a random one will be assigned
     *
     */
    public function newname($n=null){ if($n) $this->newname = $n; return $this->newname; }


    /*
     * @member function
     * 
     * GET or SET new image's name wich will be used on save
     * if a name is not given on constructor's fire, a random one will be assigned
     *
     */
    public function ext(){ $this->extn; }
    /*
     * @member function
     */
    public function name(){ return $this->file["filename"]; }
    /*
     * @member function
     * 
     * compares if a image size is compatible or inferior with the $s parameter, if false, $stts will be turned
     *
     */
    public function maxsize($s=null){ 
        if($s){
            if($this->maxsize < $s) return true; else{ $this->log0 .= "|image is bigger than ".$s."|"; $this->stts=false; return false; }
        }
        else return $this->size;
    }

    /*
     * @member function
     * 
     * turn the force flag to allow class try to upload the file even if $stts is setted as false
     *
     */
    public function mini(){ $this->mini = true; }

    /*
     * @member function
     * 
     * turn the force flag to allow class try to upload the file even if $stts is setted as false
     *
     */
    public function force(){ $this->forc = true; }

    /*
     * @member function
     * 
     * verify and upload the image
     *
     */
    public function run(){
        if(!in_array(strtolower($this->extn), ["jpg","png","gif","bmp","svg","jpeg","tiff"])){
            $this->log0 .= "|error: file extension didn't match(".$this->ext().")";
            $this->stts = false; 
        }
        if(!$this->file || !$this->file["size"]){ $this->log0 .= "|error: file missing"; $this->stts = false; }
        if($this->file["size"] > $this->maxsize){ $this->log0 .= "|error: file too big"; $this->stts = false; }
        if(!$this->path){ $this->log0 .= "|error: missing destination path"; $this->stts = false; }
        if(!$this->stts){ $this->log0 .= "|error: stts=0 (consider using force())"; }
        if($this->stts || $this->forc){
            if(!is_dir($this->path)){
                umask(0000);
                \mkdir($this->path,0777,true);
                if(!is_dir($this->path)){ $this->log0 .= "|error: couldn't create the destination folder: ".$this->path(); return -1; }
            }

            copy($this->file['tmp_name'], $this->path."tmpfile");
            if($this->forc) \rename($this->path."tmpfile", $this->path.$this->newname);
            else{
                if(!$this->forc && is_file($this->path.$this->newname)) \unlink($this->path.$this->newname);
                if(!$this->forc && $this->mini && is_file($this->path."mini_".$this->newname)) \unlink($this->path."mini_".$this->newname);
                \exec("convert -resize 1024x600 ".$this->path."tmpfile ".$this->path.$this->newname);
                //\exec("touch ".$this->path."TESTING");
                if($this->mini) \exec("convert -resize 256x256! ".$this->path."tmpfile ".$this->path."mini_".$this->newname);
                \unlink($this->path."tmpfile");
            }
            if(file_exists($this->path.$this->newname)) return 1; else $this->log0 .= "|error: seens file wasn't uploaded";
        }
        return 0;
    }

    public static function upload(){
        $args = request::in();
        $img = new Image($args["name"],$args["path"],8388608,$args["minify"],false);
        print_r($img);die;
        if($img->run()) echo $img->path.$img->newname; else echo $img->log0;
    }

    public function __construct($n=null,$p=null,$s=8388608,$m=false,$f=false)//name,path,size(max),minify?,force?
    {
        $this->file = $_FILES[current(array_keys($_FILES))];
        if($this->file["error"]) return Core::response(-1,"File upload error");
        $this->path = $p;
        $this->maxsize = $s;
        $this->mini = (bool)$m;
        $this->forc = (bool)$f;
        $this->stts = true;
        if(strpos($this->file["type"],"/"))
        {
            $this->extn = explode("/",$this->file["type"]);
            $this->extn = $this->extn[sizeof($this->extn)-1];
        }
        $this->newname = ($n?$n:$this->file["name"].".".$this->extn);
    }
}
