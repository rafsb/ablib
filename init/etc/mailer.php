<?php
namespace abox;
require '../lib/core.php';
require '../lib/date.php';

$post = in();
$from = $post['conf'];
$data = atoo($post['data']);
$to   = isset($data->mail)?$data->mail:null;
$subject = '';
$message = '';
$date = new Date();

eval(file_get_contents(__DIR__.'/mailer.d/'.$from.'.conf'));

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: <nao_responda@compartilheimoveis.com.br>" . "\r\n";
$headers .= "To: <" . $to . ">" . "\r\n";

//print_r($to);print_r($subject);print_r($message);print_r($headers);

$message = "<html><head><title>".$subject."</title></head><body style='text-align:center;padding:5vh 0'><img style='width:40%' src='http://compartilheimoveis.com.br/img/logok.png'><div style='width:100%;padding:5vh 0;background:whitesmoke;border:1px solid gray;text-align:center'>".$message."</div><div style='width:100%;background:black;text-align:center'><div class='width:100%;text-align:center;color:whitesmoke;font-size:8px'>Powered BY</div><br><img style='width:12vw' src='http://www.compartilheimoveis.com.br/img/std/dev.png'></div></body></html>";

//print_r($config_files);
echo mail($to,$subject,$message,$headers);