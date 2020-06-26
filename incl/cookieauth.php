<?php

unset($xuser);

$cookie=explode('z',$_COOKIE[$xcookie_uidhash[0]]);
if(!isset($cookie[1])){redirect('account.php');die();}

$id=(int)$cookie[0];

$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_users WHERE id='.$id);
if(neutral_num_rows($res)<1){redirect('account.php');die();}

$xuser=neutral_fetch_array($res);

$hash=hash('sha256',$xuser['id'].$settings['cookie_salt']);
if($hash!=$cookie[1]){redirect('account.php');die();}

if(!isset($xuser)){redirect('account.php');die();}

?>