<?php
/*
oldm
ctnr
*/
require("../std.php");?>

<div id="catc" class="stretch alphak8 tct fn">
    <div class="ab-dialog tct bwindow ft-back" style="border:1px solid whitesmoke">
        <div class="bar">
            <img class="hpd lt ab-h14px" src="img/mini_logok.png">
            <div class="hpd rt" ab-close></div>
        </div>

        <input type="text" class="spd dys bwhite w%80 fdark fpx12 hr tct" placeholder="<?=post("oldm")?>">

        <div class='bar'>
            <button class="hmg hpd bgreen fsmoke rt nbd hr" ab-icon="&#x52"> SIM</button>
            <button class="hmg hpd bwine fsmoke lt nbd hr" onclick="$('#cfrm').parent().getAway()" ab-icon="&#x51"> NÃO</button>
        </div>
    </div>
</div>

<script>
    $("#catc button:eq(0)").click(function(){
        var t = $("#catc [type='text']:eq(0)").val();
        if(t && t!=="<?=post("oldm")?>"){
            $("#<?=abox\post("ctnr")?>").attr("ab-response",t);
            eval($("#<?=abox\post("ctnr")?>").attr("data-callback"));
        }
    });

    $("#catc button:eq(1)").click(function(){ $('#catc').parent().getAway(); });
    
    ab_tooltips();
    
    setTimeout(function(){ $("#catc [type='text']:eq(0)").focus(); },40);

</script>
