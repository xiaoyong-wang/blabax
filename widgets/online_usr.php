<?php

if(count(get_required_files())<2){die();}

$pp=$ping_period*3;
neutral_query('DELETE FROM '.$dbss['prfx']."_online WHERE $timestamp-timestamp>$pp");

$usrall=array();

$res=neutral_query('SELECT id,name FROM '.$dbss['prfx'].'_online ORDER BY name');
while($row=neutral_fetch_array($res)){ $usrall[$row['id']]=$row['name']; }

$ctm=gmdate('H',time()+$settings['acp_offset']*60);
$res=neutral_query('SELECT b.id, b.name AS name FROM '.$dbss['prfx'].'_ufake a, '.$dbss['prfx']."_users b WHERE a.id=b.id AND $ctm>=a.hour_begin AND $ctm<a.hour_end");
while($row=neutral_fetch_array($res)){ $usrall[$row['id']]=$row['name']; }

$json=json_encode($usrall,JSON_FORCE_OBJECT);

?>