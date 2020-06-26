<?php

if(!isset($_GET['id'])){die();}
$id=(int)$_GET['id']; if($id<1){die();}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect();

$motto='';
$avatar='avatars/001.svg';

$res=neutral_query('SELECT * FROM '.$dbss['sprf'].'_uxtra WHERE id='.$id);
$uxtra=neutral_fetch_array($res); 

if(isset($uxtra['image']) && strlen($uxtra['image'])>0){$avatar=htmlspecialchars(str_replace('|','',$uxtra['image']));}

if(isset($uxtra['motto']) && strlen($uxtra['motto'])>0){$motto=htmlspecialchars(str_replace('|','',$uxtra['motto']));}
else{
$settings=get_settings();
$mottos=explode('|',$settings['mottos']);
$motto=array_rand($mottos); $motto=htmlspecialchars($mottos[$motto]);}

$uxtra=$id.'|'.$motto.'|'.$avatar;
print $uxtra;

?>