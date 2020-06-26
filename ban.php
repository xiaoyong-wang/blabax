<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

if(isset($_COOKIE[$xcookie_uidhash[0]])){ require_once 'incl/cookieauth.php'; }else{ print 'error 001';die(); }
if(!isset($xuser['id']) || $xuser['id']!=1){ print 'error 002';die();}

// ---

$id=0; $name=''; $ip=''; $mod=0;
if(isset($_POST['mod'])){$mod=(int)$_POST['mod'];}

if($mod>0){$ban_period=(int)$settings['ban_period'];}
else{$ban_period=$ping_period*2;}

$ban_period+=$timestamp;

if(isset($_POST['id'])){$id=(int)$_POST['id'];}
if($id<2){die();}

$res=neutral_query('SELECT name,ipaddr FROM '.$dbss['prfx']."_iplog WHERE id=$id ORDER BY timestamp DESC LIMIT 1 OFFSET 0");
if(neutral_num_rows($res)>0){
$dbuser=neutral_fetch_array($res); $name=$dbuser['name'];

if($mod==2 && $dbuser['ipaddr']!=$ipaddr){$ip=$dbuser['ipaddr'];}}
if($xuser['id']==$id){die();}

neutral_query('INSERT INTO '.$dbss['prfx']."_ban VALUES($id,'$name','$ip',$ban_period,3,1,'')");
neutral_query('DELETE FROM '.$dbss['prfx'].'_online WHERE id='.$id);

?>