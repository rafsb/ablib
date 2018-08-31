<?php require "../core.php";?>
<div id="{{#}}" class="view zero hbd tct -blur" style="background:inherit;z-index:4000">
	<div class="stretch"></div>
	<icon class="spd fb cur abs -lt100% -tp0 tshdw fred fbd fn" style="margin-left:-6rem;" onclick="ab.apply(function(){
		document.getElementById('{{#}}').desappear(40,true,ab);
		$('header').fadeIn();
	})">&#x4d;</icon>
	<script>
		$("#{{#}} .stretch:first").css({
			"background-image":"url('"+("<?=abox\in("pic0")?>".replace("mini_",""))+"')"
			, "background-size":"cover"
			, "background-repeat":"no-repeat"
			, "background-position":"center center"
		});
		$("header").fadeOut();
	</script>
</div>