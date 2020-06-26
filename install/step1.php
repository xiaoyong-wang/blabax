<?php 

$blabws_server_path='';
$blabws_server_port=9001;
$blabws_server_akey='';
$blabws_server_logf='/dev/null';
$blabws_server_addr=$_SERVER['HTTP_HOST'];
$blabws_prpas_token='';
$blabws_server_prto='ws';

require_once('lang_english.utf8'); ?>

<!DOCTYPE html>
<html lang="en">

<head><title><?php print $lang['installing'].' ('.$lang['step'].' 1)';?></title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="style.css" />
</head>

<body class="x_global x_overal">

<?php
if(is_file('../config.php') && filesize('../config.php')>5){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<?php print $lang['config_set'];?>
</div></body></html>
<?php die();}?>

<?php
if(!is_file('../config.php') || !is_writeable('../config.php')){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<?php print $lang['config_chm'];?>
</div></body></html>
<?php die();}?>

<form action="step2.php" method="post"  autocomplete="off">
<div class="holder">
<h2><?php print $lang['step'];?> 1</h2>
<hr />

<div id="whole_form">

<div><?php print $lang['step2_desc'];?></div>
<br /><hr />

<input type="hidden" name="blabws_server_path" value="<?php print $blabws_server_path;?>" />
<input type="hidden" name="blabws_server_port" value="<?php print $blabws_server_port;?>" />
<input type="hidden" name="blabws_server_prto" value="<?php print $blabws_server_prto;?>" />
<input type="hidden" name="blabws_server_akey" value="<?php print $blabws_server_akey;?>" />
<input type="hidden" name="blabws_server_logf" value="<?php print $blabws_server_logf;?>" />
<input type="hidden" name="blabws_server_addr" value="<?php print $blabws_server_addr;?>" />
<input type="hidden" name="blabws_prpas_token" value="<?php print $blabws_prpas_token;?>" />

<h2>MySQLi</h2><hr />

<div class="left">
<?php print $lang['dbhost'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbhost" value="localhost" />
</div><br /><hr />

<div class="left">
<?php print $lang['dbname'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbname" value="" />
</div><br /><hr />

<div class="left">
<?php print $lang['dbuser'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbuser" value="" />
</div><br /><hr />

<div class="left">
<?php print $lang['dbpass'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbpass" value="" />
</div><br /><hr />

<div class="left">
<?php print $lang['dbprfx'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbprfx" value="blabax" />
</div><br /><hr />

<div class="left">
<?php print $lang['dbcset'];?>
</div>
<div class="right">
<select class="x_accent_bb s250" name="dbcset">
<option value="utf8mb4">utf8mb4</option>
<option value="utf8">utf8 (MySQL &lt; 5.5.3)</option>
</select>
</div><br /><hr />

<div style="text-align:center"><?php print $lang['sock_desc'];?></div>
<br /><hr />

<div class="left">
<?php print $lang['dbsock'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="dbsock" value="" />
</div><br /><hr />

<input type="button" class="round4 x_accent_bg" style="width:100%;font-weight:bold;height:50px" value="<?php print $lang['testconn'];?>" onclick="test_conn()" />
</div>


<div id="mysqli_submit" style="display:none">
<div  style="text-align:center;font-weight:bold"><?php print $lang['dbok'];?></div>
<br /><hr />
<input type="button" class="round4 x_bcolor_bg" style="width:100%;font-weight:bold;height:50px" value="<?php print $lang['next'];?>" onclick="document.forms[0].submit()" />
</div>

</div>
</form>

<script>

function test_conn(x){
f=document.forms[0]
s='dbhost='+encodeURIComponent(f.dbhost.value)+'&dbname='+encodeURIComponent(f.dbname.value)+'&dbuser='+encodeURIComponent(f.dbuser.value)+'&dbpass='+encodeURIComponent(f.dbpass.value)+'&dbsock='+encodeURIComponent(f.dbsock.value)+'&dbcset='+encodeURIComponent(f.dbcset.value);
htto=new XMLHttpRequest(); htto.open('post','testcon.php');
htto.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
htto.onreadystatechange=ans; htto.send(s);}

function ans(){
if(htto.readyState==4 && htto.status==200){
response=htto.responseText.toString()

if(response!='ok'){alert(response)
if(response.search('utf8mb4')>0){document.forms[0].dbcset.value='utf8'}
return}

document.getElementById('whole_form').style.display='none'; document.getElementById('mysqli_submit').style.display='block'}
}


document.forms[0].reset()
window.onunload=function(){}
</script>
</body>
</html>