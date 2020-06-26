<?php

if(count(get_required_files())<2){die();}

$allusr=neutral_query('SELECT COUNT(*) FROM '.$dbss['prfx'].'_users');
$allusr=neutral_fetch_array($allusr);$allusr=(int)$allusr[0];

$allgrp=neutral_query('SELECT COUNT(*) FROM '.$dbss['prfx'].'_groups');
$allgrp=neutral_fetch_array($allgrp);$allgrp=(int)$allgrp[0];

$allmsg=neutral_query('SELECT COUNT(*) FROM '.$dbss['prfx'].'_messages WHERE userid>0');
$allmsg=neutral_fetch_array($allmsg);$allmsg=(int)$allmsg[0];

$allupl=neutral_query('SELECT COUNT(*) FROM '.$dbss['sprf'].'_fmedia');
$allupl=neutral_fetch_array($allupl);$allupl=(int)$allupl[0];

$allddl=neutral_query('SELECT COUNT(*) FROM '.$dbss['sprf'].'_paintings');
$allddl=neutral_fetch_array($allddl);$allddl=(int)$allddl[0];

$stats=array();
$stats['users']=$allusr;
$stats['groups']=$allgrp;
$stats['messages']=$allmsg;
$stats['uploads']=$allupl;
$stats['doodles']=$allddl;

$json=json_encode($stats,JSON_FORCE_OBJECT);

?>