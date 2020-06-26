<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect();

// check if username available on register
if(isset($_POST['check_name'])){
$check_name=abc123($_POST['check_name'],'');
$res=neutral_query('SELECT name FROM '.$dbss['prfx']."_users WHERE name='$check_name'");
if(neutral_num_rows($res)>0){print 1;}else{print 0;}die();}

$settings=get_settings(); $options=get_options(); get_language(); 

// check ip ban
// $res=neutral_query('SELECT * FROM '.$dbss['prfx']."_ban WHERE ipaddr='$ipaddr' AND $timestamp < timestamp");
// if(neutral_num_rows($res)>0){
// $info_timeout=0; $info_line=$lang['info_ban'];
// require_once('templates/info.pxtm'); die();}

// --- VARS --- //

$lang_divs=array();

foreach ($lang_list as $key => $value){$ccode=substr($value[0],0,2);
$lang_divs[]='<div class="svg_f_'.$value[0].'" title="'.$value[1].'" onclick="gourl(\'index.php?lang='.$key.'\')"><span class="x_bcolor_z">'.$ccode.'</span></div>'; }
sort($lang_divs); $lang_divs=implode("\n",$lang_divs);

unset($dbuser);
$onehourback=$timestamp-3600;

setcookie($xcookie_uidhash[0],'',time()+$xcookie_uidhash[1],'/');

// _bflog tokens: 1 = guest, 2 = register, 3 = wrong password, 4 = wrong recovery email/answer


// --- LOGIN --- //

// !!! POST[user] & POST[password] -> check user with a password
if( isset($_POST['username']) && isset($_POST['password']) && length(abc123($_POST['username'],''))>2 && length($_POST['password'])>2 ){

// --- X attempts per hour...
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_bflog WHERE token=3 AND ipaddr='$ipaddr' AND timestamp>$onehourback");
if(neutral_num_rows($res)>=$settings['wrongperhour']){
$info_timeout=0; $info_line=$lang['error_attmpts'];
require_once('templates/info.pxtm'); die();}

$postname=abc123($_POST['username'],'');
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE name='$postname'");

if(neutral_num_rows($res)<1){
$info_url='account.php'; $info_timeout=0; $info_line=$lang['no_upmatch'];
require_once('templates/info.pxtm'); die();}

$dbuser=neutral_fetch_array($res);

$hash=hash('sha256',trim($_POST['password']).$dbuser['salt']); 
if($dbuser['password']!=$hash){
neutral_query('INSERT INTO '.$dbss['prfx']."_bflog VALUES(0,'$ipaddr',3,$timestamp)");
$info_url='account.php?passrequired_uname='.$postname; $info_timeout=0; $info_line=$lang['no_upmatch'];
require_once('templates/info.pxtm'); die();}

if($dbuser['quarantine']>$timestamp){
$info_url='account.php?passrequired_uname='.$postname; $info_timeout=0; $info_line=$lang['account_wait'];
require_once('templates/info.pxtm'); die();}

$cookie=hash('sha256',$dbuser['id'].$settings['cookie_salt']);
$cookie=$dbuser['id'].'z'.$cookie;
setcookie($xcookie_uidhash[0],$cookie,time()+$xcookie_uidhash[1],'/');
redirect('blabax.php'); die();}


// ??? POST[user] & no password, set dbuser array
if( isset($_POST['username']) && length(abc123($_POST['username'],''))>2 ){
$postname=abc123($_POST['username'],'');
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE name='$postname'");
if(neutral_num_rows($res)>0){$dbuser=neutral_fetch_array($res);}}


// !!! if dbuser but the account is password protected ask for a password and die
if(isset($dbuser) && length($dbuser['password'])>0){
$info_url='account.php?passrequired_uname='.$postname; $info_timeout=0; $info_line=$lang['pass_required'];
require_once('templates/info.pxtm'); die();}


// ??? check IP <-> GUEST match
$user_ip_match=0; if(isset($dbuser)){
$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_iplog WHERE name=\''.$dbuser['name'].'\' AND ipaddr=\''.$ipaddr.'\'');
if(neutral_num_rows($res)>0){$user_ip_match=1;}}


