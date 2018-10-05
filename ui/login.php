<?php
namespace abox;
require "../modal.php";

$o = new Modal("{{ # }}","ENTRAR",AB_DIALOG,AB_NOSSCOPE);

$o->bstart();?>
    <form id="{{ # }}" class='wf dys tct' action='javascript:void(0)' onsubmit="ab.apply(function(){
        ab.signin($('#{{ # }} input:eq(0)').val(), $('#{{ # }} input:eq(1)').val(), $('#{{ # }} .-switched').attr('data-state').int());
    })">
        <div class="zbar tct"><div class="w60"><input type='text' autocomplete="current-user" class="tct alphak2"/><label>Usuário</label></div></div>
        <div class="zbar tct"><div class="w60"><input type='password' autocomplete="current-password" class="tct alphak2 -hash"/><label>Senha</label></div></div>
        <nav class='zbar tct'> <div class="w60"><img class='-ht1.25rem rt -switch' data-state='1'/><div class="rt hrs">manter-me conectado:</div></div></nav>
        <div class="sbar tct"><input type='submit' class='bt spd bvariant fwhite' value='ENTRAR'/></div>
        <div class='zbar tct'><a class='link hmg' onclick="ab.load('../lib/ui/pswd_reset.php')">Esqueci minha senha</a></div>
    </form>
    <script>
    	{{%#}} = {
    		init:function(){
                ab.organize();
                ab.loading(AB_OUT)
    			this.container.appear();
    		}
    	}
    </script>
<?php
$o->bend(true);