<?php
require("lib.php");
if(abox\post("notificationCode") && abox\post("notificationType"))
{
	$url = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/".abox\post("notificationCode")."?email=".abox\conf("psmail")."&token=".abox\conf("pstoken");
}
