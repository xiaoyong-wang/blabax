<?php

if(count(get_required_files())<2){die();}

$pp=$ping_period*3;
neutral_query('DELETE FROM '.$dbss['prfx']."_online WHERE $timestamp-timestamp>$pp");

$usrids=array(); $usrall=array(); $avatar=array(); $finals=array();

$res=neutral_query('SELECT id,name FROM '.$dbss['prfx'].'_online ORDER BY name');
while($row=neutral_fetch_array($res)){ $usrall[$row['id']]=$row['name']; $usrids[]=$row['id']; }

$ctm=gmdate('H',time()+$settings['acp_offset']*60);
$res=neutral_query('SELECT b.id, b.name AS name FROM '.$dbss['prfx'].'_ufake a, '.$dbss['prfx']."_users b WHERE a.id=b.id AND $ctm>=a.hour_begin AND $ctm<a.hour_end");
while($row=neutral_fetch_array($res)){ $usrall[$row['id']]=$row['name']; $usrids[]=$row['id']; }

$usrids=implode(',',$usrids); if(strlen($usrids)<1){$usrids=0;}
$res=neutral_query('SELECT id,image FROM '.$dbss['sprf']."_uxtra WHERE id IN ($usrids)");
while($row=neutral_fetch_array($res)){ $avatar[$row['id']]=$row['image']; }

foreach ($usrall as $key => $value) {
if(isset($avatar[$key])){$finals[$value]=$urli.$avatar[$key];}
else{$finals[$value]=$urli.$xpath_av_dir.'/001.svg';}}

$json=json_encode($finals,JSON_FORCE_OBJECT);

?>