// !!! if dbuser & no password protected but IP doesn't match display error and die
if(isset($dbuser) && ($user_ip_match<1 && $ipaddr!=$dbuser['ipaddr'])){
$info_url='account.php'; $info_timeout=0; $info_line=$lang['guest_error'];
require_once('templates/info.pxtm'); die();}


// !!! if dbuser & no password protected and IP match -> set cookie, redirect and die
if(isset($dbuser) && ($user_ip_match>0 || $ipaddr==$dbuser['ipaddr'])){
$cookie=hash('sha256',$dbuser['id'].$settings['cookie_salt']);
$cookie=$dbuser['id'].'z'.$cookie;
setcookie($xcookie_uidhash[0],$cookie,time()+$xcookie_uidhash[1],'/');
redirect('blabax.php'); die();}


// !!! if escaped POST[user] survived so far insert new user, set cookie, redirect and die
if(isset($postname) && length($postname)<17 && $settings['allow_guest']!='0'){

// --- X guests per hour...
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_bflog WHERE token=1 AND ipaddr='$ipaddr' AND timestamp>$onehourback");
if(neutral_num_rows($res)>=$settings['userperhour']){
$info_timeout=0; $info_line=$lang['error_reguser'];
require_once('templates/info.pxtm'); die();}

if($settings['utf8_run']!='0' && abcmixed($postname)){
$info_url='account.php'; $info_timeout=0; $info_line=htmlspecialchars($settings['utf8_msg']);
require_once('templates/info.pxtm'); die();}

$randstr=rand_str(20); $sgroup=(int)$settings['group_g'];
neutral_query('INSERT INTO '.$dbss['prfx']."_users VALUES(NULL,$sgroup,'$postname','','','$randstr','$ipaddr','','',$timestamp,0)");
neutral_query('INSERT INTO '.$dbss['prfx']."_bflog VALUES(0,'$ipaddr',1,$timestamp)");

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE name='$postname'");
if(neutral_num_rows($res)>0){$dbuser=neutral_fetch_array($res);
$cookie=hash('sha256',$dbuser['id'].$settings['cookie_salt']);
$cookie=$dbuser['id'].'z'.$cookie;
setcookie($xcookie_uidhash[0],$cookie,time()+$xcookie_uidhash[1],'/');
redirect('blabax.php'); die();}}


// --- REGISTER --- //

if(isset($_POST['regname']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['question'])  && isset($_POST['answer']) && $settings['allow_reg']!='0'){

// --- X users per hour...
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_bflog WHERE token=2 AND ipaddr='$ipaddr' AND timestamp>$onehourback");
if(neutral_num_rows($res)>=$settings['userperhour']){
$info_timeout=0; $info_line=$lang['error_reguser'];
require_once('templates/info.pxtm'); die();}

$regsalt=rand_str(20);
$regname=abc123($_POST['regname'],'');
$regpass=hash('sha256',trim($_POST['password']).$regsalt);
$regmail=neutral_escape($_POST['email'],64,'str');
$reguery=neutral_escape($_POST['question'],128,'str');
$regansr=hash('sha256',strtolower(trim($_POST['answer'])).$regsalt);

if(length($regname)<3 || length($regname)>16  || length($_POST['password'])<3){
$info_url='account.php?q=register'; $info_timeout=0; $info_line=$lang['wrong_usr_ps'];
require_once('templates/info.pxtm'); die();}

if(length($regmail)<7 || !stristr($regmail,'@') || !stristr($regmail,'.') || stristr($regmail,' ')){
$info_url='account.php?q=register'; $info_timeout=0; $info_line=$lang['wrong_email'];
require_once('templates/info.pxtm'); die();}

if(length($reguery)<1 || length($_POST['answer'])<1){
$info_url='account.php?q=register'; $info_timeout=0; $info_line=$lang['wrong_quas'];
require_once('templates/info.pxtm'); die();}

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE name='$regname' OR email='$regmail'");

if(neutral_num_rows($res)>0){
$info_url='account.php?q=register'; $info_timeout=0; $info_line=$lang['wrong_usr_em'];
require_once('templates/info.pxtm'); die();}

$quarantine=(int)$settings['reglog_delay'];
if($quarantine>0){$quarantine+=$timestamp;}
$sgroup=(int)$settings['group_r'];

if($settings['utf8_run']!='0' && abcmixed($regname)){
$info_url='account.php'; $info_timeout=0; $info_line=htmlspecialchars($settings['utf8_msg']);
require_once('templates/info.pxtm'); die();}

neutral_query('INSERT INTO '.$dbss['prfx']."_users VALUES(NULL,$sgroup,'$regname','$regpass','$regmail','$regsalt','$ipaddr','$reguery','$regansr',$timestamp,$quarantine)");
neutral_query('INSERT INTO '.$dbss['prfx']."_bflog VALUES(0,'$ipaddr',2,$timestamp)");

$info_url='account.php?passrequired_uname='.$regname; $info_timeout=0; $info_line=$lang['account_ok'];
if($quarantine>$timestamp){$info_line=$info_line.' '.$lang['account_wait'];}
require_once('templates/info.pxtm'); die();

}


