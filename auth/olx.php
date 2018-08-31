<?php

namespace abox;

require("../std.php");
require("authclient.php");



function olx_authenticate()
{
	return atoo([ "auth_url" => (new AuthClient(Accounts::OLX))->authUrl() ]);
}

function olx_handshake()
{
	$olx = new AuthClient(Accounts::OLX);
	$out = __DIR__."/_token";
	$prj = conf("project_name")."_olx_access_token";
	
	rem_file($out);
	exec("wget --output-document $out http://www.aboxsoft.me/_services/olx/$prj");
	$f = trim(file_get_contents($out));
	
	if($f)
	{
		//print_r($f);
		$olx->sign($f);
		//print_r($olx->result());
		if(isset($olx->result()->access_token))
			return atoo(["access_token" => sess(conf("project_name")."_olx_access_token",$olx->result()->access_token)]);
		//return $olx->result();
	}
	else return atoo([ "error" => "Token não encontrado" ]);
}

function olx_get_info()
{
	$olx = new AuthClient(Accounts::OLX);
	$t = sess(conf("project_name")."_olx_access_token");
	if(!$t) return atoo([ "error" => "É preciso efetuar a autenticação junto à OLX" ]);
	$olx->info(["access_token"=>$t]);
	return $olx->result();
}

function olx_publish($c=null)
{
	if(!sess(conf("project_name")."_olx_access_token")) return atoo([ "error" => "É preciso efetuar a autenticação junto à OLX" ]);
	$pr = atoo(qout("SELECT * FROM Properties WHERE code='$c'")->fetch_assoc());
	if($pr)
	{
		$info = conf();
		$errors = [];
		$val = explode(".",$pr->val0)[0];
		$cat = qcell("Types","rule","code='".$pr->type."'"); 			   if(!$cat) $errors[] = "Tipo não compatível com a Olx";
		$hed = substr($pr->header,0,90); 							   if(!$hed) $errors[] = "Falta um título";
		$des = substr($pr->pdes,0,5800); 								   if(!$des) $errors[] = "Falta descrição";
		$id0 = qcell("Properties","id00","code='$c'");
		$des .= " - Link direto: ".conf("domain_name")."?p=$id0 - Código: $id0";
		$zip = preg_replace("/[^0-9]/","",$pr->zipc); 					   if(!$zip) $errors[] = "Falta CEP";
		$pho = preg_replace("/[^0-9]/","",(string)$info->phones[0][0]);
		if(!$pho) $errors[] = "Falta um numero de telefone para contato";
		else if(is_array($pho)) $pho = implode("",$pho);
		if(strlen($pho)>11) $pho = substr($pho,strlen($pho)-11);

		if(!sizeof($errors))
		{
			$arg = [];	
			if($cat)
			{
				if(in_array($cat,[1020,1040, 1060, 1080]))
				{
					$arg[($cat==1020 ? "apartment_type" : "home_type")] = "1";
					if((int)$pr->beds>0) $arg["rooms"] = (string)$pr->beds;
					if((int)$pr->cars>0) $arg["garage_spaces"] = (string)$pr->cars;
					if(!$cat==1080 && (float)$pr->cond>0) $arg["condominio"] = (string)$pr->cond;
					if($cat==1080) $arg["rent_type"] = "4";
				}
				if((float)$pr->area>0) $arg["size"] = (int)$pr->area;
			}
	
			$pic = [];
			if(!($pr->pic0 == "img/cover.png"))
			{
				$path  = $info->domain_name."/0/".$c."/";
				$pic[] = $path.$pr->pic0;
				$tmp   = get_files("0/".$c);
				$count = 20;
				if(is_array($tmp) && sizeof($tmp)) foreach($tmp as $t) if(--$count>0 && !($t == $pr->pic0)) $pic[] = $path.$t;
			}

			$olxObj = [];
			$olxObj["access_token"] = sess($info->project_name."_olx_access_token");
			$olxObj["ad_list"] = [ 0 => [
				"price"			=> (int)$val
				, "id"			=> substr($c,0,19)
				, "operation"	=> "insert"
				, "category"	=> (int)$cat
				, "subject"		=> $hed
				, "body" 		=> $des
				, "phone" 		=> (int)substr($pho,strlen($pho)-11)
				, "type"		=> "s"
				, "zipcode"		=> $zip
			]];
						
			if(sizeof($arg)) $olxObj["ad_list"][0]["params"] = (array)$arg;
			if(sizeof($pic)) $olxObj["ad_list"][0]["images"] = (array)$pic;

	    	$olx = new AuthClient(Accounts::OLX);
	    	$olx->publish($olxObj,"PUT");	

	    	/*
	    	echo "/*";
		    print_r($olxObj);
		    echo(PHP_EOL."n/n/n/n".PHP_EOL);
		    print_r($olx->result());
		    echo "* /";
			*/

			//log(@json_encode($olx->result()));

		    if(isset($olx->result()->statusCode))
		    {
		    	$t = $olx->result()->token;
		    	qin("UPDATE Properties SET solx='$t' WHERE code='$c'");
		    	return $olx->resultMessage();
		    }
		    else return $olx->result();
		}
		else return atoo($errors);
	}
	else return atoo([ "error" => "Propriedade não encontrada" ]);
}

