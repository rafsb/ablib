<?php
class RealTime {
    
    private static $rtpath_ = "var".DS."realtime";
    private $bucket_;
    private $reference_;
    private $object_;

    private function loadBucket(){
        return IO::jout($this->bucket_);
    }

    private function loadRef(String $reference=null)
    {
        $tmp = $this->loadBucket();
        if($reference)
        {   
            $obj = $tmp;
            $reference = explode(DS,$reference);
            foreach($reference as $ref)
            {
                if($ref&&isset($obj->{$ref})) $obj = $obj->{$ref};
                else Core::response(-1, "invalid reference $ref");
            }
            $this->reference_ = $obj;
            return $obj;
        }
        else return $tmp;
    }

    public static function open(String $bucket=DEFAULT_COLLECTION)
    {
        return new RealTime($bucket);
    }

    public static function ref(String $ref=null, String $bucket=DEFAULT_COLLECTION){
        return RealTime::open($bucket)->loadRef($ref);
    }

    public function __construct(String $bucket=DEFAULT_COLLECTION)
    {
        $root = IO::root(self::$rtpath_);
        if(!is_file($root.DS.$bucket)) IO::jin($root.DS.$bucket,[]);
        $this->bucket_ = self::$rtpath_.DS.$bucket;
    }

}