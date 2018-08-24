<?php
header("access-control-allow-origin: https://pagseguro.uol.com.br");
header("Content-Type: text/html; charset=UTF-8",true);

require_once "../user.php";
require_once "class.php";

$user = user();
$in = in();
$_PS = new PS();

if(!isset($_GET['move'])) $_PS->checkout($in->item,$in->client,"http://ab-x.me/lib/ps/sell.php?move=".$in->item->code);
else{ //callback
	$payment = $_PS->status($_GET['move']);
	file_put_contents('payment_'.$_GET['move'].'txt',json_encode($_GET,JSON_PRETTY_PRINT));
}?>