<?php
namespace abox;
require("../../includes.php");
if(aval()){?>
    <div id="pwcg" class="ab-dialog tct bdialog fdialog">
        <div class="ab-restore">RESETAR SENHA::<?=strtoupper(qcell("Users","user"))?></div> 
        <div class="ab-controlbox">
            <div class='ab-close'></div>
            <div class="ab-minimize"></div>
            <div class="bt hpd fwhite bgreen hrs save">Salvar</div>
        </div>
        <div class="stretch">
            <?php
            if(!userlevel(2)){?>
                <div class="zbar rel tct" action="javascript:void(0)">
                    <div><input type="password" class="tct required"><label>Senha antiga</label></div>
                </div>
            <?php
            }?>
            <div class="zbar tct">
                <div><input type="password" data-control="pwcg;pswd" class="tct required"><label>Nova senha</label></div>
            </div>
            <div class="zbar tct dbs">
                <div><input type="password" class="tct required"><label>Nova senha novamente</label></div>
            </div>
        </div>   
        <script>
            var pwcg = new ab_Data({
                controller    : "users/handler.php"
                , table       : "Users"
                , restrictions: "code='<?=user()?>'"
                , mode        : abox.Queries.UPDATE
            });
            pwcg.attr("pswd",null);

            $("#pwcg .save").click(function(){
                <?php
                if(!aval(2)){?>
                    if(!ab_intcall("../lib/ctrl/pswd_check.php", { user:pwcg.attr("user"), pswd:$("#pwcg [type='password']:eq(0)").val() })){
                        $("#pwcg [type='password']:eq(0)")
                            .css("border","2px dashed red")
                            .val("");
                        ab_notify("Ops! Senha antiga não confere...");
                        return -1;
                    }
                <?php
                }?>
                var x = $("#pwcg [type='password']").length;
                if(!($("#pwcg [type='password']:eq("+(x-2)+")").val()===$("#pwcg [type='password']:eq("+(x-1)+")").val())){
                    ab_notify("Os campos da nova senha não coincidem...");
                    return -2;
                }
                pwcg.query(function(d){
                    if(parseInt(d)){
                        ab_notify("Nova senha atribuída com sucesso!");
                        $("#pwcg .ab-close").click();
                    }else ab_error();
                });
            });

            setTimeout(function(){$("#pwcg input:eq(0)").val("").focus();},180);
            ab_controllers();
        </script>
    </div>
<?php
}else echo "<useless><script>ab_notify('Ops! Problemas com suas permissões...');$('useless').remove()</script></useless>";