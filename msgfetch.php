<?php

if(!isset($_GET['mtoken'])){print 'error 001';die();}

if(isset($_GET['room'])){$room=(int)$_GET['room'];}else{$room=0;}
if(isset($_GET['tuid'])){$tuid=(int)$_GET['tuid'];}else{$tuid=0;}
if(isset($_GET['tpoi'])){$tpoi=(int)$_GET['tpoi'];}else{$tpoi=0;}
if(isset($_GET['zone'])){$zone=(int)$_GET['zone'];}else{$zone=0;}
if(isset($_GET['ampm'])){$ampm=(int)$_GET['ampm'];}else{$ampm=2;}


if(($room<1 && $tuid<1) || ($room>0 && $tuid>0) || $tpoi<1){print 'error 002';die();}

require_once('config.php');
require_once('incl/main.php');

function tformat($t,$z,$a){
if($a<1){return '';}
if($a>1){$f='H:i:s';}else{$f='h:i:s';}
return gmdate($f,$t+$z*60);}

neutral_dbconnect(); $settings=get_settings();

if($settings['msg2db']<1){print 'error 003';die();}

$mtoken=explode('|',$_GET['mtoken']);
if(!isset($mtoken[3])){print 'error 004';die();}
$m_hash=hash('sha256',$mtoken[0].$mtoken[1].$mtoken[2].$settings['random_salt']);

if($m_hash!=$mtoken[3]){print 'error 005';die();}

$userid=(int)$mtoken[0];

if($userid>1){
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_ban WHERE (ipaddr='$ipaddr' OR id=$userid) AND $timestamp < timestamp");
if(neutral_num_rows($res)>0){print '[]';die();}}

// ---

if($room>0){$query='SELECT * FROM '.$dbss['prfx']."_messages WHERE touserid=0 AND roomid=$room AND timestamp<$tpoi ORDER BY id DESC LIMIT 20 OFFSET 0";}
if($tuid>0){$query='SELECT * FROM '.$dbss['prfx']."_messages WHERE ((touserid=$tuid AND userid=$userid) OR (touserid=$userid AND userid=$tuid)) AND timestamp<$tpoi ORDER BY id DESC LIMIT 20 OFFSET 0";}

$res=neutral_query($query);
$messages=array();

while($row=neutral_fetch_array($res)){

$msg=array();
$msg['id']=$row['id'];
$msg['color']=$row['color'];
$msg['uid']=$row['userid'];
$msg['name']=$row['username'];
$msg['time']=tformat($row['timestamp'],$zone,$ampm);
$msg['text']=$row['line'];
$msg['group']=$row['usergroup'];

$messages[]=$msg;

}

krsort($messages);
$jsonmsg=json_encode($messages);

//sleep(1);

print $jsonmsg;

?>