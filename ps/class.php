<?php
namespace abox;

class PS{
	private $mail;
	private $token_sandbox;
	private $token;
	private $return_url;
	/*
	//URL OFICIAL
	//COMENTE AS 4 LINHAS ABAIXO E DESCOMENTE AS URLS DA SANDBOX PARA REALIZAR TESTES
	private $url              = "https://ws.pagseguro.uol.com.br/v2/checkout/";
	private $url_redirect     = "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=";
	private $url_notificacao  = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/';
	private $url_transactions = 'https://ws.pagseguro.uol.com.br/v2/transactions/';

	//URL SANDBOX
	//DESCOMENTAR PARA REALIZAR TESTES
	*/
	private $url              = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/";
	private $redirect_url     = "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=";
	private $notification_url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/';
	private $transaction_url  = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/';
	
	private $mail_token = "";//NÃO MODIFICAR
	private $status_ = ["Pendente","Aguardando pagamento","Em análise","Pago","Disponível","Em disputa","Devolvida","Cancelada"];
		
	private function genRequest($item,$client,$callback){
		//Configurações
		$obj['email'] = $this->mail;
		//$obj['token'] = $this->token;
		$obj['token'] = $this->token_sandbox;
		$obj['currency'] = 'BRL';
		
		//Itens [code,desc,val0,qtty,wght]
		$obj['itemId1'] = $item['code'];
		$obj['itemDescription1'] = $item['desc'];
		$obj['itemAmount1'] = number_format($item['val0'],2,".","");
		$obj['itemQuantity1'] = (int)$item['qtty']?$item['qtty']:'1';
		$obj['itemWeight1'] = (float)$item['wght']?$item['wght']:'0';
		
		//Dados do pedido
		$obj['reference'] = $item['code'];
			
		//Dados do comprador
		$obj['senderName'] = $client['name'];
		$obj['senderAreaCode'] = substr($client['tel0'],0,2);
		$obj['senderPhone'] = substr($client['tel0'],2,strlen($client['tel0']));
		$obj['senderEmail'] = $client['mail'];

		$obj['shippingType'] = '3';
		$obj['shippingAddressStreet'] = $client['adrs'];
		$obj['shippingAddressNumber'] = $client['nadr'];
		$obj['shippingAddressComplement'] = " ";
		$obj['shippingAddressDistrict'] = $client['ngbr'];
		$obj['shippingAddressPostalCode'] = $client['zipc'];
		$obj['shippingAddressCity'] = $client['city'];
		$obj['shippingAddressState'] = strtoupper($client['uf00']);
		$obj['shippingAddressCountry'] = 'BRA';
		$obj['redirectURL'] = $callback;
		
		return http_build_query($obj);
	}
	
	public function checkout($item,$client,$callback){
		if(isset($item['code'])&&$item['code']) header('Location: '.$this->redirect_url.$item['code']);
		$data = $this->genRequest($item,$client,$callback);
		$curl = curl_init($this->url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$out= curl_exec($curl);
		if($out == 'Unauthorized'){
			echo '{"error":"Unauthorized"}';
			exit;
		}
		curl_close($curl);
		$out = simplexml_load_string($out);
		if(count($out->error) > 0){
			//Insira seu código de tratamento de erro, talvez seja útil enviar os códigos de erros.
			echo '{"error":"'.var_export($out->errors,true).'"}';
			exit;
		}
		header('Location: '.$this->redirect_url.$out->code);
	}
	
	//RECEBE UMA NOTIFICAÇÃO DO PAGSEGURO
	//RETORNA UM OBJETO CONTENDO OS DADOS DO PAGAMENTO
	public function notify($data){
		$url = $this->notification_url.$data['code'].$this->mail_token;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	
		$transaction = curl_exec($curl);
		if($transaction == 'Unauthorized'){
			echo '{"error":"Unauthorized"}';
		    exit;
		}
		curl_close($curl);
		$transaction = simplexml_load_string($transaction);
		return $transaction;
	}
	
	//Obtém o status de um pagamento com base no código do PagSeguro
	//Se o pagamento existir, retorna um código de 1 a 7
	//Se o pagamento não exitir, retorna NULL
	public function status($code){
		$url = $this->transaction_url.$code.$this->mail_token;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$transaction = curl_exec($curl);
		if($transaction=='Unauthorized') {
			echo '{"error":"Unauthorized"}';
			exit;
		}
		$transaction = simplexml_load_string($transaction);
		if(count($transaction->error) > 0) {
		   echo '{"error":"'.var_export($transaction->errors,true).'"}';
		}		

		if(isset($transaction->status)) return atoo(["code"=>$transaction->status,"text"=>$this->status_[$transaction->status]]);
		else return NULL;
	}
	
	//Obtém o status de um pagamento com base na referência
	//Se o pagamento existir, retorna um código de 1 a 7
	//Se o pagamento não exitir, retorna NULL
	public function check($code){
		$url = $this->transaction_url.$this->mail_token."&reference=".$code;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$transaction = curl_exec($curl);
		if($transaction == 'Unauthorized') {
			echo '{"error":"Unauthorized"}';
			exit;
		}
		$transaction = simplexml_load_string($transaction);
		if(count($transaction->error) > 0) {
			echo '{"error":"'.var_export($transaction->errors,true).'"}';
		}
		//print_r($transaction);
		if(isset($transaction->transactions->transaction->status)) return $transaction->transactions->transaction->status;
		else return NULL;
	}
	
	public function __construct($sandbox=true){
		$conf = json_decode(file_get_contents('ps.conf'));
		$this->mail          = $conf->mail;
		$this->token_sandbox = $conf->token_sandbox;
		$this->token 		 = $conf->token;
		$this->return_url	 = $conf->return_url;
		$this->mail_token    = "?email=".$conf->mail."&token=".($sandbox?$conf->$token_sandbox:$conf->token);
		$this->url .= $this->mail_token;
	}
}?>