<?php

if($this -> allow_access()) header("Access-Control-Allow-Origin: " . $this -> allow_access()); // OLY FOR PUBLIC API USE
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: text/html; charset=UTF-8",true);

ob_start();?>
	<!DOCTYPE html>
    <html lang="pt-BR">
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content=""/>
            <meta name="author" content="<?=App::devel()?>"/>
            <?php
            IO::scripts(SCAN);
            IO::stylesheets(SCAN);?>
            <title><?=App::project_name()?></title>
        </head>
        <body class="-view -content-center -ssans-light">
<?php
$this -> html_first_portion_ = trim(ob_get_clean());

ob_start();?>
		</body>
	</html>
<?php
$this -> html_last_portion_ = trim(ob_get_clean());

?>