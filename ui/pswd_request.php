<?php
namespace abox;

include_once "../user.php";
include_once "../modal.php";

$o = new Modal("{{#}}","Favor inserir sua senha:",AB_DIALOG,AB_NOSSCOPE);
$o->bstart();?>
    <div class="stretch" style="margin-bottom:2rem;overflow:visible">
        <form class="w80 sys" action="javascript:void(0)">
            <input 
                type="password" 
                onkeypress="ab.apply(function(e){
                if(e.v&&(e.k.keyCode||e.k.which)==13){ {{%#}}.submit(e.v.hash()); }
            },{v:this.value,k:window.event})"/>
            <label>Usuário logado: <b class="fwine"><?=strtoupper(username())?></b></label>
        </form>
        <div class='abs -tp100% -lt0 bar'>
            <div
                class="lt tct bdisabled fdark wh hyp cur" 
                onclick="document.getElementById('{{#}}').desappear(120,true);">
                CANCELAR
            </div>
            <div
                class="lt bspan fwhite tct wh hyp cur"
                onclick="{{%#}}.submit($('#{{#}} [type=password]').val().hash())">
                PROSSEGUIR
            </div>
        </div>
        <script>
            {{%#}} = {
                submit : function(t){
                    var 
                    x = document.getElementById('<?=in("from")?>');
                    if(t){
                        ab.exec('../lib/fn/get_pswd_pass.php',{pswd:t},function(d){
                            if(d.status==200){
                                //console.log('ab.apply('+x.dataset.pass.replace(/::this/g,'document.getElementById(\'<?=in("from")?>\')'+',"'+d+'")'));
                                if(d&&x.dataset.pass) eval('ab.apply('+x.dataset.pass.replace(/::this/g,'document.getElementById(\'<?=in("from")?>\')')+',"'+d.data+'")');
                                else ab.error('Senha inexistente, não confere ou insuficiente...');
                            }
                        });
                    }else ab.error('Por favor digite uma senha!');
                    document.getElementById('{{#}}').desappear(120,true);
                }
            };
            setTimeout(function(){ $("#{{#}} [type=password]").focus(); }, 200 );
        </script>
    </div>
<?php
$o->bend(AB_PRINT);