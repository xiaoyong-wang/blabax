<?php

if(!isset($_GET['q'])){die('{"error":"widget not specified"}');}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

switch($_GET['q']){
case 'online_usr' : if($settings['w_onlu']>0){$widget_file = 'widgets/online_usr.php';} else{$dsbl='{"error":"widget disabled"}';} break;
case 'online_avt' : if($settings['w_onla']>0){$widget_file = 'widgets/online_avt.php';} else{$dsbl='{"error - widget disabled":""}';} break;
case 'statistics' : if($settings['w_stat']>0){$widget_file = 'widgets/statistics.php';} else{$dsbl='{"error":"widget disabled"}';} break;
case 'usr_top_10' : if($settings['w_tten']>0){$widget_file = 'widgets/usr_top_10.php';} else{$dsbl='{"0":{"0":"error","1":"","2":"widget disabled","3":"100"}}';} break;
case 'usr_latest' : if($settings['w_last']>0){$widget_file = 'widgets/usr_latest.php';} else{$dsbl='{"0":{"0":"widget disabled","1":"","2":""}}';} break;
default :  $dsbl='{"error":"no such widget"}'; break;
}

$sec2cache = (int)$settings['w_cache'];

$ts = gmdate("D, d M Y H:i:s", time() + $sec2cache) . " GMT";
header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: max-age=$sec2cache");
header('Content-Type: application/json');
if($settings['w_cross']>0){header('Access-Control-Allow-Origin: *');}

// ---

$json = '{"error":"unspecified"}';
$urli = str_replace('widget.php', '', "//{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");

if(isset($widget_file)){require_once($widget_file);} 
else{$json=$dsbl;}

print $json;

?>
