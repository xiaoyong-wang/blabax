<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();
$avsize=(int)$settings['avsize'];

if(isset($_FILES['avupload']) && isset($_FILES['avupload']['size']) && isset($_POST['avatar']) && isset($_POST['stoken'])){

$stoken=explode('z',$_POST['stoken']); $id=(int)$stoken[0];
if(!isset($stoken[1]) || hash('sha256',$stoken[0].$settings['random_salt']) != $stoken[1]){die();}

$avatar2db=''; $motto2db=''; $image_pro=0;

$avatar_filename='attachments/'.substr($stoken[1],0,20);

if(move_uploaded_file($_FILES['avupload']['tmp_name'],$avatar_filename)){
chmod("$avatar_filename",0666); $image_pro=1;}

$maxsize=(int)$settings['avatar_msize'];

if($image_pro==1 && filesize($avatar_filename)<$maxsize){$image_pro=2;}

if($image_pro==2){

$smime=0;

if(function_exists('finfo_open') && function_exists('finfo_file')){
$finfo=@finfo_open(FILEINFO_MIME_TYPE);$smime=@finfo_file($finfo,$avatar_filename);}
elseif(function_exists('mime_content_type')){$smime=@mime_content_type($avatar_filename);}

if($smime=='0'){
$unkext=explode('.',$_FILES['avupload']['name']);
$unkext=strtolower(end($unkext));
switch($unkext){
case 'jpeg': $smime='image/jpeg'; break;
case 'jpg' : $smime='image/jpeg'; break;
case 'png' : $smime='image/png';  break;
case 'gif' : $smime='image/gif';  break;
default    : $smime='unknown';    break;}}

$extension=explode('/',$smime);
if(isset($extension[1])){$ext=strtolower($extension[1]);}

if(isset($ext) && ($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='gif')){
@rename($avatar_filename,$avatar_filename.'.'.$ext);
$avatar_filename=$avatar_filename.'.'.$ext; 

// resize begin
if(!isset($unkext) && function_exists('imagecreatefromjpeg') && function_exists('imagecreatefrompng') && function_exists('imagecreatefromgif')){
switch($ext){
case 'jpeg': function imgsrc($v){return imagecreatefromjpeg($v);} function imgdst($d,$f){return imagejpeg($d,$f);} break;
case 'jpg' : function imgsrc($v){return imagecreatefromjpeg($v);} function imgdst($d,$f){return imagejpeg($d,$f);} break;
case 'png' : function imgsrc($v){return imagecreatefrompng($v);}  function imgdst($d,$f){return imagepng($d,$f);}  break;
case 'gif' : function imgsrc($v){return imagecreatefromgif($v);}  function imgdst($d,$f){return imagegif($d,$f);}  break;
default    : break;}

if(function_exists('imgsrc') && function_exists('imgdst') && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')){
$src=imgsrc($avatar_filename); list($width,$height)=getimagesize($avatar_filename);

$offsetx=0;$offsety=0;$xwidth=$width;$xheight=$height;
if($width>$height){$offsetx=round(($width-$height)/2);$xwidth=$width-$offsetx*2;}
if($height>$width){$offsety=round(($height-$width)/2);$xheight=$height-$offsety*2;}

$dst=imagecreatetruecolor($avsize,$avsize);
imagealphablending($dst,false); imagesavealpha($dst,true);
imagecopyresampled($dst,$src,0,0,$offsetx,$offsety,$avsize,$avsize,$xwidth,$xheight);
imgdst($dst,$avatar_filename);}}
// resize end

$image_pro=3;}}

if($image_pro<3){@unlink($avatar_filename);}else{$avatar2db=$avatar_filename;}

if(strlen($_POST['avatar'])>5){$avatar2db=neutral_escape($_POST['avatar'],50,'str');
if(!file_exists($avatar2db)){$avatar2db='';}}

if(isset($_POST['motto']) && strlen(trim($_POST['motto']))>0){$motto2db=neutral_escape($_POST['motto'],64,'str');}

neutral_query('DELETE FROM '.$dbss['sprf'].'_uxtra WHERE id='.$id);
neutral_query('INSERT INTO '.$dbss['sprf']."_uxtra VALUES($id,'$avatar2db','$motto2db',0,'','')");

redirect('blabax.php');die(); }


// ---------------

if(isset($_GET['list'])){
$list_pics='';

$mcache=neutral_query('SELECT value FROM '.$dbss['prfx']."_scache WHERE id='avt_cache'");
$mcache=neutral_fetch_array($mcache); $avt_cache=$mcache[0];

if(strlen($avt_cache)<1){$avt_set=array();} else{$avt_set=unserialize(base64_decode($avt_cache));}

shuffle($avt_set);

for($i=0;$i<count($avt_set);$i++){
$pic='<img src="avatars/'.$avt_set[$i].'" alt="" class="avatar_list" id="av'.$avt_set[$i].'" onclick="de(\'avupload\').value=\'\';de(\'avatar\').value=\'avatars/'.$avt_set[$i].'\';de(\'my_avatar_pic\').src=\'avatars/'.$avt_set[$i].'\';shoop(this,1,100);pan.scrollTop=0" /> ';
$list_pics.=$pic;
}
print $list_pics;}

?>
