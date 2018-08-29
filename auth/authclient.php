<?php
namespace abox;

class iAccounts
{
	//abstract function authUrl();
	//abstract function tokenArgs($c);
	//abstract function resultMessage($s);
	public function tokenUrl(){ return $this->token_uri; }
	public function importUrl(){ return $this->import_uri; }
	public function verifyUrl(){ return $this->verify_uri; }
}

class Olx extends iAccounts
{
	public function __construct()
	{
		$this->state = conf("project_name")."_olx_access_token";
		$this->kind            = false; // false=imoveis, true=veiculos
		$this->response_type   = "code";
		$this->scope           = "basic_user_info autoupload";
		$this->redirect_uri    = "http://www.aboxsoft.me/_services/olx";
		$this->info_uri        = "https://apps.olx.com.br/oauth_api/basic_user_info";
		$this->import_uri      = "https://apps.olx.com.br/autoupload/import";
		$this->verify_uri      = "https://apps.olx.com.br/autoupload/import";
		$this->token_uri       = "https://auth.olx.com.br/oauth/token";
		$this->auth_uri        = "https://auth.olx.com.br/oauth";
		$this->client_id       = conf("olx_dev_code",null,false);
		$this->client_secret   = conf("olx_dev_pswd",null,false);
		$this->return_messages = [
			"0"=>"Sucesso na importação, agora é só aguardar a OLX publicar"
			, "-1" => "Ops! Houve um erro inesperado"
			, "-2" => "Requisição bloqueada por excesso de requisições"
			, "-3" => "Não há anúncios para importar nesta requisição"
			, "-4" => "Há um erro na validação do anúncio, veja se todos os campos estão devidamente preenchidos"
			, "-5" => "O serviço de importação da OLX está fora do ar, tente novemente mais tarde"
			, "-6" => "Infelizmente você não possui a conta PREMIUM da OLX para executar esta importação"
			, "-7" => "O limite de anúncios da sua conta na OLX foi excedido"
		];
		$this->error_list    = [
			"ERROR_CATEGORY_INVALID"            => "Categoria inválida"
			, "ERROR_REGION_MISSING"            => "Falta informar a região do anúncio"
			, "ERROR_ZIPCODE_INVALID"           => "CEP informado é inválido"
			, "ERROR_TYPE_INVALID"              => "Tipo de imóvel inválido"
			, "ERROR_PHONE_INVALID"             => "Número de telefone informado e inválido"
			, "ERROR_PHONE_TOO_SHORT"           => "O número de telefone é muito curto"
			, "ERROR_PHONE_TOO_LONG"            => "O número de telefone é muito longo"
			, "ERROR_BODY_TOO_SHORT"            => "Descrição do anúncio muito curta"
			, "ERROR_BODY_TOO_LONG"             => "Descrição do anúncio muito longa"
			, "ERROR_SUBJECT_TOO_SHORT"         => "Título muito curto"
			, "ERROR_SUBJECT_TOO_LONG"          => "Título muito longo"
			, "ERROR_ROOMS_INVALID"             => "Número de quartos inválido"
			, "ERROR_ROOMS_MISSING"             => "Ńão foi informado o número de quartos"
			, "ERROR_CATEGORY_SUBTYPE_MISSING"  => "Tipo de imovel inválido"
			, "ERROR_SIZE_INVALID"              => "Tamanho inválido"
			, "ERROR_UNKNOWN_APARTMENT_TYPE"    => "Tipo de apartamento inválido"
			, "ERROR_NO_SUCH_PARAMETER"         => "Há informações que não poderão ser mostradas"
			, "ERROR_OPERATION_INVALID"         => "Tipo de operação inválida"
			, "ERROR_FUEL_INVALID"              => "Tipo de combustível inválido"
			, "ERROR_FUEL_MISSING"              => "Não foi informado o tipo de combustível"
			, "ERROR_CARTYPE_INVALID"           => "Tipo de carro inválido"
			, "ERROR_DOORS_MISSING"             => "Tipo de portas inválido"
			, "ERROR_MILEAGE_INVALID"           => "Kilometragem incorreta"
			, "ERROR_REGDATE_INVALID"           => "Ano inválido"
			, "ERROR_UNKNOWN_CAR_FEATURES"      => "Parâmetro adicional de veículo desconhecido"
		];
	}

	public function authUrl()
	{
		$a = atoh([
			"client_id"		 => $this->client_id
			, "redirect_uri" => $this->redirect_uri
			, "scope"		 => $this->scope
			, "response_type"=> $this->response_type
			, "state"		 => $this->state
		]);
		return $this->auth_uri."/?".$a;
	}

	public function tokenArgs($c=null)
	{
		//echo $c;
		if($c)
		return [
			"code"           => trim($c)
			, "grant_type"   => "authorization_code"
			, "client_id"    => trim($this->client_id)
			, "client_secret"=> trim($this->client_secret)
			, "redirect_uri" => trim($this->redirect_uri)
		];
		return ["error"=>"code missing"];
	}

	public function resultMessage($s)
	{  
		if(isset($s->statusCode)) return atoo([ "message" => $this->return_messages[$s->statusCode] ]);
		return atoo([ "error" => "Erro desconhecido" ]);
	}
}


