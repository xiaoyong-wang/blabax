<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings(); $options=get_options(); get_language(); 

if(isset($_COOKIE[$xcookie_uidhash[0]])){ require_once 'incl/cookieauth.php'; } else{ redirect('account.php');die(); }

if(!isset($xuser['id']) || !isset($xuser['name'])){print '...';die();}
$xuser['bwsuser']=1; if(strlen($xuser['password'])>0){$xuser['bwsuser']=2;}

$uid=$xuser['id'];
$welcome=$settings['welcome_msg'];

// check id & ip ban
if($uid!=1){
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_ban WHERE (ipaddr='$ipaddr' OR id=$uid) AND $timestamp < timestamp");
if(neutral_num_rows($res)>0){
$info_timeout=0; $info_line=$lang['info_ban'];
require_once('templates/info.pxtm'); die();}
}

if($uid>1 && $settings['chaton']<1){
$info_timeout=0; $info_line=$settings['chatoff'];
require_once('templates/info.pxtm'); die();}

// ---

$uname=abc123($xuser['name'],'');

$user_visible=1;
if(isset($stealth_users) && is_array($stealth_users) && in_array($uid,$stealth_users)){$user_visible=0;}

$tstamp_token=$timestamp+$settings['token_validity'];

$b64name=base64_encode($uname);
$admin=0;if($uid==1){$admin=1;}
$hash=hash('sha256',$b64name.$uid.'1'.$admin.$user_visible.$tstamp_token.$settings['server_key']);

$stoken=hash('sha256',$uid.$settings['random_salt']);
$stoken=$uid.'z'.$stoken;

$ptoken=0;$applink='';

$mtoken=hash('sha256',$uid.$b64name.'1'.$settings['random_salt']);
$mtoken=$uid.'|'.$b64name.'|1|'.$mtoken;

if($user_visible>0){
$country='-'; if(isset($_SERVER['GEOIP_COUNTRY_NAME'])){$country=neutral_escape($_SERVER['GEOIP_COUNTRY_NAME'],64,'str');}
$roundhour=gmdate('i',$timestamp)*60+gmdate('s',$timestamp);$roundhour=$timestamp-$roundhour;

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_iplog WHERE id=$uid AND ipaddr='$ipaddr' AND timestamp>$roundhour");
if(neutral_num_rows($res)<1){neutral_query('INSERT INTO '.$dbss['prfx']."_iplog VALUES($uid,'$uname','$ipaddr',$timestamp,'$country')");}
}

//user extra
$mymotto=''; $myavatar='avatars/001.svg';
$res=neutral_query('SELECT * FROM '.$dbss['sprf'].'_uxtra WHERE id='.$uid);
if(neutral_num_rows($res)>0){
$uxtra=neutral_fetch_array($res); $mymotto=htmlspecialchars($uxtra['motto']);
if(strlen($uxtra['image'])>0){$myavatar=htmlspecialchars($uxtra['image']);}}

// prepare list of emoticons to replace
require_once('emocodes.php');

$emos2js='';  $emos2dv='';

foreach ($emoticons as $emo) {
$emo=explode(' ',$emo);

if(isset($emo[2])){
$emos2js.="emos['".$emo[0]."']='".$emo[1]."';\r\n";
if($emo[2]==1){$emos2dv.='<span class="'.$emo[1].' chat_list_emoticon" onclick="shoop(this,1,100);emo2input(\''.$emo[0].'\')"></span>';}
}}

//prepare language options
$lang2select=array();
foreach ($lang_list as $key => $value) {
$sel='';if($options[0]==$key){$sel=' selected="selected"';}
$lang2select[]='<option title="'.$value[0].'" value="'.$key.'"'.$sel.'>'.$value[1].'</option>';
} sort($lang2select); $lang2select=implode("\n",$lang2select);

// get stickers from cache or set empty array if no cache
$mcach=neutral_query('SELECT * FROM '.$dbss['prfx']."_scache WHERE id='sticache1' OR id='sticache2'");
while($row=neutral_fetch_array($mcach)){$msti[$row['id']]=$row['value'];}
if(strlen($msti['sticache1'])<1){$sticker_sets=array();} else{$sticker_sets=unserialize(base64_decode($msti['sticache1']));}
if(strlen($msti['sticache2'])<1){$stick2js='stickers=new Array();';} else{$stick2js=base64_decode($msti['sticache2']);}

// prepare badwordsfilter
$badwordsfilter='';
if(strlen($settings['badwords'])>0){ $bwords=explode(',',$settings['badwords']);
foreach($bwords as $key => $value){$bwords[$key]="'$bwords[$key]'";}
$bwords=implode(',',$bwords); $badwordsfilter='badwordsfilter=new Array('.$bwords.');';}

// prepare language entries to be used with JS functions
$lang2js='';
foreach ($js_lang as $key => $value) {
$lang2js.="lang['".$key."']='".$value."';\r\n";
}

// prepare colors and set color on load
$colors=''; $css_colors=''; $i=0; $swap_color_onload='current_color='.$options[3].';';
$db_col=explode('|',$settings['colors']);

