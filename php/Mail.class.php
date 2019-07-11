<?php


use Convert;
use Request;

class Mail {
	public function send($conf=null,$data=null){
		if(!$data) $data = Request::in();
		if($data){
			if(is_array($data)) $data = Convert::atoo($data);
			$logo = "";
			$devl = "";
			$from = isset($data->mail) ? $data->mail : "";
			$to   = isset($data->dest) ? $data->dest : "";
			$subject = isset($data->subj) ? $data->subj : "";
			$message = isset($data->mesg) ? $data->mesg : "";
			$date = date("d/m/y");
			$stts = true;
			if(is_file(__DIR__."/../../etc/mailer.d/default.conf")) eval(file_get_contents(__DIR__."/../../etc/mailer.d/default.conf"));
			$conf = $conf ? $conf : $data->conf;
			if($conf&&is_file(__DIR__.'/../../etc/mailer.d/'.$conf.'.conf')) eval(file_get_contents(__DIR__.'/../../etc/mailer.d/'.$conf.'.conf'));
			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From: <".$from.">" . "\r\n";
			$headers .= "To: <" . $to . ">" . "\r\n";
			$msgbody = "
				<html>
					<head>
						<title>".$subject."</title>
					</head>
					<body style='width:100%;position:relative;top:0;left:0;text-align:center;padding:5vh 0;background:#1F1F1F;color:whitesmoke;'>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<div style='width:100%; padding-top:2rem'>
							<img style='position:relative;width:20%' src='".$logo."'/>
						</div>
						<div style='position:relative;width:100%;padding:2rem 0;text-align:center'>
							<div style='width:80%;margin-left:10%'>".$message."</div>
							<br>
							<br>
							<!PADDING>
							<!PADDING>
							<!PADDING>
							<!PADDING>
						</div>
						<br>
						<br>
						<br>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<div style='position:relative;width:100%;text-align:center;padding:2rem 0;font-size:.5rem'>
							Powered BY
						</div>
						<div style='position:relative;width:100%;text-align:center;padding-bottom:4rem'>
							<img style='width:20%' src='".$devl."'/>
						</div>
					</body>
				</html>
			";

			// echo $msgbody;

			// print_r($data);

			if($stts&&$to&&$subject&&$msgbody&&$headers){ \mail($to,$subject,$msgbody,$headers); echo 1; }
			else echo 0;
		}
	}
}