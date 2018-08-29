<?php
namespace abox;
include_once('../queries.php');
$sqli = "select ".post("field")." from ".post("table").(post("restrictions") ? " where ".post("restrictions") : " ").(post("orderby") ? " order by ".post("orderby") : " ").(post("limit") && gettype(post("limit"))==="array" && sizeof(post("limit"))===2?" limit ".post("limit")[0].",".post("limit")[1]:"");
echo qjson($sqli);