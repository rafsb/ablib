<?php
namespace spume;
require_once 'core.php';
class mail {
	public function send($data,$conf=null){
		if($data){
			if(is_array($data)) $data = atoo($data);
			$logo = "";
			$devl = "";
			$from = isset($data->mail) ? $data->mail : "";
			$to   = isset($data->dest) ? $data->dest : "";
			$subject = isset($data->subj) ? $data->subj : "";
			$message = isset($data->mesg) ? $data->mesg : "";
			$date = date("d/m/y");
			$stts = true;
			eval(file_get_contents(__DIR__."/../../etc/mailer.d/default.conf"));
			if($conf) eval(file_get_contents(__DIR__.'/../../etc/mailer.d/'.$conf.'.conf'));
			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From: <".$from.">" . "\r\n";
			$headers .= "To: <" . $to . ">" . "\r\n";
			$msgbody = "
				<html>
					<head>
						<title>".$subject."</title>
					</head>
					<body style='width:100%;position:relative;top:0;left:0;text-align:center;padding:5vh 0'>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<!PADDING>
						<div style='width:100%; padding-top:2rem'>
							<img style='position:relative;width:20%' src='".$logo."'/>
						</div>
						<div style='position:relative;width:100%;padding:2rem 0;background:whitesmoke;text-align:center'>
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
							<img style='height:2rem' src='".$devl."'/>
						</div>
					</body>
				</html>
			";
			if($stts&&$to&&$subject&&$msgbody&&$headers){ mail($to,$subject,$msgbody,$headers); return 1; }
			else return 0;
		}
	}
}