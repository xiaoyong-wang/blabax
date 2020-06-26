<?php

require_once('config.php');

$avatar_file=$xpath_av_dir.'/001.svg';
if(isset($_GET['q'])){$q=(int)$_GET['q'];}else{$q=-1;}

if($q==0){$avatar_file=$xpath_av_dir.'/000.svg';}

if($q>0 && (!isset($disablema) || $disablema<1)){

require_once('incl/main.php'); neutral_dbconnect();

$res=neutral_query('SELECT * FROM '.$dbss['sprf'].'_uxtra WHERE id='.$q);

$uxtra=neutral_fetch_array($res); 

if(isset($uxtra['image']) && strlen($uxtra['image'])>0){
$avatar_file=htmlspecialchars($uxtra['image']); }}

header('Location: '.$avatar_file);

?>