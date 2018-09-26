<?php 
namespace abox;
require "../core.php";?>
<div class="view zero hbd tct" style="z-index:4000">
	<div class="stretch"></div>
	<!CLOSE>
	<icon class="fb spd cur abs -lt100vw -tp0 -closed fwhite -map@spd;" style="margin-left:-6rem" onclick="ab.apply(function(){
		var page = ab.UUID?AB_PAGE_CHROOT:AB_PAGE_WELCOME, cont = ab.loadPool.length();
		$('#{{ # }}')[0].unload(true);
		do{
			var tmp = ab.loadPool.get(-1);
			//console.log(tmp+' is it already running?');
			if(tmp!=='{{#}}'&&$('#'+tmp).length){ page=false; setTimeout(function(tmp){ $('#'+tmp)[0].appear(80); },80,tmp); break; }
			else ab.loadPool.pop();
		}while(ab.loadPool.get(-1));
		if(page) setTimeout(function(page){ ab.load(page); },80,page);
	})">&#x4d;</icon>
	<script>
		{{%#}} = {
			init : function(){
				$("#{{#}} .stretch:first").css({
					"background-image":<?=in("pic0")?(strpos("url",in("pic0"))>=0?"'".in("pic0")."'":"'url(\'".in("pici")."\')'"):""?>.replace("mini_","")
					, "background-size":"cover"
					, "background-repeat":"no-repeat"
					, "background-position":"center center"
				});
				this.container.appear();
				ab.loading(AB_OFF);
			}
		}
	</script>
</div>