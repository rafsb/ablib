<?php
namespace abox;
require "../user.php";
require "../modal.php";
$user = user();
$o = new Modal("{{#}}","RESETAR SENHA",AB_DIALOG,AB_NOSSCOPE);
$o->bstart();?>
    <form class="w80" action="javascript:void(0)" onsubmit="{{$#}}.submit(this)">
        <p>Olá, para consguirmos te localizar em nossa base de dados, precisamos que você nos dê uma dic de quem é.</p>
        <p>
            Por favor insira no campo abaixo alguma informação relevante, como:
            <ul class="lt tlt">
                <li>seu usuário</li>
                <li>e-mail cadastrado na <b>CI<sup>®</sup></b></li>
                <li>ou seu documento, repeitando o formato padrão</li>
            </ul>
        </p>
        <div class="sbar"></div>
        <div class="zbar tct">
            <div class="w80"><input type="text" class="tct -required"><label>Dica</label></div>
        </div>
        <div class="dbar tct">
            <input type='submit' class='hpd fwhite bblue' value="ENVIAR SOLICITAÇÃO"/>
        </div>
    </form>
    <script>
        {{%#}} = {
            data: ab.data({conf:'users/current',load:AB_LOAD}),
            submit : function(f=null,fn='<?=in("func")?>'){
                if(!f) return -1;
                if(ab.checkouts(f)){
                    var
                    hint = this.container.getElementsByTagName('input')[0].value;
                    ab.exec("../lib/ctrl/pswd_reset.php",{hint:hint},function(d){ 
                        if(d.data.stts.int()) if(fn) eval(fn)(d.data);
                        else ab.error(d.data.error);
                        return 0;
                    });
                }
                else ab.error("Favor preencher o campo indicado!");
                ab.loading(false);
            },
            init:function(){
                ab.organize();
                this.container.appear();
                setTimeout(function(c){ c.getElementsByTagName("input")[0].focus(); },180,this.container);
            }
        };
    </script>
<?php
$o->bend();
$o->appendButton("<div class='hpd fblue fbd -tooltip' data-message='A informação dada por você aqui tem que ser exatamente igual à cadastrada em nossa base de dados, portanto, se optar por informar um documento, se atente aos padrões:<br>'>?</div>")