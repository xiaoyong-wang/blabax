<?php

if(!isset($_POST['msg']) || !isset($_POST['mtoken']) || !isset($_POST['status']) || !isset($_POST['ohash']) || !isset($_POST['dbid']) || !isset($_POST['ampm']) || !isset($_POST['ajnm']) || !isset($_POST['zone']) || !isset($_POST['tousername'])){print 'error 001';die();}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

$mtoken=explode('|',$_POST['mtoken']);
if(!isset($mtoken[3])){print 'error 002';die();}
$m_hash=hash('sha256',$mtoken[0].$mtoken[1].$mtoken[2].$settings['random_salt']);

if($m_hash!=$mtoken[3]){print 'error 003';die();}

$userid=(int)$mtoken[0];
$username=base64_decode($mtoken[1]);
$username=neutral_escape($username,64,'str');
$usergroup=(int)$mtoken[2];

$status=(int)$_POST['status'];
if($status<1 || $status>5){print 'error 004';die();}


// check ban
if($userid>1){
$res=neutral_query('SELECT timestamp FROM '.$dbss['prfx']."_ban WHERE (ipaddr='$ipaddr' OR id=$userid) AND $timestamp < timestamp");
if(neutral_num_rows($res)>0){print 'idehic';die();}}

// exit
if($_POST['msg']=='1'){neutral_query('DELETE FROM '.$dbss['prfx'].'_online WHERE id='.$userid);
if(isset($msg_leave) && isset($sys_xuser)){
neutral_query('INSERT INTO '.$dbss['prfx']."_messages VALUES(NULL,999999,0,0,'$sys_xuser',0,'','$username $msg_leave',0,0,$timestamp)");
} die('{}');}

// ---

$usronline=array();
$rmessages=array();
$dhash='';

$dbid=(int)$_POST['dbid'];
$zone=(int)$_POST['zone'];
$ampm=(int)$_POST['ampm'];
$ajnm=(int)$_POST['ajnm'];

function tformat($t,$z,$a){
if($a<1){return '';}
if($a>1){$f='H:i:s';}else{$f='h:i:s';}
return gmdate($f,$t+$z*60);}

function rolldice($d){
$t=''; $s=0; $x=explode('d',$d);
if(count($x)==2){
$a=(int)$x[0];$b=(int)$x[1];
if($a<1 || $a>99){$a=2;}
if($b<1 || $b>99){$b=6;}
}else{$a=2;$b=6;}
$r=$a.'d'.$b;
for($i=0;$i<$a;$i++){$m=mt_rand(1,$b); $s+=$m; $t.=' '.$m;}
$v=round($s/$a); $t=$r.':'.$t.' (sum:'.$s.' avg:'.$v.')';
return $t;}


// send message

if($_POST['msg']!='0'){
$message=@json_decode($_POST['msg'],true);

if(!is_array($message) || !isset($message['to']) || !isset($message['color']) || !isset($message['text']) || !isset($message['room'])){print 'error 006';die();}

if(isset($message['attach'])){$attach=(int)$message['attach'];}else{$attach=0;}

if($attach==4){$message['text']=rolldice($message['text']);}

$roomid=(int)$message['room'];
$touser=(int)$message['to']; if($touser>0){$roomid=0;}
$tousername=neutral_escape($_POST['tousername'],64,'str');
$line=neutral_escape($message['text'],2048,'str');
$color=(int)$message['color'];

neutral_query('INSERT INTO '.$dbss['prfx']."_messages VALUES(NULL,$roomid,$userid,$usergroup,'$username',$touser,'$tousername','$line',$color,$attach,$timestamp)");
}


// online list

$pp=$ping_period*$ping_multpl;

if(isset($msg_leave) && isset($sys_xuser)){ $usr2tout=array();
$res=neutral_query('SELECT name FROM '.$dbss['prfx']."_online WHERE $timestamp-timestamp>$pp");	
if(neutral_num_rows($res)>0){
while($row=neutral_fetch_array($res)){$usr2tout[]=$row['name'];}
$usr2tout=implode(',',$usr2tout);
neutral_query('INSERT INTO '.$dbss['prfx']."_messages VALUES(NULL,999999,0,0,'$sys_xuser',0,'','$usr2tout $msg_leave',0,0,$timestamp)");
}}

neutral_query('DELETE FROM '.$dbss['prfx'].'_online WHERE id='.$userid." OR $timestamp-timestamp>$pp");
if(!in_array($userid,$stealth_users)){neutral_query('INSERT INTO '.$dbss['prfx']."_online VALUES($userid,'$username',$usergroup,'$ipaddr',$timestamp,$status)");}
$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_online ORDER BY id');


while($row=neutral_fetch_array($res)){
$dhash.=$row['id'].$row['status'];
$user=array($row['id'],$row['ugroup'],$row['name'],$row['status']);
$usronline[$row['id']]=$user;
}

$dhash=md5($dhash);
if($dhash==$_POST['ohash']){$usronline=false;}


// fetch messages

if($dbid<1){$res=neutral_query('SELECT MAX(id) FROM '.$dbss['prfx'].'_messages');
$res=neutral_fetch_array($res);$dbid=$res[0];}

else{

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_messages WHERE id>$dbid AND (roomid>0 OR (touserid=$userid OR userid=$userid)) ORDER BY id ASC");

while($row=neutral_fetch_array($res)){

$sngmsg=array();
$sngmsg['name']=$row['username'];
$sngmsg['color']=$row['color'];
$sngmsg['text']=$row['line'];
$sngmsg['fromid']=$row['userid'];
$sngmsg['pbox']=$row['userid'];
if($row['touserid']!=$userid){$sngmsg['pbox']=$row['touserid'];}
$sngmsg['attach']=$row['attach'];
$sngmsg['group']=$row['usergroup'];
$sngmsg['room']=$row['roomid'];
$sngmsg['time']=tformat($row['timestamp'],$zone,$ampm);
$dbid=$row['id'];
$rmessages[]=$sngmsg;
}
}

// ---

$debug=false;$ajnm+=1;

$arr2return=array($usronline,$rmessages,$dhash,$dbid,$ajnm,$debug);
$arr2return=json_encode($arr2return,JSON_FORCE_OBJECT);

print $arr2return;

?>