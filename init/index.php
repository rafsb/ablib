<?php
namespace abox;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: text/html; charset=UTF-8",true);
set_include_path("lib");
require_once 'user.php';
require_once 'project/main.php';
sess(HTTP,@\file_get_contents('var/.HTTP'));
$user = user();
$root = root();
if(is_file($root."var/.PROJ")) conf("project_name",str_replace(array("\r","\n"),"",file_get_contents($root."var/.PROJ")));
$sc=schema();
if(!$user) if(cook(USER)) $user = sess(USER,cook(USER));?>
<!DOCTYPE xhtml>
<html lang="pt-BR">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content=""/>
        <meta name="author" content="Alphabox Dev Team"/>
        <script src="lib/jquery.min.js"></script>
        <script src="lib/jqui.min.js"></script>
        <script src="lib/std.js"></script>
        <script src="project/main.js"></script>
        <link href="lib/fonts.css" rel="stylesheet"/>
        <link href="lib/std.css" rel="stylesheet"/>
        <link href="project/main.css" rel="stylesheet"/>
        <title><?=conf("project_name")?></title>
    </head>
    <body class="view zero bmain fmain"></body>
    <style type="text/css">
        .bmain    { background: <?=$sc->bmain    ?>; }
        .bmodal   { background: <?=$sc->bmodal   ?>; }
        .bvariant { background: <?=$sc->variant  ?>; }
        .bspan    { background: <?=$sc->span     ?>; }
        .bdisabled{ background: <?=$sc->disabled ?>; }
        .fmain    { color     : <?=$sc->fmain    ?>; }
        .fmodal   { color     : <?=$sc->fmodal   ?>; }
        .fvariant { color     : <?=$sc->variant  ?>; }
        .fspan    { color     : <?=$sc->span     ?>; }
        .fdisabled{ color     : <?=$sc->disabled ?>; }
    </style>
    <script>
        (function(){
            ab.USER = "<?=$user?>";
            ab.schema = <?=json_encode($sc);?>;
            ab.organize();
            <?php
            if(get("mailcheck") &&
                qio("SELECT * FROM Users WHERE code='".get("mailcheck")."'") &&
                qin("UPDATE Users SET mchk=1 WHERE code='".get("mailcheck")."'")
            ){?> ab.success("E-mail confirmado...."); <?php };
            if(get('advise')){?> ab.advise("<?=get('advise')?>"); <?php }
            if(get('notify')){?> ab.notify("<?=get('notify')?>"); <?php }?>
        })();
    </script>
</html>