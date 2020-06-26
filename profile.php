<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings(); $options=get_options(); get_language();

// check user: begin
if(!isset($_COOKIE[$xcookie_uidhash[0]])){redirect('account.php');die();}

$cookie=explode('z',$_COOKIE[$xcookie_uidhash[0]]);
if(!isset($cookie[1])){redirect('account.php');die();}

$id=(int)$cookie[0];

$hash=hash('sha256',$id.$settings['cookie_salt']);
if($hash!=$cookie[1]){redirect('account.php');die();}

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE id=$id");
if(neutral_num_rows($res)<1){redirect('account.php');die();}
// check user: end

$dbuser=neutral_fetch_array($res);

// --- PROFILE: guest ---

if(isset($_POST['password']) && isset($_POST['retype']) && isset($_POST['email']) && isset($_POST['question']) && isset($_POST['answer'])){

if(length($dbuser['question'])>0 || length($dbuser['answer'])>0 || length($dbuser['email'])>0 || length($dbuser['password'])>0){
redirect('account.php');die();}

$usrpass=hash('sha256',trim($_POST['password']).$dbuser['salt']);
$usrmail=neutral_escape($_POST['email'],64,'str');
$usrquery=neutral_escape($_POST['question'],128,'str');
$usranswr=hash('sha256',strtolower(trim($_POST['answer'])).$dbuser['salt']);

if(length($_POST['password'])<3 || trim($_POST['password'])!=trim($_POST['retype'])){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['pass_short'];
require_once('templates/info.pxtm'); die();}

if(length($usrmail)<7 || !stristr($usrmail,'@') || !stristr($usrmail,'.') || stristr($usrmail,' ')){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_email'];
require_once('templates/info.pxtm'); die();}

if(length($usrquery)<1 || length($_POST['answer'])<1){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_quas'];
require_once('templates/info.pxtm'); die();}

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE email='$usrmail'");
if(neutral_num_rows($res)>0){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_promail'];
require_once('templates/info.pxtm'); die();}

$ugroup=(int)$dbuser['ugroup'];
if($dbuser['ugroup']==$settings['group_g']){$ugroup=(int)$settings['group_r'];}

neutral_query('UPDATE '.$dbss['prfx']."_users SET ugroup=$ugroup, password='$usrpass', email='$usrmail', question='$usrquery', answer='$usranswr' WHERE id=$id");
redirect('blabax.php');die();}

// --- PROFILE: user ---

if(isset($_POST['newpass']) && isset($_POST['oldpass']) && isset($_POST['retype']) && isset($_POST['email']) && isset($_POST['answer'])){

if(length($dbuser['question'])<1 || length($dbuser['answer'])<1 || length($dbuser['email'])<1 || length($dbuser['password'])<1){
redirect('account.php');die();}

if(length(trim($_POST['oldpass']))>2 && length(trim($_POST['newpass']))>2){
$oldpass=hash('sha256',trim($_POST['oldpass']).$dbuser['salt']);

if($oldpass!=$dbuser['password']){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_pass'];
require_once('templates/info.pxtm'); die();}

$newpass=hash('sha256',trim($_POST['newpass']).$dbuser['salt']);
neutral_query('UPDATE '.$dbss['prfx']."_users SET password='$newpass' WHERE id=$id");}


if(trim($_POST['email']) != $dbuser['email']){

if(hash('sha256',strtolower(trim($_POST['answer'])).$dbuser['salt'])!=$dbuser['answer']){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_recansw'];
require_once('templates/info.pxtm'); die();}

$usrmail=neutral_escape($_POST['email'],64,'str');

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_users WHERE email='$usrmail'");
if(neutral_num_rows($res)>0){
$info_url='blabax.php'; $info_timeout=0; $info_line=$lang['wrong_promail'];
require_once('templates/info.pxtm'); die();}

neutral_query('UPDATE '.$dbss['prfx']."_users SET email='$usrmail' WHERE id=$id");
}}

redirect('blabax.php');

?>