function olx_remove($c=null)
{
	if(!sess(conf("project_name")."_olx_access_token")) return atoo([ "error" => "É preciso efetuar a autenticação junto à OLX" ]);
	$olxObj = [];
	$olxObj["access_token"] = sess(conf("project_name")."_olx_access_token");
	$olxObj["ad_list"] = [ 0 => [
		"id"			=> substr($c,0,19)
		, "operation"	=> "delete"
	]];
	
	$olx = new AuthClient(Accounts::OLX);
	$olx->publish($olxObj,"PUT");	

	//log(@json_encode($olx->result()));

    if(isset($olx->result()->statusCode))
    {
    	$t = $olx->result()->token;
    	qin("UPDATE Properties SET solx='' WHERE code='$c'");
    	return atoo(["result"=>$t]);//$olx->resultMessage();
    }
    else return $olx->result();
}

function olx_check_status($c)
{
	if(sess(conf("project_name")."_olx_access_token"))
	$t = (new AuthClient(Accounts::OLX))->verifyStatus($c,sess(conf("project_name")."_olx_access_token"));
	else return 0;
	//print_r($t);

	//log(@json_encode($t));

	if(isset($t->autoupload_status))
	{
		$r = "none";
		$c = substr($c,0,19);
		if(isset($t->ads)) $ad = $t->ads->$c;

		//print_r($ad);
		
		if($t->autoupload_status == "done")
		{
			if(isset($ad->status)){
				if($ad->status=="error"){
					if($ad->message->messages=='LIMIT_EXEEDED') qin("update Properties set solx='' where code='".$c."'");
					if(isset($ad->message)){ return atoo([$ad->status=>$ad->message->messages]); }
				}
			}
			$r = "Publicado";
			//print_r($ad);

			if($ad->operation == "delete")
			{
				qin("UPDATE Properties SET solx='' WHERE code='$c'");
				$r = "Removido";
			}
			return atoo(["status"=>$r, "url"=>(isset($ad->url)?$ad->url:"")]);
		}
		return atoo(["status"=>$ad->status ]);
	}
	return atoo(["status"=>"Não encontrado"]);
}


switch(post("mode"))
{
	case("auth"): echo json_encode(olx_authenticate());															break;
	case("hshk"): echo json_encode(olx_handshake());															break;
	case("info"): echo json_encode(olx_get_info());																break;
	case("publ"): if(post("code")) echo json_encode(olx_publish(post("code")));									break;
	case("remv"): if(post("code")) echo json_encode(olx_remove(post("code")));									break;
	case("stts"): if(post("code")) echo json_encode(olx_check_status(post("code")));							break;
	default: echo json_encode(atoo([ "error" => "Ação não suportada pela Classe AuthClient(Accounts::OLX)" ])); break;
}