// --- PASSWORD --- //

// if email exists ask recovery question
if(isset($_POST['lostpassemail'])){

// --- X attempts per hour...
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_bflog WHERE token=4 AND ipaddr='$ipaddr' AND timestamp>$onehourback");
if(neutral_num_rows($res)>=$settings['wrongperhour']){
$info_timeout=0; $info_line=$lang['error_attmpts'];
require_once('templates/info.pxtm'); die();}

$lostpassemail=neutral_escape($_POST['lostpassemail'],64,'str');
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE email='$lostpassemail'");

if(neutral_num_rows($res)<1){
neutral_query('INSERT INTO '.$dbss['prfx']."_bflog VALUES(0,'$ipaddr',4,$timestamp)");
$info_url='account.php?q=password'; $info_timeout=0; $info_line=$lang['wrong_recmail'];
require_once('templates/info.pxtm'); die();}

$lpuser=neutral_fetch_array($res);
$lpuserid=$lpuser['id'];
$lpuserqu=strip_tags($lpuser['question']);

require_once('templates/password_recovery_question.pxtm');die();}


// if recovery answer ok - reset password
if(isset($_POST['lostpassanswer']) && isset($_POST['lostpassid'])){

$lostpassid=(int)$_POST['lostpassid'];

// --- X attempts per hour...
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_bflog WHERE token=4 AND (ipaddr='$ipaddr' OR id=$lostpassid) AND timestamp>$onehourback");
if(neutral_num_rows($res)>=$settings['wrongperhour']){
$info_timeout=0; $info_line=$lang['error_attmpts'];
require_once('templates/info.pxtm'); die();}

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE id=$lostpassid");

if(neutral_num_rows($res)<1){die();}

$lpuser=neutral_fetch_array($res);
$postansr=hash('sha256',strtolower(trim($_POST['lostpassanswer'])).$lpuser['salt']);

if($postansr!=$lpuser['answer']){
neutral_query('INSERT INTO '.$dbss['prfx']."_bflog VALUES($lostpassid,'$ipaddr',4,$timestamp)");
$info_url='account.php?q=password'; $info_timeout=0; $info_line=$lang['wrong_recansw'];
require_once('templates/info.pxtm'); die();}

$pass=strtolower(rand_str(5));
$hash=hash('sha256',$pass.$lpuser['salt']); 

neutral_query('UPDATE '.$dbss['prfx']."_users SET password='$hash' WHERE id=$lostpassid");

$info_url='account.php?passrequired_uname'; $info_timeout=0; $info_line=$lang['pass_newpass'].'<b>'.$pass.'</b>';
require_once('templates/info.pxtm'); die();

}


// ---------------- //


if(isset($_GET['q']) && $_GET['q']=='register' && $settings['allow_reg']!='0'){require_once('templates/register.pxtm');die();}
if(isset($_GET['q']) && $_GET['q']=='password'){require_once('templates/password_recovery_email.pxtm');die();}
require_once('templates/login.pxtm');

?>