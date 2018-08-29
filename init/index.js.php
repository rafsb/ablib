<?php
namespace abox;
set_include_path("lib");
require_once 'user.php';
require_once 'project/main.php';
$user = user();
$root = root();
$conf = conf();?>
(function(){
    ab.USER = "<?=$user?>";
    ab.load('header.php');
})();