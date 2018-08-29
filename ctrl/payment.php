
<?php
/*
 * PagSeguro® API Integration
 * Receives a some args and return string line of permission code
 *
 * args = item : { 
 *		code : "CODE_OF_PRODUCT_0019038"
 *		, qtty : "1"
 *		, name : "NAME OF PRODUCT"
 *		, val0 : "100.00" 
 *		, dsct : "0.95" // for 5% discount x(1-0.05)
 * }
 */
require("../php/std.php");

$data["token"] = conn(pstokn);
$data["email"] = conn(psmail);
$data["currency"] = "BRL";

$i = 1;
$items = post(item);
foreach($items as $x)
{
	$data["itemId".$i] 			= $x -> code;
	$data["itemQuantity".$i] 	= $x -> qtty;
	$data["itemDescription".$i] = $x -> name;
	$data["itemAmount".$i] 		= ((float)$x -> val0 * (float)$x -> qtty) * (float)$x -> dsct;
	++$i;
}

$data = http_build_query($data);

$curl = curl_init(conn(psurl0));

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

$xml= curl_exec($curl);
curl_close($curl);
$xml = simplexml_load_string($xml);

echo $xml -> code;
?>