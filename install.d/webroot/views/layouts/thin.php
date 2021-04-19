<?php
if($this->allow_access()) header('Access-Control-Allow-Origin: ' . $this->allow_access()); // OLY FOR PUBLIC API USE
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: text/html; charset=UTF-8',true);?>
<!DOCTYPE html>
<html lang='pt-BR'>
    <head>
        <!-- Basic Tags -->
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, minimal-ui, viewport-fit=cover" />
        
        <!-- Custom Tags -->
        <meta name='description' content=''/>
        <meta name='author' content='<?=App::devel()?>'/>
        
        <title><?=App::project_name()?></title>

    </head>
    <body>

        <?php
        $load = $this->view() ? $this->view() : IO::root() . "/webroot/views/" . strtolower(get_called_class()) . ".php";
        if(is_file($load)) include $load;
        else if($this->result()) echo $this->result();
        else if(DEBUG) Debug::show();?>
        
    </body>
</html>
