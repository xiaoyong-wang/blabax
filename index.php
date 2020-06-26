<?php

if(@filesize('config.php')<5){header('location: install/index.php');die();}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings(); $options=get_options();


// --- SETTINGS ---

// set lang from login
if(isset($_GET['lang'])){
$options[0]=(int)$_GET['lang'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
redirect('account.php');die();}

// ---

if(isset($_GET['acceptannounce'])){
setcookie($xcookie_message[0],1,time()+$xcookie_message[1],'/');
redirect('account.php');die();}

// ---

if(isset($_POST['lang'])){
$options[0]=(int)$_POST['lang'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
print 'reload';die();}

// ---

if(isset($_POST['ampm'])){
$options[1]=(int)$_POST['ampm'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
die();}

// ---

if(isset($_POST['sound'])){
$options[2]=(int)$_POST['sound'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
die();}

// ---

if(isset($_POST['color'])){
$options[3]=(int)$_POST['color'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
die();}

// ---

if(isset($_POST['pmreg'])){
$options[4]=(int)$_POST['pmreg'];
$cookie=(implode('z',$options));
setcookie($xcookie_options[0],$cookie,time()+$xcookie_options[1],'/');
die();}

// ---

if(isset($_GET['room'])){$room=(int)$_GET['room'];if($room>0){setcookie('room',$room,time()+3600,'/');}}
if(isset($_GET['mobileapp'])){$mpp=(int)$_GET['mobileapp'];setcookie('mobileapp',$mpp,time()+$xcookie_options[1],'/');}

// ---

if(!isset($_COOKIE[$xcookie_uidhash[0]])){redirect('account.php');die();}

redirect('blabax.php');

?>