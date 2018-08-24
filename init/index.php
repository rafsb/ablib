<?php
namespace abox;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: text/html; charset=UTF-8",true);
set_include_path("lib");
require_once 'user.php';
require_once 'project/main.php';
$user = user();
$root = root();
if(is_file($root."var/.PROJ")) conf("project_name",str_replace(array("\r","\n"),"",file_get_contents($root."var/.PROJ")));
if(!$user) if(cook(USER)) $user = sess(USER,cook(USER));
sess(HTTP,\file_get_contents('var/.HTTP'));
$cf=conf();?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
        <meta name="description" content=""/>
        <meta name="author" content="Aboxsoft DevTeam"/>
        <script src="lib/jquery.min.js"></script>
        <script src="lib/jqui.min.js"></script>
        <script src="lib/std.js"></script>
        <script src="project/main.js"></script>
        <link href="lib/fonts.css" rel="stylesheet"/>
        <link href="lib/std.css" rel="stylesheet"/>
        <link href="project/main.css" rel="stylesheet"/>
        <title><?=$cf->project_name?></title>
    </head>
    <body class="stretch zero ft-ubuntu -ft11px"></body>
    <style type="text/css"><?php include "index.css.php";?></style>
    <script>
        <?php
        include "index.js.php";
        if(get('advise')){?> ab.advise("<?=get('advise')?>"); <?php };
        if(get('notify')){?> ab.notify("<?=get('notify')?>"); <?php };?>  
    </script>
</html>
