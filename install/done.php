<?php 

if(!isset($_POST['user']) || !isset($_POST['pass']) || !isset($_POST['mail']) || !isset($_POST['ques']) || !isset($_POST['answ'])){die();}

require_once('../config.php');
require_once('lang_english.utf8');
require_once('../incl/mysqli_functions.php');

neutral_dbconnect();

function rand_str($l){
$l=(int)$l; if($l<5){$l=5;}
$str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$len=strlen($str); $randstr='';
if(function_exists('random_int')){
for($i=0;$i<$l;$i++){$randstr.=$str[random_int(0,$len-1)];}
return $randstr;}
if(function_exists('password_hash')){
$randstr=substr(password_hash(microtime(),PASSWORD_DEFAULT),10,60);
$randstr=preg_replace('/[^\da-z]/i','',$randstr.$randstr);
$randstr=substr($randstr,0,$l); return $randstr;}
$randstr=substr(substr(md5(microtime()),0,15).substr(base64_encode(sha1(time())),0,15).substr(sha1(microtime()),0,15).substr(base64_encode(md5(time())),0,15),0,$l);
return $randstr;}

function abc123($n,$s){
$n=trim($n);
$n=preg_replace('/[^\p{L}\p{N} ]/u',$s,$n);
$n=preg_replace('/([\s])\1+/',' ',$n);
return $n;}

function length($x){$l=strlen($x);return $l;}

function process_error($x){print $x;die();}

$timestamp=time();
$ipaddr=$_SERVER['REMOTE_ADDR'];
$regsalt=rand_str(20);
$regname=abc123($_POST['user'],'');
$regpass=hash('sha256',trim($_POST['pass']).$regsalt);
$regmail=neutral_escape($_POST['mail'],64,'str');
$reguery=neutral_escape($_POST['ques'],128,'str');
$regansr=hash('sha256',strtolower(trim($_POST['answ'])).$regsalt);

neutral_query('INSERT INTO '.$dbss['prfx']."_users VALUES(1,1,'$regname','$regpass','$regmail','$regsalt','$ipaddr','$reguery','$regansr',$timestamp,0)");

$csalt=neutral_query('SELECT value FROM '.$dbss['prfx']."_settings WHERE id='cookie_salt'");
$csalt=neutral_fetch_array($csalt); $csalt=$csalt[0];

$cookie=hash('sha256','1'.$csalt);
$cookie='1z'.$cookie;
setcookie($xcookie_uidhash[0],$cookie,time()+$xcookie_uidhash[1],'/');

 ?>

<!DOCTYPE html>
<html lang="en">

<head><title>...</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="style.css" />
</head>

<body class="x_global x_overal">
<div class="holder" style="margin-top:100px;font-weight:bold;text-align:center">
<hr />
<?php print $lang['inst_ok'];?>
<br /><br /><hr />
</div>
</body>
</html>