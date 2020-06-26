<?php

if(count(get_required_files())<2){die();}

$top10users=array();

$res=neutral_query('SELECT a.userid AS uid,a.username AS uname,COUNT(a.id) AS msgnm, b.image AS avatar FROM '.$dbss['prfx'].'_messages a LEFT JOIN '.$dbss['sprf'].'_uxtra b ON a.userid=b.id WHERE a.userid>0 GROUP BY a.userid,a.username,b.image ORDER BY msgnm DESC LIMIT 10 OFFSET 0');


while($row=neutral_fetch_array($res)){

$msgnum=(int)$row['msgnm'];

if(!isset($maxx)){$maxx=$msgnum;$peruser=100;}
else{$peruser=$msgnum*100/$maxx;$peruser=(int)$peruser;}


if(strlen($row['avatar'])>0){$avatar=$urli.htmlspecialchars($row['avatar']);}
else{$avatar=$urli.$xpath_av_dir.'/001.svg';}

$top10users[]=array($row['uname'],$avatar,$msgnum,$peruser);}

$json=json_encode($top10users,JSON_FORCE_OBJECT);

?>