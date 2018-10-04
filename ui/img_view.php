<?php 
namespace abox;
require "../core.php";?>
<div class="view zero hbd tct" style="z-index:4000">
	<div class="stretch"></div>
	<!CLOSE>
	<div class='abs zero'>
		<img class="abs zero spd xls  -wd10vw -op.6" src='src/img/logow.png'/>
		<!CLOSE>
		<icon class="fb spd cur abs tshdk -op.7 -lt100vw -tp0 -closed -map@spd;" style="margin-left:-6rem;color:red" onclick="ab.apply(function(){
			var page = ab.UUID?AB_PAGE_CHROOT:AB_PAGE_WELCOME, cont = ab.loadPool.length();
			do{
				var tmp = ab.loadPool.get(-1);
				if(tmp!=='{{#}}'&&$('#'+tmp).length){ page=false; setTimeout(function(tmp){ $('#'+tmp)[0].appear(80); },80,tmp); break; }
				else ab.loadPool.pop();
			}while(ab.loadPool.get(-1));
			$('#{{ # }}')[0].desappear(80,true);
			if(page) setTimeout(__load,80,page);
		})">&#xe051;</icon>
	</div>
	<script>
		{{%#}} = {
			init : function(){
				$("#{{#}} .stretch:first").css({
					"background-image":<?=in("pic0")?
						(strpos("url",in("pic0")) >= 0 ? "'".substr(in("pic0"),0,strlen(in("pic0"))-2)."?_=".uniqid()."\")'" : "'url(\'".in("pic0")."?_=".uniqid()."\')'"):""?>.replace("mini_","")
					, "background-size":"cover"
					, "background-repeat":"no-repeat"
					, "background-position":"center center"
				});
				ab.organize();
				this.container.appear();
				ab.loading(AB_OFF);
			}
		}
	</script>
</div>