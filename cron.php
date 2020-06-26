<?php

function time_to_run(){
$time=microtime();
$time=explode(' ',$time);
return $time[1]+$time[0];}

$stime=time_to_run();

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

header('Content-Type:text/plain');
if(!isset($_GET['q']) || $_GET['q']!=$settings['crn_k']){print 'Forbidden, exiting...';die();}

$dpoint_msg=(int)$settings['crn_m']; $dpoint_msg=$timestamp-($dpoint_msg*86400);
$dpoint_doo=(int)$settings['crn_d']; $dpoint_doo=$timestamp-($dpoint_doo*86400);
$dpoint_upl=(int)$settings['crn_u']; $dpoint_upl=$timestamp-($dpoint_upl*86400);
$delg=(int)$settings['crn_g']; $optd=(int)$settings['crn_o'];
$dbtables=array('ban','bflog','fmedia','groups','iplog','jbox','messages','nbox','news','online','paintings','rbox','rooms','scache','settings','social','stimoji','style','ufake','users','uxtra');

// ---

print 'Security key: OK'."\r\n";

// messages
neutral_query('DELETE FROM '.$dbss['prfx']."_messages WHERE timestamp<$dpoint_msg AND id>1");
$res=neutral_affected_rows(); print 'Messages deleted: '.$res."\r\n";

// guests
if($delg>0){ $gs=array();
$res=neutral_query('SELECT id FROM '.$dbss['prfx'].'_users WHERE length(password)<1 AND id NOT IN (SELECT id FROM '.$dbss['prfx'].'_ufake)');
while($row=neutral_fetch_array($res)){ $gs[]=$row['id']; }
if(count($gs)>0){$gs=implode(',',$gs);
neutral_query('DELETE FROM '.$dbss['prfx']."_uxtra WHERE id IN ($gs)");}
neutral_query('DELETE FROM '.$dbss['prfx'].'_users WHERE length(password)<1 AND id NOT IN (SELECT id FROM '.$dbss['prfx'].'_ufake)');
$res=neutral_affected_rows(); print 'Guests deleted: '.$res."\r\n";
neutral_query('DELETE FROM '.$dbss['prfx'].'_iplog');
$res=neutral_affected_rows(); print 'IP Log deleted: '.$res."\r\n";
}

else{print 'Guests deleted: NO'."\r\n";}

// optimize
if($optd>0){
foreach ($dbtables as &$value){neutral_query('OPTIMIZE TABLE '.$dbss['prfx'].'_'.$value);}
print 'DB optimized: YES'."\r\n";
} 

else{print 'DB optimized: NO'."\r\n";}

// end 
	
print '------------'."\r\n".'Done! ';

$etime=time_to_run();
$ttr=round($etime-$stime,3);
print $ttr.'s';

?>