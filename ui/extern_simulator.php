<?php
require("../std.php");?>
<div id="extr" class="stretch alphak8 tct">
    <div class="ab-dialog tct bwindow ft-back" style="border:1px solid whitesmoke">
        <div class="bar">
            <img class='hpd lt ab-h14px' src='img/mini_logok.png'>
            <div class='hpd rt' onclick="$('#extr').parent().getAway()" ab-close></div>
        </div>
        <div class="ab-w80% sys own-font fs">Emulador de API externa (PagSeguro)</div>
        <div class="ab-w100% trt">
            <button class="hmg hpd fs fsmoke bgreen" ab-icon="&#x52;"> CONFIRMAR PAGTO</button>
            <button class="hmg hpd bwine fsmoke lt fs" onclick="$('#extr').parent().getAway()" ab-icon="&#x51"> NÃO</button>
        </div>
    </div>
</div>
<script>
    $('#extr button:eq(0)').click(function(){
        // DEBIT
        var itrn = new ab_Data();
        itrn.attr("code","<?=abox\get_hash()?>");
        itrn.attr("name","<?=abox\post("who0")."|".abox\post("code")."|".abox\post("mode")."|".abox\post("val0")?>|IN")
        itrn.attr("val0",parseFloat(<?=abox\post("val0")?>));
        itrn.attr("acct","<?=abox\user()?>");
        itrn.attr("paym","<?=abox\get_hash()?>");
        itrn.attr("stts",3);
        ab_exec("trs/control.php",{ mode:"new0",type:0,tran:itrn.obj[0] });

        // CREDIT
        var otrn = new ab_Data();
        otrn.attr("code","<?=abox\get_hash()?>");
        otrn.attr("name","<?=abox\post("who0")."|".abox\post("code")."|".abox\post("mode")."|".abox\post("val0")?>|OUT")
        otrn.attr("val0",parseFloat(<?=abox\post("val0")?>));
        otrn.attr("acct","<?=abox\user()?>");
        otrn.attr("paym","<?=abox\get_hash()?>");
        otrn.attr("stts",3);
        otrn.attr("type",1);
        ab_exec("trs/control.php",{ mode:"new0",type:1,tran:otrn.obj[0] });

        // SUM ADS CREDITS ON EVENT
        var a = "";
        <?php
        if(abox\post("who0")=="evnt")
        {?>
            a = "evnt";
            var evnt = new ab_Data("Events","*","code='<?=abox\post("code")?>'");
            evnt.attr("ads0",parseFloat(evnt.attr("ads0"))+<?=(float)abox\post("val0")?>);
            ab_exec("evt/control.php",{ mode:"edit",evnt:evnt.obj[0] });
        <?php
        }
        else if(abox\post("who0")=="attr")
        {?>
            a = "attr";
            var tckt = new ab_Data();
            tckt.attr("code","<?=abox\get_hash()?>");
            tckt.attr("attr","<?=abox\post("code")?>");
            tckt.attr("ltra",otrn.attr("code"));
            tckt.attr("stts",1);
            tckt.attr("ownr","<?=abox\user()?>");
            ab_exec("evt/atr/tkt/control.php",{ mode:"new0", tckt:tckt.obj[0] });

            var attr = new ab_Data("Attractions","*","code='<?=abox\post("code")?>'");
            attr.attr("tcnt",parseInt(attr.attr("tcnt"))+1);
            ab_exec("evt/atr/control.php",{ mode:"edit",attr:attr.obj[0] });

        <?php
        }?>
        
        setTimeout(function(){
            ab_advise("Este simulador vai aprovar a compra imediatamente, para fins demonstrativos, mas o periodo de aprovação deverá levar algunmas horas ou dias!!<br><br>");
        },400);
    });
    setTimeout(function(){ $("#extr button:eq(0)").focus(); },50);
</script>
