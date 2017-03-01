<?php
// meter types
$electricMeterTypes = [4, 5, 7, 8]; // 4 must either be a different type of meter or units are different; perhaps this measures positive flow (eg. solar panel)
$gasMeterTypes      = [ 2, 9, 12];
$waterMeterTypes    = [11, 13];
// with gmt we see the correct EST time
date_default_timezone_set('GMT');
?>
