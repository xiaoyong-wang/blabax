<?php 

if(!isset($_POST['dbhost']) || !isset($_POST['dbname']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass'])){die();}

require_once('../version.php');
require_once('lang_english.utf8'); ?>

<!DOCTYPE html>
<html lang="en">

<head><title><?php print $lang['installing'].' ('.$lang['step'].' 2)';?></title>
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

<?php

// setting config.php

$blabws_server_path=$_POST['blabws_server_path'];
$blabws_server_port=$_POST['blabws_server_port']; if($blabws_server_port==''){$rplport=9001;$dbport='';}else{$rplport=$blabws_server_port;$dbport=$blabws_server_port;}
$blabws_server_prto=$_POST['blabws_server_prto'];
$blabws_server_akey=$_POST['blabws_server_akey'];
$blabws_server_logf=$_POST['blabws_server_logf'];
$blabws_server_addr=$_POST['blabws_server_addr'];
$blabws_prpas_token=$_POST['blabws_prpas_token'];
if($blabws_server_port==''){$EXTHOSTED=1;}else{$EXTHOSTED=0;}

if(isset($_POST['dbhost'])){$dbhost=$_POST['dbhost'];}else{$dbhost='localhost';}
if(isset($_POST['dbname'])){$dbname=$_POST['dbname'];}else{$dbname='';}
if(isset($_POST['dbuser'])){$dbuser=$_POST['dbuser'];}else{$dbuser='';}
if(isset($_POST['dbpass'])){$dbpass=$_POST['dbpass'];}else{$dbpass='';}
if(isset($_POST['dbcset'])){$dbcset=$_POST['dbcset'];}else{$dbcset='utf8';}
if(isset($_POST['dbprfx'])){$dbprfx=trim($_POST['dbprfx']);}else{$dbprfx='blabax';}
if(isset($_POST['dbsock'])){$dbsock=trim($_POST['dbsock']);}else{$dbsock='';}

function slash_n_replace($a,$b,$c){
$b=trim(addcslashes($b,"'\\"));
$c=str_replace($a,$b,$c); return $c;}

$config=@file('phpconfig',FILE_IGNORE_NEW_LINES);
$config=implode("\n",$config);
$config=slash_n_replace('DBHOST',$dbhost,$config);
$config=slash_n_replace('DBNAME',$dbname,$config);
$config=slash_n_replace('DBUSER',$dbuser,$config);
$config=slash_n_replace('DBPASS',$dbpass,$config);
$config=slash_n_replace('PREFIX',$dbprfx,$config);
$config=slash_n_replace('DBSOCK',$dbsock,$config);
$config=slash_n_replace('DBCSET',$dbcset,$config);
$config=slash_n_replace('BLABWSPATH',$blabws_server_path,$config);
$config=slash_n_replace('BLABWSPORT',$rplport,$config);
$config=slash_n_replace('BLABWSAKEY',$blabws_server_akey,$config);
$config=slash_n_replace('BLABWSLOGF',$blabws_server_logf,$config);
$config=slash_n_replace('EXTHOSTED',$EXTHOSTED,$config);

$handle=fopen('../config.php','w');fwrite($handle,$config);fclose($handle);

require_once('../config.php');
require_once('../incl/mysqli_functions.php');

function rand_str($l){
$l=(int)$l; if($l<5){$l=5;}
$str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$len=strlen($str); $randstr='';
if(function_exists('random_int')){
for($i=0;$i<$l;$i++){$randstr.=$str[random_int(0,$len-1)];}
return $randstr;}
if(function_exists('password_hash')){
$randstr=substr(password_hash(microtime(),PASSWORD_DEFAULT),10,60);
$randstr=preg_replace('/[^\da-z]/i','',$randstr.$randstr);
$randstr=substr($randstr,0,$l); return $randstr;}
$randstr=substr(substr(md5(microtime()),0,15).substr(base64_encode(sha1(time())),0,15).substr(sha1(microtime()),0,15).substr(base64_encode(md5(time())),0,15),0,$l);
return $randstr;}

function process_error($x){
print $x;die();
}

$cookiesalt=rand_str(50);
$randomsalt=rand_str(40);
$cronkeyrnd=rand_str(20);
$timestamp=time();


$options=' ENGINE=MYISAM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
if($dbss['cset']=='utf8'){$options=' ENGINE=MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci';}

// db install goes here

$install=array();
neutral_dbconnect();

$install[]='CREATE TABLE '.$dbss['prfx'].'_online(
id int(11) NOT NULL,
name varchar(64) NOT NULL,
ugroup smallint NOT NULL,
ipaddr varchar(50) NOT NULL,
timestamp int(11) NOT NULL,
status smallint NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_ban(
id integer NOT NULL,
name varchar(64) NOT NULL,
ipaddr varchar(64) NOT NULL,
timestamp integer NOT NULL,
ulevel smallint NOT NULL,
ban smallint NOT NULL,
aname varchar(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_social(
id varchar(128) NOT NULL,
userid integer NOT NULL,
social char(2) NOT NULL,
sA varchar(256) NOT NULL,
sB varchar(256) NOT NULL,
sC varchar(512) NOT NULL,
sD varchar(512) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_scache(
id varchar(16) NOT NULL,
value mediumtext NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_bflog(
id integer NOT NULL,
ipaddr varchar(64) NOT NULL,
token integer NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_iplog(
id integer NOT NULL,
name varchar(64) NOT NULL,
ipaddr varchar(64) NOT NULL,
timestamp integer NOT NULL,
country varchar(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_grules(
id integer NOT NULL auto_increment PRIMARY KEY,
description varchar(256) NOT NULL,
scenario text NOT NULL,
ugroup integer NOT NULL,
zorder integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_rooms(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
description varchar(128) NOT NULL,
color char(6) NOT NULL,
zorder integer NOT NULL,
hidden smallint NOT NULL,
groupids varchar(256) NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_rooms VALUES(1,'PanDEMONiuM','Everybody\'s Very Welcome!','3B7AB5',0,0,'')";
$install[]='INSERT INTO '.$dbss['prfx']."_rooms VALUES(2,'Mediterranean Dish','What to Eat, and How to Cook It!','FFD93F',1002,0,'')";
$install[]='INSERT INTO '.$dbss['prfx']."_rooms VALUES(3,'Vivian\'s Bedroom','Don\'t Come In, I\'m Naked!','D90048',1003,0,'')";
$install[]='INSERT INTO '.$dbss['prfx']."_rooms VALUES(4,'Honky-Tonk Corner','WARNING! 85+ Only!','8BC34A',1004,0,'')";

$install[]='CREATE TABLE '.$dbss['prfx'].'_fmedia(
id integer NOT NULL auto_increment PRIMARY KEY,
filename varchar(64) NOT NULL,
file2hdd varchar(255) NOT NULL,
filetype integer NOT NULL,
sourcetxt text NOT NULL,
timestamp integer NOT NULL,
userid integer NOT NULL,
username varchar(64) NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_fmedia VALUES(NULL,'Citrus Fruit','attachments/CitRuS.jpg',1,'',$timestamp,0,'(P)')";
$install[]='INSERT INTO '.$dbss['prfx']."_fmedia VALUES(NULL,'Baby Dont Go','attachments/BrOoNzY.mp3',2,'',$timestamp,0,'(P)')";
$install[]='INSERT INTO '.$dbss['prfx']."_fmedia VALUES(NULL,'Oh Oh Bunnie','attachments/BuNniE.mp4',3,'',$timestamp,0,'(P)')";

$install[]='CREATE TABLE '.$dbss['prfx'].'_paintings(
id integer NOT NULL auto_increment PRIMARY KEY,
description varchar(64) NOT NULL,
srx text NOT NULL,
sry text NOT NULL,
src text NOT NULL,
bgc char(6) NOT NULL,
timestamp integer NOT NULL,
userid integer NOT NULL,
username varchar(64) NOT NULL,
bgid integer NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_paintings VALUES
(2,'Fruits','99 99 101 102 104 106 110 114 119 123 128 134 138 140 142 144 146 148 149 150 152 152 0 0 129 129 129 130 131 133 136 140 144 152 157 161 165 167 169 170 170 170 168 166 165 163 162 162 162 162 162 162 162 162 0 0 0 0 0 314 314 313 311 309 306 304 301 298 295 292 289 286 283 280 277 274 272 269 267 264 261 259 257 256 255 254 253 252 252 251 251 250 249 248 247 246 0 0 238 238 238 237 235 233 231 228 226 224 222 221 221 220 219 219 219 220 222 225 230 236 242 249 258 263 267 273 277 281 283 0 0 0 0 0 38 39 41 43 46 50 54 58 64 69 75 77 79 80 80 81 82 83 85 87 89 91 92 93 93 94 94 0 0 68 68 69 70 72 74 76 80 84 88 92 95 97 99 101 102 103 105 106 106 107 107 107 107 107 107 107 107 107 107 107 107 107 107 107 107 106 0 0 0 0 0 248 248 247 245 243 240 236 231 227 224 222 219 216 213 211 209 207 206 206 206 206 206 206 207 209 211 215 217 221 225 231 235 241 248 257 269 281 291 299 304 307 310 312 315 317 319 321 321 321 321 321 321 319 317 315 312 309 305 301 297 293 289 284 280 273 269 264 260 256 251 247 243 241 239 0 0 221 222 223 224 224 226 229 233 236 238 240 242 243 243 243 243 243 243 242 242 242 242 242 242 242 242 242 0 0 262 262 264 265 267 270 274 278 282 286 288 289 289 289 288 286 284 282 279 276 273 271 269 267 265 263 262 262 261 261 262 263 264 266 270 274 278 283 286 287 288 287 0 0 0 0 0 0','345 345 344 343 341 339 335 333 329 325 321 316 314 311 308 305 303 301 299 297 296 295 0 0 298 297 297 296 294 293 291 289 288 285 283 281 280 278 277 277 278 279 281 285 288 291 294 298 301 303 304 305 306 307 0 0 0 0 0 24 24 26 28 31 33 35 37 40 43 45 47 50 53 55 58 61 63 65 67 71 73 75 77 78 79 80 81 82 83 83 84 85 86 87 88 88 0 0 63 64 64 66 68 74 78 83 87 91 95 97 100 102 104 105 106 106 106 106 104 103 102 101 98 97 96 96 95 94 93 0 0 0 0 0 58 58 59 63 67 70 74 77 79 82 85 86 87 87 88 88 90 91 93 95 98 101 101 102 103 104 105 0 0 103 104 105 106 107 109 111 114 116 118 119 120 120 120 121 121 121 120 120 119 119 118 118 115 111 106 101 96 92 88 82 80 77 74 71 68 66 0 0 0 0 0 144 144 144 144 144 145 146 147 148 149 151 153 156 160 164 168 172 175 180 184 189 193 198 204 210 217 223 229 234 238 242 244 245 246 247 247 247 247 246 244 242 238 235 231 226 221 216 211 207 202 197 192 185 181 176 172 168 164 160 158 155 153 151 149 146 145 143 143 143 142 142 142 142 143 0 0 198 198 196 195 193 192 189 186 183 179 177 175 173 172 173 175 178 182 187 192 197 203 212 216 220 219 218 0 0 175 174 174 173 173 173 173 174 176 179 181 183 185 187 189 191 193 195 197 200 203 205 207 209 211 213 215 217 219 221 221 221 221 221 220 219 218 218 218 218 218 218 0 0 0 0 0 0','414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 414 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 49 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 411 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45 45','ecf0f1',$timestamp,0,'(P)',1)";

$install[]='CREATE TABLE '.$dbss['prfx'].'_settings(
id varchar(32) NOT NULL,
value text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_stimoji(
id integer NOT NULL auto_increment PRIMARY KEY,
filename varchar(64) NOT NULL,
keytags text NOT NULL,
FULLTEXT(keytags))'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_style(
id integer NOT NULL,
value text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_messages(
id integer NOT NULL auto_increment PRIMARY KEY,
roomid integer NOT NULL,
userid integer NOT NULL,
usergroup integer NOT NULL,
username varchar(64) NOT NULL,
touserid integer NOT NULL,
tousername varchar(64) NOT NULL,
line text NOT NULL,
color smallint NOT NULL,
attach smallint NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_messages VALUES (NULL,0,0,0,'system',0,'','Installed successfully...',0,0,$timestamp)";

$install[]='CREATE TABLE '.$dbss['prfx'].'_users(
id integer NOT NULL auto_increment PRIMARY KEY,
ugroup integer NOT NULL,
name varchar(64) NOT NULL,
password char(64) NOT NULL,
email varchar(128) NOT NULL,
salt char(20) NOT NULL,
ipaddr varchar(64) NOT NULL,
question varchar(256) NOT NULL,
answer char(64) NOT NULL,
timestamp integer NOT NULL,
quarantine integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_uxtra(
id integer NOT NULL,
image varchar(128) NOT NULL,
motto varchar(128) NOT NULL,
age smallint NOT NULL,
location varchar(128) NOT NULL,
gender varchar(128) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_ufake(
id integer NOT NULL,
status integer NOT NULL,
hour_begin smallint NOT NULL,
hour_end smallint NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_groups(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
welcome text NOT NULL,
link integer NOT NULL,
color char(6) NOT NULL,
pa smallint NOT NULL,
pb smallint NOT NULL,
pc smallint NOT NULL,
pd smallint NOT NULL,
pe smallint NOT NULL,
pf smallint NOT NULL,
pg smallint NOT NULL,
ph smallint NOT NULL,
pi smallint NOT NULL,
pj smallint NOT NULL,
pk smallint NOT NULL,
pl smallint NOT NULL,
pm smallint NOT NULL,
pn smallint NOT NULL,
po smallint NOT NULL,
pp smallint NOT NULL,
pq smallint NOT NULL,
pr smallint NOT NULL,
ps smallint NOT NULL,
pt smallint NOT NULL,
pu smallint NOT NULL,
pv smallint NOT NULL,
pw smallint NOT NULL,
px smallint NOT NULL,
py smallint NOT NULL,
pz smallint NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_groups VALUES(NULL,'DEFAULT','',0,'FEC400',1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0)";

$install[]='CREATE TABLE '.$dbss['prfx'].'_jbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
infinite integer NOT NULL,
shuffle integer NOT NULL,
gap integer NOT NULL,
delay integer NOT NULL,
cookielength integer NOT NULL,
hremember integer NOT NULL,
elements mediumtext NOT NULL,
template text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_rbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
pm integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
keywords text NOT NULL,
answers mediumtext NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_nbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
lastrun integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
headlines integer NOT NULL,
pagesize integer NOT NULL,
origin char(2) NOT NULL,
topic varchar(256) NOT NULL,
keywords text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_news(
id integer NOT NULL auto_increment PRIMARY KEY,
idnbox integer NOT NULL,
seen integer NOT NULL,
nhead varchar(256) NOT NULL,
npubl varchar(32) NOT NULL,
ndate varchar(10) NOT NULL,
ndesc text NOT NULL,
nlink text NOT NULL,
npict text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_gbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
pagesize integer NOT NULL,
glocale char(2) NOT NULL,
topic varchar(256) NOT NULL,
keywords text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_gifs(
id integer NOT NULL auto_increment PRIMARY KEY,
idgbox integer NOT NULL,
seen integer NOT NULL,
ggif varchar(256) NOT NULL,
gmp4 varchar(256) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_pcache(
ipaddr varchar(64) NOT NULL,
proxy smallint NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_polls(
id integer NOT NULL,
vote integer NOT NULL,
userid integer NOT NULL,
ipaddr varchar(64) NOT NULL)'.$options;

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_lang','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_ampm','2')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('colors','F44336|E91E63|9C27B0|673AB7|3F51B5|2196F3|03A9F4|00BCD4|009688|4CAF50|8BC34A|CDDC39|FFEB3B|FFC107|FF9800|FF5722|795548|607D8B|E53935|D81B60|8E24AA|5E35B1|3949AB|1E88E5|039BE5|00ACC1|00897B|43A047|7CB342|C0CA33|FDD835|FFB300|FB8C00|F4511E|6D4C41|546E7A')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_sound','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('html_title','Our Chat!')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('allow_guest','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('cookie_salt','$cookiesalt')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('random_salt','$randomsalt')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('allow_reg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('reglog_delay','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('userperhour','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('wrongperhour','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mobile_effe','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('dimonblur','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('notes','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ban_period','86400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mute_period','300')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('show_thumbs','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('keepiplg','7776000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_display','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_default','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_icon','svg_star')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_title','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_content','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('avatar_msize','102400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('file_msize','512000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('uploads_user','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('paintings_user','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('style_template','body,td,p,div,input,select,textarea{font-size:[1]px;font-family:[2]}\r\n.x_global{[3]}\r\n.x_blab{[17]}\r\ninput,select{color:#[4]}\r\n.x_overal{color:#[4];background-color:#[5]}\r\n.x_accent_bg{color:#[0];background-color:#[6]}\r\n.x_accent_fg{color:#[6];background-color:transparent}\r\n.x_accent_bb{border-bottom:1px solid #[6]}\r\n.x_input_blabws{color:#[7];background-color:#[8]}\r\n.x_bcolor_x{color:#[9];background-color:#[10]}\r\n.x_bcolor_y{color:#[11];background-color:#[12]}\r\n.x_bcolor_z{color:#[13];background-color:#[14]}\r\n.x_left_rounded{border-radius:[15]px 0 0 [15]px}\r\n.x_right_rounded{border-radius: 0 [15]px [15]px 0}\r\n.x_bottom_rounded{border-radius: 0 0 [15]px [15]px}\r\n.x_top_rounded{border-radius: [15]px [15]px 0 0}\r\n.x_all_rounded{border-radius: [15]px [15]px [15]px [15]px}\r\n.x_circle{border-radius:[16]%}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('style_delivery','body,td,p,div,input,select,textarea{font-size:14px;font-family:sans-serif}\r\n.x_global{}\r\n.x_blab{}\r\ninput,select{color:#FFFFFF}\r\n.x_overal{color:#FFFFFF;background-color:#333333}\r\n.x_accent_bg{color:#000;background-color:#FEC400}\r\n.x_accent_fg{color:#FEC400;background-color:transparent}\r\n.x_accent_bb{border-bottom:1px solid #FEC400}\r\n.x_input_blabws{color:#000000;background-color:#FFFFFF}\r\n.x_bcolor_x{color:#FFFFFF;background-color:#222222}\r\n.x_bcolor_y{color:#FFFFFF;background-color:#222222}\r\n.x_bcolor_z{color:#FFFFFF;background-color:#111111}\r\n.x_left_rounded{border-radius:5px 0 0 5px}\r\n.x_right_rounded{border-radius: 0 5px 5px 0}\r\n.x_bottom_rounded{border-radius: 0 0 5px 5px}\r\n.x_top_rounded{border-radius: 5px 5px 0 0}\r\n.x_all_rounded{border-radius: 5px 5px 5px 5px}\r\n.x_circle{border-radius:100%}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('webkit_css','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('post_interval','2')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acp_css','1.css')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('token_validity','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acp_offset','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_url','$blabws_server_addr')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_key','$blabws_server_akey')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_port','$dbport')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_wss','$blabws_server_prto')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_pps','$blabws_prpas_token')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_bbcms','blabws')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_cookie','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_prefix','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_nolog','../')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_logout','../')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_pflink','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg_style','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg_template','<span class=\"chat_area_time\">{TIME}</span> <span class=\"chat_area_user g{GROUP}\">{NAME}</span>: <span class=\"tt{COLOR}\">{TEXT}</span><br />')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('announce','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('version','$version')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('forcereload','Alexandria')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('drag2scroll','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('whee2scroll','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_g','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_r','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_f','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_num','15')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_fts','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_num','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_dir','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_rnd','summer, mountain, beach')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('svgtstamp','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badge_bgc','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badge_txt','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('showroombg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgf','serif')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgt','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgc','123456')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgs','90')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgl','8')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('upd_cache','$timestamp')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('logio_msg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('multi_links','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg2db','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('appcodesc','Download our Android app from <b class=\"x_accent_fg pointer\" onclick=\"window.open(\'https://play.google.com/store/apps/details?id=com.justblab.bwsq\')\">here</b> and scan the QR code.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('welcome_msg','<b>Welcome to our chat!</b><div style=\"margin:10px\"><div class=\"help_allp help_esck\">The ESC key is your friend. Use it!</div><div class=\"help_allp help_cycl\">Cycle through rooms with Ctrl+Shift+L/R Arrows.</div><div class=\"help_allp help_ctrl\">Change rooms with Ctrl+Shift+1 Ctrl+Shift+2 ...</div><div class=\"help_allp help_drag\">Drag-to-scroll or scroll with the arrow keys.</div><div class=\"help_allp help_dblc\">A double-click swaps scroll &amp; select.</div><div class=\"help_allp help_swip\">A swipe from the left edge opens the menu.</div></div>')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mottos','Acta Non Verba|Audentes Fortuna Iuvat|Alea Iacta Est|Ars Longa, Vita Brevis|Ave, Morituri Te Salutant|Credo Quia Absurdum|Dulce Bellum Inexpertis|Dum Excusare Credis, Accusas|Fabas Indulcet Fames|Fortis Fortuna Adiuvat|In Vino Veritas|Non Ducor Duco|Oderint Dum Metuat|Quis Custodiet Ipsos Custodes?|Semper Ad Meliora|Semper Inops Quicumque Cupit|Si Vis Amari, Ama|Si Vis Pacem, Para Bellum|Sic Transit Gloria Mundi|Transit Umbra, Lux Permanet|Una Hirundo Non Facit Ver|Veni, Vidi, Vici|Vestis Virum Reddit|Vir Sapit Qui Pauca Loquitur|Vires Acquirit Eundo|Vitam Regit Fortuna, Non Sapientia')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_on','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_sz','1000000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_la','30')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_lv','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_ba','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_bv','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_rs','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_us','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rmb_unsent','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_appid','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_r_url','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_t_frm','index.php')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_o','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_g','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_m','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_d','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_u','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_k','$cronkeyrnd')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badwords','')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_set','Arabic,Armenian,Bengali,Bopomofo,Braille,Buhid,Canadian_Aboriginal,Cherokee,Cyrillic,Devanagari,Ethiopic,Georgian,Greek,Gujarati,Gurmukhi,Han,Hangul,Hanunoo,Hebrew,Hiragana,Inherited,Kannada,Katakana,Khmer,Lao,Latin,Limbu,Malayalam,Mongolian,Myanmar,Ogham,Oriya,Runic,Sinhala,Syriac,Tagalog,Tagbanwa,TaiLe,Tamil,Telugu,Thaana,Thai,Tibetan,Yi')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_run','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_msg','Please choose another name! Numeric-only names and and names containing letters of different alphabets are not allowed.')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('avsize','250')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('chaton','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('chatoff','Our chat is closed now...')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rbox_sender','8000001:1:GodFather')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('nbox_sender','8000002:1:NewsMaster')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('newsapi_key','')";

$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('gbox_sender','8000003:1:GIFMaster')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('ptop','')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('pmlog_stop','86400')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('tns_length','200')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('tns_lowprv','0')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('meta_ref','same-origin')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ip2c','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ip2hash','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_on','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_api_src','pg_iphub')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_api_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_timeout','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_tcache','86400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_wlist','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_failmsg','Please turn off your VPN and refresh.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_login','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_reg','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_pass','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acpreadonly','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('genderlist','Male,Female')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('genderedit','1')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_seeres','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_change','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_ipaddr','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_colors','000,ba443e,c17d51,cca851,22865e,ad1457,0d47a1,74554d,6a1b9a,086269')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_ofile','/tmp/online')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_cache','120')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_cross','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_onlu','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_onla','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_stat','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_tten','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_last','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('customjs','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('p2p_global','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stun_svs','stun.stunprotocol.org:3478')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ask_av','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pingws','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('p2p_level','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('uf_order','1')";

$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sticache1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sticache2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('avt_cache','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache3','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache4','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache5','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache6','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound3','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound4','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound5','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound6','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound7','')";

$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(1,'14')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(2,'sans-serif')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(3,'')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(4,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(5,'333333')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(6,'FEC400')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(7,'000000')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(8,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(9,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(10,'222222')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(11,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(12,'222222')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(13,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(14,'111111')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(15,'5')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(16,'100')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(17,'')";

for($i=0;$i<count($install);$i++){neutral_query($install[$i]);}

// end db install
?>

<div class="holder">
<h2><?php print $lang['step'];?> 2</h2>
<hr />

<div><?php print $lang['step3_desc'];?></div>
<br /><hr />

<form action="done.php" method="post" autocomplete="off">

<div class="left">
<?php print $lang['user'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="user" value="" maxlength="12" onfocus="input_style_back(this)" />
</div><br /><hr />

<div class="left">
<?php print $lang['mail'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="mail" value="" maxlength="64" onfocus="input_style_back(this)" />
</div><br /><hr />

<div class="left">
<?php print $lang['pass'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="pass" value="" maxlength="32" onfocus="input_style_back(this)" />
</div><br /><hr />

<div class="left">
<?php print $lang['ques'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="ques" value="" maxlength="128" onfocus="input_style_back(this)" />
</div><br /><hr />

<div class="left">
<?php print $lang['answ'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="answ" value="" maxlength="128" onfocus="input_style_back(this)" />
</div><br /><hr />

<input type="button" class="round4 x_bcolor_bg" style="width:100%;font-weight:bold;height:50px" value="<?php print $lang['next'];?>" onclick="check_form()" />
</form>
</div>

<script>
function check_form(){
f=document.forms[0];s='x_accent_bg s250';
a=f.user.value; f.user.value=a.replace(/[^a-z0-9]/gi,'');
if(f.user.value.trim().length<3){f.user.className=s;return false}
if(f.mail.value.trim().length<7){f.mail.className=s;return false}
if(f.mail.value.indexOf('@')==-1){f.mail.className=s;return false}
if(f.mail.value.indexOf('.')==-1){f.mail.className=s;return false}
if(f.mail.value.indexOf(' ')!=-1){f.mail.className=s;return false}
if(f.pass.value.trim().length<3){f.pass.className=s;return false}
if(f.ques.value.trim().length<1){f.ques.className=s;return false}
if(f.answ.value.trim().length<1){f.answ.className=s;return false}
document.forms[0].submit()}

function input_style_back(x){x.className='x_accent_bb s250'}

document.forms[0].reset()
window.onunload=function(){}
</script>
</body>
</html>
