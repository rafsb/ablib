<?php
namespace abox;
require("../modal.php");

$advs = in("advs");
$setup = in('setu');

$o = new Modal("{{#}}",conf("enterprise"),AB_DIALOG);
$o->bstart();?>
    <p class='lt justify tlt syp dxp' data-map=":fxs"><?=$advs?></p>
    <button class="wf tct bspan fwhite hyp dts" onclick="$('#{{#}} .-closed').click()">OK</button>
    <script>$("#{{#}} button:eq(0)").focus();</script>
<?php
$o->bend();
if(isset($setup["background"])) $o->paint($setup["background"]);
if(isset($setup["color"])) 		$o->font($setup["color"]);
if(isset($setup["classes"])) 	$o->set($setup["classes"],AB_REPLACE);
$o->print();