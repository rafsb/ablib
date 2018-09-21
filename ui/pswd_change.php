<?php
namespace abox;
require "../user.php";
require "../modal.php";
if(aval()){
    $o = new Modal("{{#}}","RESETAR SENHA::".strtoupper(qcell("Users","user")),AB_DIALOG,AB_NOSSCOPE);
    $o->bstart();?>
        <div class="stretch">
            <?php
            if(!aval(AB_ADMIN)){?>
                <div class="zbar rel tct" action="javascript:void(0)">
                    <div><input type="password" class="tct -required -hash"><label>Senha antiga</label></div>
                </div>
            <?php
            }?>
            <div class="zbar tct">
                <div><input type="password" data-control="pwcg;pswd" class="tct -required -hash"><label>Nova senha</label></div>
            </div>
            <div class="zbar tct dbs">
                <div><input type="password" class="tct -required -hash"><label>Nova senha novamente</label></div>
            </div>
        </div>   
        <script>
            {{%#}} = {
                data : ab.data({conf:"users/seek",data:{code:"<?=user()?>"},load:true}),
                save : function(){
                    let
                    __save = function(){
                        var 
                        x = $("#{{#}} [type='password']").length,
                        p1 = $("#{{#}} [type='password']:eq("+(x-2)+")").val(),
                        p2 = $("#{{#}} [type='password']:eq("+(x-1)+")").val();
                        if(!p1||!p2||!(p1===p2)){
                            ab.error("Os campos da nova senha não coincidem...");
                            return -2;
                        }
                        {{%#!}}.value('pswd',p1);
                        {{%#!}}.run(function(d){
                            if(d.data.int()) ab.success("Nova senha atribuída com sucesso!");
                            else{
                                ab.error("Erro inesperado, tente novamente mais tarde...");
                                console.log(d);
                            }
                            $("#{{#}} .-closed").click();
                            ab.loading(AB_OUT);
                        },0,"users/edit");
                    },
                    stts = false;
                    <?php
                    if(!aval(AB_ADMIN)){?>
                        ab.call(
                            "../lib/ctrl/pswd_check.php", 
                            { user:this.data.value("user"), pswd:$("#{{#}} [type='password']:eq(0)").val() },
                            function(d){
                                if(d.data.int()){
                                    __save.apply();
                                }else{
                                    $("#{{#}} [type='password']:eq(0)")
                                        .css("border","2px dashed red")
                                        .val("");
                                    ab.error("Ops! Senha antiga não confere...");
                                    return -1;
                                }
                            }
                        );

                    <?php
                    }else echo 'stts=true;'?>
                    if(stts) __save.apply();
                },
                init : function(){
                    this.data.value('pswd','');
                    ab.organize();
                    this.container.appear();
                    setTimeout(function(){ $("#{{#}} input:first").focus(); },400);
                }
            }
        </script>
    <?php
    $o->bend();
    $o->appendButton("<div class='bt hpd zxs fwhite bblue' onclick='{{%#}}.save()'>SALVAR</div>");
    $o->print();
}else echo "<useless><script>ab_notify('Ops! Problemas com suas permissões...');$('useless').remove()</script></useless>";