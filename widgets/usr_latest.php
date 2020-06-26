<?php

if(count(get_required_files())<2){die();}

$last_seen=array(); $alrd_seen=array(); $tcnt=0;

$res=neutral_query('SELECT a.id AS uid, a.name AS uname, a.timestamp AS tstamp, b.image AS avatar FROM '.$dbss['prfx'].'_iplog a LEFT JOIN '.$dbss['sprf'].'_uxtra b ON a.id=b.id ORDER BY a.timestamp DESC LIMIT 200 OFFSET 0');
while($row=neutral_fetch_array($res)){

if(!in_array($row['uid'],$alrd_seen)){
if($tcnt>9){break;} 

$alrd_seen[]=$row['uid'];

if(strlen($row['avatar'])>0){$avatar=$urli.htmlspecialchars($row['avatar']);}
else{$avatar=$urli.$xpath_av_dir.'/001.svg';}

$stamp=gmdate('M d H:i',$row['tstamp']+$settings['acp_offset']*60);

$last_seen[]=array($row['uname'],$avatar,$stamp); 
$tcnt+=1;}}

$json=json_encode($last_seen,JSON_FORCE_OBJECT);

?>