<?php
namespace abox;
require("../modal.php");

$advs = in("advs");
$setup = in('setu');

$o = new Modal("{{#}}",conf("enterprise"),AB_DIALOG);
$o->bstart();?>
    <p class='lt justify tlt syp dxp -map[=fxs]'><?=$advs?></p>
    <button class="wf tct bspan fwhite hyp dts" onclick="$('#{{#}} .-closed').click()">OK</button>
    <script>
    	{{%#}} = {
	    	init:function(){
	    		$("#{{#}} button:eq(0)").focus();
                $(this.container).draggable();
	    		this.container.appear();
                ab.organize();
	    	}
    	}
    </script>
<?php
$o->bend();
if(isset($setup["background"])) $o->paint($setup["background"]);
if(isset($setup["color"])) 		$o->font($setup["color"]);
if(isset($setup["classes"])) 	$o->set($setup["classes"],AB_REPLACE);
$o->print();