class Instagram extends iAccounts
{
	public function __construct()
	{
		$this->scope           = "basic";
		$this->redirect_uri    = "http://www.aboxsoft.me/_services/instagram";
		$this->info_uri        = "";
		$this->import_uri      = "";
		$this->token_uri       = "";
		$this->auth_uri        = "https://api.instagram.com/oauth/authorize";
		$this->client_id       = conf("instagram_dev_code",null,false);
		$this->client_secret   = conf("instagram_dev_pswd",null,false);
		$this->state           = conf("project_name")."_instagram_access_token";
		$this->return_messages = [];
		$this->error_list      = [];
	}

	public function authUrl()
	{
		$a = atoh([
			"client_id"		 => $this->client_id
			, "redirect_uri" => $this->redirect_uri
			, "scope"		 => $this->scope
			, "response_type"=> $this->response_type
			, "state"		 => $this->state
		]);
		return $this->auth_uri."/?".$a;
	}

	public function tokenArgs($c=null)
	{
		//echo $c;
		if($c)
		return [
			"code"           => trim($c)
			, "grant_type"   => "authorization_code"
			, "client_id"    => trim($this->client_id)
			, "client_secret"=> trim($this->client_secret)
			, "redirect_uri" => trim($this->redirect_uri)
		];
		return ["error"=>"code missing"];
	}

	public function resultMessage($s){ return 0;}
	
}


class Google extends iAccounts
{
	public function __construct()
	{
		$this->response_type   = "code";
		$this->scope           = "basic";
		$this->redirect_uri    = "http://www.aboxsoft.me/_services/google";
		$this->info_uri        = "https://apps.olx.com.br/oauth_api/basic_user_info";
		$this->import_uri      = "https://apps.olx.com.br/autoupload/import";
		$this->token_uri       = "https://auth.olx.com.br/oauth/token";
		$this->auth_uri        = "https://api.instagram.com/oauth/authorize";
		$this->client_id       = conf("instagram_dev_code",null,false);
		$this->client_secret   = conf("instagram_dev_pswd",null,false);
		$this->return_messages = [];
		$this->error_list      = [];
	}

	public function authUrl()
	{
		$a = atoh([
			"client_id"		 => $this->client_id
			, "redirect_uri" => $this->redirect_uri
			, "scope"		 => $this->scope
			, "response_type"=> $this->response_type
			, "state"		 => $this->state
		]);
		return $this->auth_uri."/?".$a;
	}

	public function tokenUrl()
	{
		return $this->token_uri;
	}

	public function importUrl(){ return (string)$this->import_uri; }

	public function verifyUrl(){ return (string)$this->import_uri; }

	public function tokenArgs($c=null)
	{
		//echo $c;
		if($c)
		return [
			"code"           => trim($c)
			, "grant_type"   => "authorization_code"
			, "client_id"    => trim($this->client_id)
			, "client_secret"=> trim($this->client_secret)
			, "redirect_uri" => trim($this->redirect_uri)
		];
		return ["error"=>"code missing"];
	}

	public function resultMessage($s){ return 0;}
	
}

class Facebbok{}

abstract class Accounts
{
	const OLX       = 1;
	const GOOGLE    = 2;
	const FACEBOOK  = 3;
	const INSTAGRAM = 4;
}

class AuthClient
{
	private $cfg = null;
	private $mod = null;

	public function __construct($c=null)
	{
		switch($c)
		{
			case Accounts::OLX      : $this->cfg = new Olx();       break;
			case Accounts::GOOGLE   : $this->cfg = new Google();    break;
			case Accounts::FACEBOOK : $this->cfg = new Facebook();  break;
			case Accounts::INSTAGRAM: $this->cfg = new Instagram(); break;
		}
		$this->mod = $c;
	}

	public function authUrl(){ return $this->cfg->authUrl(); }

	public function exec($u=null,$a=null,$o=[])
	{
		$this->res = "";
		$c = \curl_init();
		\curl_setopt_array($c,
			[
				CURLOPT_URL => $u
				, CURLOPT_POSTFIELDS => $a
				, CURLOPT_RETURNTRANSFER => true
				, CURLOPT_header => false
			] + $o
		);
		$t = \curl_exec($c);
		\curl_close($c);

		//print_r($a);

		$this->res = json_decode($t);

		return ($this->result()?$this->result():["error"=>"No result"]);
	}

	public function sign($c=null){ return $this->exec($this->cfg->tokenUrl(),$this->cfg->tokenArgs($c)); }

	public function publish($a,$m="POST")
	{
		$c = \curl_init();

		\curl_setopt_array(
			$c
			, [
				CURLOPT_URL              => $this->cfg->importUrl()
				, CURLOPT_CUSTOMREQUEST  => $m
				, CURLOPT_POSTFIELDS     => json_encode($a)
				, CURLOPT_RETURNTRANSFER => true
				, CURLOPT_header         => false
			]
		);

		$t = \curl_exec($c);

		$this->res = json_decode($t);

		\curl_close($c);

		return $this->result();
	}

	public function info($t) { if($t) return $this->exec($this->cfg->info_uri,json_encode($t)); }

	public function result(){ return $this->res; }

	public function resultMessage(){ return $this->cfg->resultMessage($this->result()); }

	public function verifyStatus($c,$t)
	{
		//print_r([$this->cfg->verifyUrl(),["access_token"=>$t]]);
		switch($this->mod)
		{
			case(Accounts::OLX):
				$c = qcell("Properties","solx","code='$c'");
				return $this->exec(
					$this->cfg->import_uri."/".$c
					, json_encode(["access_token"=>sess(conf("project_name")."_olx_access_token")])
				);
			break;
			
			default: return ["error"=>"mode_not_recognized"];
		}
		
	}
}