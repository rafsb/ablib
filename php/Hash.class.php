<?php
namespace lib;

class Hash{
	// returnd a 32 bytes unique character sequence, used as code for example
    public static function hash($w=null,$h="sha512"){ return \hash($h,$w?$w:\uniqid(\rand())); }
}