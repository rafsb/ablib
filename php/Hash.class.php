<?php
class Hash{
	private static $valid_haxits = [
		"md2"
		, "md4"
		, "md5"
		, "sha1"
		, "sha256"
		, "sha384"
		, "sha512"
		, "ripemd128"
		, "ripemd160"
		, "ripemd256"
		, "ripemd320"
		, "whirlpool"
		, "tiger128,3"
		, "tiger160,3"
		, "tiger192,3"
		, "tiger128,4"
		, "tiger160,4"
		, "tiger192,4"
		, "snefru"
		, "gost"
		, "adler32"
		, "crc32"
		, "crc32b"
		, "haval128,3"
		, "haval160,3"
		, "haval192,3"
		, "haval224,3"
		, "haval256,3"
		, "haval128,4"
		, "haval160,4"
		, "haval192,4"
		, "haval224,4"
		, "haval256,4"
		, "haval128,5"
		, "haval160,5"
		, "haval192,5"
		, "haval224,5"
		, "haval256,5"
	];
	
    public static function word($w=null,$h=null,$r=false){
    	$h = $h ? $h : App::config("hash_algorithm");
    	//print_r($w);die;
    	return \in_array($h,self::$valid_haxits) ? \hash($h,$w?$w:\uniqid(\rand()),$r) 
    	: "<pre>$h is not a recognized hash, try: <br/><br/>" . \implode("<br/>",self::$valid_haxits); 
    }

    public function all_haxits($w=null){
    	if(!$w) $w = \uniqid(\rand());
    	echo "<pre>word: $w<br/><br/>";
    	foreach(self::$valid_haxits as $h) echo $h . " => " . \hash($h,$w) . "<br/>";
    }

    public function recypher($hash, $tries=100){
    	$file = "var/hashes/cypher";
    	if(!is_file(IO::root($file))) return Core::response(0, "cypher file not found");
    	$cypher = IO::read($file);
    	$tries = $tries && $tries>0 && $tries<1000 ? $tries : 100;
    	$pass = false;
    	while($tries--&&!$pass){
    		if($cypher==$hash) $pass=true;
    		else $hash = Hash::word($hash);
    	}
    	IO::write($file,Hash::word($cypher));
    	return Core::response($pass?$hash:0, ($pass? "succeed" : "failed") . " on " . $tries);
    }
}