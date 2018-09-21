<?php
namespace abox;
require '../user.php';
require_once '../date.php';
require_once '../../etc/mailer.php';
$hint = in('hint');
$user = qout("SELECT * FROM Users WHERE user='$hint' OR mail='$hint' OR doc0='$hint'",AB_OBJECT);
if($hint && $user->status()){
	$user = $user->data();
	$hash = $user->code."-".hash('sha256',(new Date())->today());
	file_put_contents(root("var/users/".$user->code."/pswd_reset_valid_hash",AB_FILE),$hash);
	mailer(["conf"=>"pswd_reset","data"=>["mail"=>$user->mail,"hash"=>$hash]]);
	echo '{"stts":"1","hash":"'.$hash.'","mail":"'.$user->mail.'"}';
}else echo '{"stts":"0","error":"O valor enviado não foi suficiente para encontrar seu usuário em nossa base."}';