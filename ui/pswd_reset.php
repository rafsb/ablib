<?php
namespace abox;
require "../user.php";
$user = user();
if(aval()){
    require "../modal.php";
    $o = new Modal("{{#}}","RESETAR SENHA",AB_DIALOG,AB_NOSSCOPE);
    $o->bstart();?>
        <form class="stretch" action="javascript:void(0)" onsubmit="ab.apply(function(t){
            var
            elements = $('#{{#}} [type=\'password\']'),
            el_count = x.length,
            execution = function(){
                ab.call('../ctrl/pswd_check.php',{user:'<?=qcell('User','user')?>',pswd:elements.eq(x-1).val()},function(d){
                    if(!d.error && d.data.int()==1){
                        ab.tmpfs.get(AB_PAGE_PSWD_RESET).data.run(function(d){console.log(d)},null,'users/edit');
                    }else ab.error();
                });
            };
            <?php
            if(!aval(AB_ADMIN)){?>
                if(!elements.eq(x-1).val() || !(elements.eq(y-2).val()===elements.eq(x-1).val())){
                    ab.error('Problemas com as senhas inseridas!');
                }else execution();
            <?php
            }else{?>
                execution();
            <?php
            }?>
        },this)">
            <?php        
            if(!userlevel(AB_ADMIN)){?>
                <div class="zbar tct">
                    <div class="w80"><input type="password" class="tct required"><label>Senha antiga</label></div>
                </div>
            <?php
            }?>
            <div class="zbar tct">
                <div class="w80"><input type="password" data-control="#!;pswd" class="tct required"><label>Nova senha</label></div>
            </div>
            <div class="zbar tct dbs">
                <div class="w80"><input type="password" class="tct required"><label>Nova senha novamente</label></div>
            </div>
            <script>
                ab.tmpfs.set({data: ab.data({conf:'users/current',load:AB_LOAD})},AB_PAGE_PSWD_RESET);
                setTimeout(function(){$("#{{#}} input:eq(0)").val("").focus();},180);
            </script>
        </form>
    <?php
    $o->bend(AB_PRINT);
}else echo "<nocontent><script>ab.working('Ops! Problemas com suas permissões...');$('nocontent').remove()</script></nocontent>";