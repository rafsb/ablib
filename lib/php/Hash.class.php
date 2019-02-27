<?php
namespace spume;
require_once "core.php";
class hash{
	// returnd a 32 bytes unique character sequence, used as code for example
    public function hash($w=null,$h="sha512"){ return \hash($h,$w?$w:\uniqid(\rand())); }
}