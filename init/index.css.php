<?php
namespace abox;
include_once "lib/core.php";
$sc=schema();
echo "
        .bmain    { background: ".$sc->bmain    ."; } 
        .bheader  { background: ".$sc->bheader  ."; } 
        .bwindow  { background: ".$sc->bwindow  ."; } 
        .bdialog  { background: ".$sc->bdialog  ."; } 
        .bpanel   { background: ".$sc->bpanel   ."; } 
        .bvariant { background: ".$sc->variant  ."; } 
        .bspan    { background: ".$sc->span     ."; } 
        .bdisabled{ background: ".$sc->disabled ."; } 
        .fpanel   { color     : ".$sc->fpanel   ."; } 
        .fmain    { color     : ".$sc->fmain    ."; } 
        .fheader  { color     : ".$sc->fheader  ."; } 
        .fvariant { color     : ".$sc->variant  ."; } 
        .fspan    { color     : ".$sc->span     ."; } 
        .fdisabled{ color     : ".$sc->disabled ."; }
";