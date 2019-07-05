<?php

class RealTime extends IO {
    
    private static $rtpath_ = "var".DS."realtime";
    private $bucket_;
    private $reference_;
    private $object_;

    private function loadBucket(){
        return self::jout($this->bucket_)
    }

    public static function connect(String $bucket=DEFAULT_COLLECTION)
    {
        return new RealTime($bucket);
    }


    public function __construct(String $bucket=DEFAULT_COLLECTION, String $reference=null)
    {
        $root = self::root(self::rtpath_);
        if(!is_file($root.DS.$bucket)) self::jin($root.DS.$bucket,[]);
        $this->bucket_ = $bucket;
        if($reference)
        {   
            $tmp = $this->loadBucket();
        }
    }

}