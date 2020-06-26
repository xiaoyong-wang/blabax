<?php

/* -------------------------------------------------------------------------------

Should be called from your page as a JS file: <script src="PATH-TO/online.php?mode=MODE"></script> 

MODES:

online.php?mode=0    ->>    document.write('3')
online.php?mode=1    ->>    document.write('Jo, Adam, Maria')

online.php?mode=2    ->>    adds a 'position:fixed' DIV to the BODY element of your page;
                            can be updated automatically with Ajax requests
   __________________
  |                  |
  | NOW IN CHAT: 3   |
  |------------------|
  | Jo, Adam, Maria  |
  |__________________|

------------------------------------------------------------------------------- */

// SETTINGS
// --------------------------------------------------------------------------- //

// mode2box: style of the online box
$baxdv_box_style='font-size:90%; position:fixed; right:1%; bottom:1%; min-width:100px; max-width:200px; padding:10px; background-color:#3F51B5; color:#FFFFFF; border-radius:5px;';

// mode2box: line between title and usernames
$bax_draw_line='<hr style="height:1px; border:none; background-color:#fff; opacity:0.5" />';

// mode2box: miliseconds before hiding the online box; 0 to disable
$bax_online_hide=30000; 

// mode2box: update interval miliseconds; 0 to disable
$bax_update_interval=5000; 

// --------------------------------------------------------------------------- //

$url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
if(isset($_GET['mode'])){ $mode=(int)$_GET['mode']; } else{ $mode=0; }

$lst_users=array();

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

$pp=$ping_period*3;
neutral_query('DELETE FROM '.$dbss['prfx']."_online WHERE $timestamp-timestamp>$pp");

$res=neutral_query('SELECT name FROM '.$dbss['prfx'].'_online ORDER BY name');
while($row=neutral_fetch_array($res)){$lst_users[]=$row['name'];}

$ctm=gmdate('H',time()+$settings['acp_offset']*60);
$res=neutral_query('SELECT b.name AS name FROM '.$dbss['prfx'].'_ufake a, '.$dbss['prfx']."_users b WHERE a.id=b.id AND $ctm>=a.hour_begin AND $ctm<a.hour_end");
while($row=neutral_fetch_array($res)){$lst_users[]=$row['name'];}


$num_users=count($lst_users);
sort($lst_users);
$lst_users=implode(', ',$lst_users); 

if($mode==0){print 'document.write(\''.$num_users.'\')';}
if($mode==1){print 'document.write(\''.$lst_users.'\')';}
?>

<?php if($mode==2){ ?>

/* THE JAVASCRIPT PART */

function add_users_online_box(){

baxdv=document.createElement('div');
baxdv.style.cssText='<?php print $baxdv_box_style; ?>';

setTimeout('ajx_snd()',800);
setTimeout('document.body.appendChild(baxdv)',1000);

<?php if($bax_online_hide > 2000){print "setTimeout('stop_hide()',$bax_online_hide)";} ?>
}

function ajx_snd(){
ajax_obj=new XMLHttpRequest();
ajax_obj.open('get','<?php print $url;?>?mode=3')
ajax_obj.onreadystatechange=ajx_rcv
ajax_obj.send()}

function ajx_rcv(){
if(ajax_obj.readyState==4 && ajax_obj.status==200){
response=ajax_obj.responseText.toString();
baxdv.innerHTML=response}}

function stop_hide(){baxdv.style.display='none';
if(typeof bax_interval == 'number'){clearInterval(bax_interval);}}

window.addEventListener('load',add_users_online_box,false);
<?php if($bax_update_interval>0){print "bax_interval=setInterval('ajx_snd()',$bax_update_interval)";} ?>

<?php } ?>

<?php if($mode==3){ print "<b>Now in chat: $num_users</b> $bax_draw_line $lst_users";} ?>
