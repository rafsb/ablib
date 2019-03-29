<?php
if($this -> allow_access()) header('Access-Control-Allow-Origin: ' . $this -> allow_access()); // OLY FOR PUBLIC API USE
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: text/html; charset=UTF-8',true);?>
<!DOCTYPE html>
<html lang='pt-BR'>
    <head>
        <meta http-equiv='Content-Type' content='text/html;charset=UTF-8'/>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name='description' content=''/>
        <meta name='author' content='<?=App::devel()?>'/>
        <?php
        IO::scripts(SCAN);
        IO::js(SCAN);
        IO::stylesheets(SCAN);
        IO::css(SCAN);?>
        <title><?=App::project_name()?></title>
    </head>
    <body 
        class='-view -content-center -confortaa'>
        <?php
        $load = $this -> view() ? $this -> view() : IO::root() . "/webroot/views/" . strtolower(get_called_class()) . ".php";
        if(is_file($load)) include $load;?>
    </body>
</html>