foreach ($db_col as $value) {$i+=1;
if($options[3]==$i){$swap_color_onload.='swap_color('.$i.',\'#'.$value.'\',0);';}
if(preg_match("/^[a-fA-F0-9]+$/", $value) == 1 && $value!='123456') {
$css_colors.='.tt'.$i.'{color:#'.$value.'}'."\r\n";
$colors.='<span onclick="swap_color('.$i.',\'#'.$value.'\',1)" class="x_circle panel_color" style="background-color:#'.$value.'"></span>'."\r\n";
}}

// prepare pm log
if(!isset($settings['pmlog_stop'])){$pmlogstop=86400;}else{$pmlogstop=(int)$settings['pmlog_stop'];}

if($pmlogstop>0){

$pmhist=$timestamp-$pmlogstop; $pmlog='';
$res=neutral_query('SELECT userid,username,MAX(timestamp) AS timestamp FROM (SELECT userid,username,timestamp FROM '.$dbss['prfx']."_messages WHERE roomid=0 AND timestamp>$pmhist AND touserid=$uid) AS t GROUP BY userid,username ORDER BY timestamp DESC LIMIT 20 OFFSET 0");

while($row=neutral_fetch_array($res)){

$pmid=$row['userid'];
$pmname=htmlspecialchars($row['username']);
$pmdate=gmdate('Y-m-d',$row['timestamp']);

$pmlog.='<div id="pmd'.$pmid.'" class="x_accent_bb pointer panel_pmitem" onclick="if(!show2user('.$pmid.',this)){setTimeout(\'manage_esc()\',100)}">';
$pmlog.='<div class="panel_pmavtr" style="background-image:url(avatar.php?q='.$pmid.')"></div>';
$pmlog.='<b>'.$pmname.'</b><br /><small>'.$pmdate.'</small><br style="clear:both" /></div>';

}}else{$pmlog='';}

// prepare rooms
if(isset($_COOKIE['room'])){$tmpcheckroom=(int)$_COOKIE['room'];setcookie('room','',time()-3600,'/');}else{$tmpcheckroom=0;}
$rooms=array(); $rooms2js='rooms=new Array();'; $landing_room=1; 
$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_rooms ORDER BY zorder,id ASC');
while($row=neutral_fetch_array($res)){
if($row['id']==$tmpcheckroom){$landing_room=$tmpcheckroom;}
$rooms[]=array($row['id'],$row['name'],$row['description'],$row['color']);
$rooms2js.="rooms[".$row['id']."]=new Array('".$row['color']."',roomhistbutton,0,'',0);";}

// prepare room backgrounds / css
$svgbg='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 200 100"><text font-size="'.$settings['roombgs'].'px" fill-opacity="opatxt" font-weight="bold" y="90" x="5" font-family="fnttxt" fill="#txtclr">svgtxt</text></svg>';
$bgrooms='';
foreach($rooms as &$bgt){

$col_repl=$settings['roombgc'];
if($col_repl=='123456'){$col_repl=$bgt[3];}
$tra_repl=$settings['roombgt']/100;

if($settings['showroombg']>0){
$notag=htmlspecialchars(substr(strip_tags($bgt[1]),0,$settings['roombgl']));
$curbg=str_replace('svgtxt',$notag,$svgbg);
$curbg=str_replace('txtclr',$col_repl,$curbg);
$curbg=str_replace('opatxt',$tra_repl,$curbg);
$curbg=str_replace('fnttxt',$settings['roombgf'],$curbg);
$curbg=base64_encode($curbg);
$srm='{background-image: url("data:image/svg+xml;base64,'.$curbg.'")}';}
else{$srm='{background-image:none}';}

$bgrooms.='.rr'.$bgt[0].$srm."\n";
}

// fake users
$ctm=gmdate('H',time()+$settings['acp_offset']*60);
$res=neutral_query('SELECT a.id AS fid, a.status AS fstatus, b.name AS fname FROM '.$dbss['prfx'].'_ufake a, '.$dbss['prfx']."_users b WHERE a.id=b.id AND $ctm>=a.hour_begin AND $ctm<a.hour_end");
$ufake=''; $rcount=0;
if(neutral_num_rows($res)>0){
$ufake=array();
while($row=neutral_fetch_array($res)){$rcount-=1; $ufake[] = "'$rcount':[".$row['fid'].",1,'".$row['fname']."',".$row['fstatus']."]";}
$ufake=implode(',',$ufake);
$ufake='fls_online={'.$ufake.'};';
}

// set sound_on and ampm on load
$swap_ampm_onload=''; $swap_sound_onload=''; $swap_pmreg_onload='';

if($options[1]<2){$swap_ampm_onload='swap_ampm(0);';}
if($options[1]<1){$swap_ampm_onload.='swap_ampm(0);';}
if($options[2]==0){$swap_sound_onload='swap_sound(0);';}
if($options[4]==1){$swap_pmreg_onload='swap_pmreg(0,\''.$lang['on'].'\',0);';}

if(isset($msg_enter) && isset($sys_xuser)){
neutral_query('INSERT INTO '.$dbss['prfx']."_messages VALUES(NULL,999999,0,0,'$sys_xuser',0,'','$uname $msg_enter',0,0,$timestamp)");
}

require_once('templates/blabax.pxtm');
?>