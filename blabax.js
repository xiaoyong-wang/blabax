usr_online=false; mobile=0; ohash=''; dbid=0; ajnm=0; sound=0;  // usr_online[id]=[ext.id,group,name]
dragypos=0; dragdown=false; allowselect=0; autoscroll=1; globx=0; globy=0; chatflow=0
hoop_on=1; effects_on_mobile=1; obj2hoop=false; poststamp=0; current_status=1; freeaudio=1; usrinteaction=0
ignored_users=new Array('0'); pmnotifications=new Array('0'); uxtra_motto=new Array(); uxtra_avatar=new Array();  uxtra_expire=new Array(); phistorycached=new Array(); phistoryhidebtn=new Array();
srv_usr_id=0; ext_usr_id=0; active_usrname=''; avatars_loaded=0; div_roomhistory=false; div_userhistory=false; blockroomchange=0; blockuserchange=0;
ax_intv=false;pmarrnotify=[];load_history=0;

arr_rmb_rooms=new Array();
arr_rmb_users=new Array();

zone=new Date();zone=-zone.getTimezoneOffset();

try{dtitle=top.document.title.toString()}catch(e){dtitle=false}
if(typeof window.orientation !== 'undefined' && 'ontouchstart' in document.documentElement){mobile=1;usrinteaction=1}

function de(x){return document.getElementById(x)}

// ----------

function acntx(){usrinteaction=1;rcntx()} 

function rcntx(){
window.removeEventListener('click',acntx)
window.removeEventListener('wheel',acntx)
window.removeEventListener('keypress',acntx)
window.removeEventListener('touchstart',acntx)}

// ----------

mp3_ok=0; audf=document.createElement('audio')
if(audf.canPlayType && ''!=audf.canPlayType('audio/mpeg')){mp3_ok=1}

if(window.parent && typeof mobileapp!='undefined' && mobileapp>1){
function play_s(x){if(sound_on==1 && x>0 && sound_options[x]>0){parent.postMessage(x,'*')}}}
else{
function play_s(x){
if(mp3_ok<1 || sound_on<1 || usrinteaction<1 || x<1 || freeaudio<1 ||  sound_options[x]<1){return}
switch(x){
case 1: y=enter_mp3;break
case 2: y=messg_mp3;break
case 3: y=pmntf_mp3;break
case 4: y=bgmsg_mp3;break
case 5: y=pmmsg_mp3;break
default:y=false}

freeaudio=0; if(y && y.length>20){audf.src=y;audf.play()}; setTimeout('freeaudio=1',100)
}}

// ----------

function verti_pos(){
ph=de('middps').offsetHeight
dh=document.documentElement.clientHeight
offset=parseInt((dh-ph)/2)
if(offset>0){de('middps').style.marginTop=offset+'px'}}

// ----------

function shoop(x,y,z){ 
// z = time; y = scaleFactor: 1,2,3 etc
obj2unhoop=x; y=1-y*0.1
obj2unhoop.style.transform='scale('+y+','+y+')' 
setTimeout("obj2unhoop.style.transform='scale(1,1)';obj2unhoop.style.transform='rotate(0deg)'",z)}

// ----------

function cdispl(a){return a.currentStyle?a.currentStyle.display:getComputedStyle(a,null).display}

function clear_hoop(){
if(typeof opad1 == 'number'){clearTimeout(opad1)}
if(typeof opad2 == 'number'){clearTimeout(opad2)}
if(obj2hoop){
if(hoop_direction==1){obj2hoop.style.display='block';obj2hoop.style.opacity=1;}
if(hoop_direction==2){obj2hoop.style.display='none';obj2hoop.style.opacity=0;}
}}

function hoop(a,b){

if(typeof a!='object'){a=de(a)}

if((effects_on_mobile<1 && mobile>0) || hoop_on<1){
if(b>0){a.style.display='block'}else{a.style.display='none'}
return}

clear_hoop()

if(b==1 && cdispl(a)=='none'){
hoop_direction=1
a.style.display='block'; a.style.opacity=0
a.className=a.className.replace(' sfade0','')
obj2hoop=a
opad1=setTimeout("obj2hoop.style.opacity=1;obj2hoop.className+=' sfade1'",50)
opad2=setTimeout("obj2hoop=false",500)}

if(b==0 && cdispl(a)=='block'){
hoop_direction=2; a.style.opacity=0
a.className=a.className.replace(' sfade1','')
a.className+=' sfade0'; obj2hoop=a
opad1=setTimeout("obj2hoop.style.display='none'",400)
opad2=setTimeout("obj2hoop=false",500)}
}

// ----------

function init(){
display_layout_rsz(); window.onresize=display_layout_rsz;ax_ping(0); ax_status(welcome_msg)
if(mobile==1 && mobileapp<2 && sound_on>0){swap_panel(1);hst.style.display='block';pab.style.display='block';hoop(pan,1)}
}

// ----------

function ax_ping(msg){
if(typeof ax_intv == 'number'){clearInterval(ax_intv)}
rq='mtoken='+encodeURIComponent(mtoken)+'&msg='+encodeURIComponent(msg)+'&tousername='+encodeURIComponent(active_usrname)+'&status='+current_status+'&ohash='+ohash+'&dbid='+dbid+'&zone='+zone+'&ampm='+time_ampm+'&ajnm='+ajnm
ax_pulse=new XMLHttpRequest()
ax_pulse.open('post','ax_ping.php')
ax_pulse.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
ax_pulse.onreadystatechange=ax_ansr; ax_pulse.send(rq)
ax_intv=setInterval('ax_ping(0)',ping_period*1000)}

function ax_ansr(){
if(ax_pulse.readyState==4 && ax_pulse.status==200){
rcv=ax_pulse.responseText.toString()
if(rcv=='idehic'){gourl('./info.php?q=rem');return}

try{
rcv=JSON.parse(rcv); if(!rcv[4]){return}
if(rcv[4]<=ajnm){return}else{ajnm=rcv[4]}
ohash=rcv[2]; dbid=rcv[3]; sound=0
if(rcv[5]){console.log(rcv[5])}
if(rcv[0]){usr_online=rcv[0];online_construct();sound=1} 
rmsg=rcv[1];for(m in rmsg){msg_display(rmsg[m])}
play_s(sound)
}

catch(e){console.log(e)}
}}

// ----------

function ax_status(x){onl.innerHTML=''; log.innerHTML=x; if(load_history>0){load_hist_init()}}

// ----------

function msg_send(){
if(inp.value.trim().length>0){

if(inp.readOnly==true){inp.value='';return}

currentt=Math.floor(Date.now()/1000)
if(currentt-poststamp<postint || blockuserchange>0 || blockroomchange>0){
if(currentt-poststamp<postint){tmpspammsg=lang['no_spam']}else{tmpspammsg=lang['pwait']}
usrmsg=inp.value
inp.value=tmpspammsg
inp.disabled=true
setTimeout('inp.disabled=false;inp.value=usrmsg;inp_focus()',1000)
return}

msg={}
msg['to']=ext_usr_id
msg['color']=current_color
msg['text']=inp.value
msg['room']=current_room
msg=JSON.stringify(msg)

if(lof.style.display=='block'){if(srv_usr_id==0){hsp.style.display='none'} obj2hoop=false; lof.style.display='none'}

if(chatflow<1){ax_ping(msg)
if(ext_usr_id>0 && pm_reg>0){pm_reorder(ext_usr_id,active_usrname)}
}

inp.value=''
inp_focus()
ascroll()
poststamp=currentt}}

// ----------

function change_room(x){
if(typeof rooms[x]=='object' && x!=current_room && blockroomchange<1){
rmb_txt('r_hide')
current_room=x;log.innerHTML=split_room_content(x)
scrolllog(); play_s(4); ascroll(); recalc_msg()
btl.style.backgroundColor='#'+rooms[current_room][0]
log.className='bgbwsp rr'+x; rmb_txt('r_show')
}}

function split_room_content(x){
if(rooms[x][4]<=msgs2keep){return rooms[x][3]+rooms[x][1]}
else{
splitpoint=rooms[x][4]-msgs2keep+1
allmsg=rooms[x][3]+rooms[x][1]
allmsg=allmsg.split('<!--'+splitpoint+'-->')
if(allmsg[1]){rooms[x][1]=allmsg[1];return allmsg[1]}
else{return allmsg[0]}
}}

// ----------

function key_changer(e){
e=parseInt(e);troom=e-48
if(rcodes[troom]){change_room(rcodes[troom]);return}

if(e==39){
stpp=0;index=rcodes.indexOf(current_room)
for(i=1;i<rcodes.length;i++){
if(stpp==1){change_room(rcodes[i]);return}
if(i==index){stpp=1}} change_room(1)}

if(e==37){
stpp=0;index=rcodes.indexOf(current_room)
for(i=rcodes.length;i>0;i--){
if(stpp==1){change_room(rcodes[i]);return}
if(i==index){stpp=1}} change_room(rcodes[rcodes.length-1])}

}

// ----------

function change_status(s,t){
if(s!=current_status){
current_status=s
ax_ping(0)
de('curr_status_ico').className='pchat_notify status'+s
de('curr_status_nme').innerHTML=t
} hst.style.display='none'; hoop(ons,0); inp_focus()}

// ----------

function display_layout_rsz(){
dw=parseInt(window.innerWidth)
pc=parseInt(dw/35)
if(dw<400){
onx.className='online_title2 x_bcolor_z x_top_rounded'
onl.className='online_users2 x_bcolor_x x_bottom_rounded'
log.style.width='98%'
onl.style.display='none'
onx.style.display='none'
de('global_notify').className='pchat_notify'
}  else{
onx.className='online_title1 x_bcolor_z x_top_rounded'
onl.className='online_users1 x_bcolor_x x_bottom_rounded'
onl.style.display='block'
onx.style.display='block'
log.style.width=(dw-(onl.clientWidth+pc))+'px'
}scrolllog()}

// ----------

function display_layout_frs(x){
dw=parseInt(window.innerWidth)
pc=parseInt(dw/35)
if(x<1){
onl.style.display='none'
onx.style.display='none'
log.style.width='98%'
de('global_notify').className='pchat_notify'
} else{
onl.style.display='block'
onx.style.display='block'
if(dw>400){log.style.width=(dw-(onl.clientWidth+pc))+'px'}
}scrolllog()}

// ----------

function msg_format(x){
msgsticker=false
for(i in stickers){if(stickers[i]==x){msgsticker=1;x='<img src="'+x+'" class="chat_area_sticker" alt="" />'}}
if(msgsticker){return x}
x=repl_emoticons(x); x=repl_links(x)
return x}

// ----------

function msg_display(x){
msg2add='';

time=x['time']
clas=x['color'].toString()
name=escape_str(x['name'])
text=escape_str(x['text'])
pbox=parseInt(x['pbox'])
from=parseInt(x['fromid'])
gclr=parseInt(x['group'])
room=parseInt(x['room']); if(room>999998){room=current_room}
attach=parseInt(x['attach'])

for(i in ignored_users){if(ignored_users[i]==name){return}}

if(typeof badwordsfilter!=='undefined' && badwordsfilter.length>0){
for(i in badwordsfilter){regex=new RegExp('('+badwordsfilter[i]+')','gi'); text=text.replace(regex,'***')}}

text=msg_format(text)

htmlmsg=msg_template.replace('{TIME}',time).replace('{GROUP}','1').replace('{NAME}',name).replace('{UID}',from).replace('{COLOR}',clas).replace('{TEXT}',text)

if(from<1 || room>0){
rooms[room][4]++;
htmlmsg='<!--'+rooms[room][4]+'-->'+htmlmsg;
if(typeof rooms[room]=='object'){rooms[room][1]+=htmlmsg; rooms[room][2]++; if(bg_sound>0 && sound<1){sound=4}}
if(current_room==room){msg2add=htmlmsg; if(sound<2 || sound==4){sound=2}}
recalc_msg()}

else{

notify_now=0; if(pbox!=ext_usr_id){
de('global_notify').className='x_accent_bg pchat_notify_on'

notify_now=1; for(i in pmnotifications){if(pmnotifications[i]==pbox){notify_now=0}}
if(notify_now>0){
if(dtitle){top.document.title='☺ ⊶ ☺ '+name}
msg2add=msg_template.replace('{TIME}','').replace('{GROUP}','1').replace('{NAME}',lang['system']).replace('{UID}',0).replace('{COLOR}',0).replace('{TEXT}',lang['pmmsg']+' <b class="pointer x_accent_fg" onclick="show2user('+pbox+',this)">'+name+'</b>')

if(de('panel_pmlog').style.display!='block' || pan.style.display!='block'){
pmarrnotify_add(from); de('top2unread').style.display='block'; de('bot2unread').style.opacity=1.0;}
sound=3; pmnotifications.splice(1,0,pbox)}

tcss=de('e'+pbox).className
tcss=tcss.replace('pchat_notify_on','').replace('pchat_notify','').trim()
de('e'+pbox).className=tcss+' pchat_notify_on'}

pmlog=de('p'+pbox)
if(from!=myuuid){pm_reorder(from,name)}
pmlog.innerHTML =pmlog.innerHTML+htmlmsg
pmlog.scrollTop=pmlog.scrollHeight
if(notify_now<1){sound=5}}

// reduce msg
splitpoint=rooms[current_room][4]-msgs2keep+1
allmsg=log.innerHTML.split('<!--'+splitpoint+'-->')
if(allmsg[1]){msg2add=allmsg[1]+msg2add}else{msg2add=allmsg[0]+msg2add}
log.innerHTML=msg2add
scrolllog()}

//-----------

function show2user(x,y){tmpo=true;
hide_user();hsp.style.display='none'; 
if(lof.style.display=='block'){lof.style.display='none'}
for(i in usr_online){if(usr_online[i][0]==x){show_user(i,parseInt(usr_online[i][0]),usr_online[i][1],usr_online[i][2]);tmpo=false;break}}
if(tmpo && typeof y=='object'){if(y.children.length>0){shoop(y,1,200)}else{y.style.filter='grayscale(100%)'}}return tmpo}

// ----------

function pm_reorder(x,y){z=get_time();
pmele='<div id="pmd'+x+'" class="x_accent_bb pointer panel_pmitem" onclick="if(!show2user('+x+',this)){setTimeout(\'manage_esc()\',100)}">';
pmele+='<div class="panel_pmavtr" style="background-image:url(avatar.php?q='+x+')"></div>';
pmele+='<b>'+y+'</b><br /><small>'+z+'</small><br style="clear:both" /></div>';
if(de('pmd'+x)){de('pmd'+x).parentNode.removeChild(de('pmd'+x))}
s=de('pmlog_container').innerHTML;de('pmlog_container').innerHTML=pmele+s}

// ----------

function recalc_msg(){
u=0; rooms[current_room][2]=0
for(i in rooms){if(typeof rooms[i]=='object'){u+=rooms[i][2]; de('unr'+i).innerHTML=rooms[i][2]}}
if(u>0){de('top_unread').style.display='block';de('bot_unread').style.opacity=1.0}else{de('top_unread').style.display='none';de('bot_unread').style.opacity=0.0}
de('unr'+current_room).innerHTML='&#9733;'
}

// ----------

function online_construct(){

if(typeof Object.assign!='function'){uo=usr_online}
else{uo=Object.assign(usr_online,fls_online)}

onl_str=new Array(); usr_length=0
uinonlinebg='x_accent_bg x_top_rounded'


for(i in uo){

// bg pchat online/offline	
if(ext_usr_id==uo[i][0]){uinonlinebg='x_bcolor_x x_top_rounded'}

ntfy='status'+uo[i][3]

// preserve notifications
ntsg=de('e'+uo[i][0])
if(ntsg && ntsg.className.indexOf('pchat_notify_on')>-1){ntfy+=' pchat_notify_on';}
else{ntfy+=' pchat_notify'}

linethrough=''; // strike ignored users
for(j in ignored_users){if(uo[i][2]==ignored_users[j]){linethrough=' style="text-decoration:line-through"';}}

sortbyname=uo[i][2].toLowerCase()
onl_str.push('<!-- '+sortbyname+' --><div class="single_online_user"><span id="e'+uo[i][0]+'" class="'+ntfy+'"></span><span'+linethrough+' class="g'+uo[i][1]+'" onclick="show_user('+i+','+uo[i][0]+','+uo[i][1]+',\''+uo[i][2]+'\')">'+uo[i][2]+'</span></div>')

// append pchat div
p=document.createElement('div')
pu='p'+uo[i][0]
if(!de(pu)){
p.id=pu; p.className='one2chat x_bottom_rounded x_bcolor_x svg_pmpm'
pch.appendChild(p)}
usr_length+=1}

uin.className=uinonlinebg
onl_str.sort()
onl_str=onl_str.join(' ')
onl.innerHTML=onl_str}

// ----------

function show_user(a,b,c,d){

pmarrnotify_rem(b);
if(pmarrnotify.length<1)(hidepm_notify())

if(dtitle){top.document.title=dtitle}
hsp.style.display='block'
uin.style.display='block'
de('user_buttons').style.display='block'

tcss=de('e'+b).className
tcss=tcss.replace('pchat_notify_on','').replace('pchat_notify','').trim()
de('e'+b).className=tcss+' pchat_notify'

m=de('p'+b)
m.style.display='block'; m.scrollTop=m.scrollHeight
srv_usr_id=a; ext_usr_id=b; active_usrname=d
ignore_init()
inp.placeholder=' @'+d
unn.innerHTML=d
uin.className='x_bcolor_x x_top_rounded'

if(typeof uxtra_expire[ext_usr_id]=='number' && uxtra_expire[ext_usr_id]>Math.floor(Date.now()/1000)){uxtra_init(ext_usr_id)}
else{
de('user_avatar').style.backgroundImage='none'
de('user_motto').innerHTML=''
uxtra_snd()}

if(phistoryhidebtn.indexOf(ext_usr_id)==-1){phistoryhidebtn.push(ext_usr_id);m.innerHTML=privhistbutton+m.innerHTML;}
if(stealth>0){inp.placeholder=' '+lang['stlth'];inp.value='';inp.readOnly=true}
rmb_txt('u_show'); inp_focus()}

// ----------

function uxtra_snd(){
ajax_uxt=new XMLHttpRequest()
ajax_uxt.open('get','user.php?id='+ext_usr_id)
ajax_uxt.onreadystatechange=uxtra_ans
ajax_uxt.send()}

function uxtra_ans(){
if(ajax_uxt.readyState==4 && ajax_uxt.status==200){
res=ajax_uxt.responseText.toString()
res=res.split('|')
if(res[2]){
uxtra_motto[res[0]]=res[1]
uxtra_avatar[res[0]]=res[2]
uxtra_expire[res[0]]=Math.floor(Date.now()/1000)+1200
uxtra_init(res[0])}}}

// ----------

function uxtra_init(x){
if(uxtra_motto[x] && uxtra_avatar[x]){
de('user_avatar').style.backgroundImage='url('+uxtra_avatar[x]+')'
if(uxtra_motto[x].length<33){sh_motto=uxtra_motto[x]}
else{sh_motto=uxtra_motto[x].substr(0,30);sh_motto=sh_motto+'...'}

de('user_motto').innerHTML=sh_motto
de('user_motto').title=uxtra_motto[x]
}}


// ----------

function hide_user(){
if(!ext_usr_id){return}
pu='p'+ext_usr_id;rmb_txt('u_hide')
de('kick_ban').style.display='none'
de('user_buttons').style.display='none'
de(pu).style.display='none'
uin.style.display='none'
for(i in pmnotifications){if(pmnotifications[i]==ext_usr_id){pmnotifications.splice(i,1)}}
srv_usr_id=0; ext_usr_id=0; active_usrname=''
inp.placeholder=' '+lang['message'];inp.readOnly=false;inp_focus()}

// ----------

function ignore_init(){
emute='x_circle x_bcolor_z pointer svg_mute'
dmute='x_circle x_bcolor_z pointer svg_unmt'
tmp=emute
for(i in ignored_users){if(ignored_users[i]==active_usrname){tmp=dmute}}
de('ig_unig').className=tmp
}

// ----------

function ignore_set(){
emute='x_circle x_bcolor_z pointer svg_mute'
dmute='x_circle x_bcolor_z pointer svg_unmt'
pss=false; p=de('ig_unig')
for(i in ignored_users){if(ignored_users[i]==active_usrname){pss=i}}
if(!pss){ignored_users.splice(1,0,active_usrname);p.className=dmute}
else{ignored_users.splice(pss,1);p.className=emute}
online_construct();save_cookie(ignored_cookie_name,ignored_cookie_length,JSON.stringify(ignored_users))}

// ----------

function ban_user(b){
ajax_bus=new XMLHttpRequest()
x='id='+ext_usr_id+'&mod='+b
ajax_bus.open('post','ban.php')
ajax_bus.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
ajax_bus.onreadystatechange=ban_ans;ajax_bus.send(x)
if(lof.style.display!='block'){hsp.style.display='none'}
setTimeout('ax_ping(0)',500);hide_user()}

function ban_ans(){
if(ajax_bus.readyState==4 && ajax_bus.status==200){
response=ajax_bus.responseText.toString()
//console.log(response)
}}

// ----------

function load_avatars(){
if(avatars_loaded<1){
ajax_avt=new XMLHttpRequest()
ajax_avt.open('get','mavatar.php?list=1')
ajax_avt.onreadystatechange=avatars_ans
ajax_avt.send()}}

function avatars_ans(){
if(ajax_avt.readyState==4 && ajax_avt.status==200){
response=ajax_avt.responseText.toString()
de('avatar_list').innerHTML=response
avatars_loaded=1}}

// ----------

function gourl(x){self.location.href=x}

function wopen(x,isurl){if(mobileapp<1){nw=window.open();nw.opener=null;nw.location.href=x}else if(isurl>0){prompt('URL',x);}}

// ----------

function hidepm_notify(){
pmarrnotify=[]; if(dtitle){top.document.title=dtitle}
de('top2unread').style.display='none';de('bot2unread').style.opacity=0.0}

function pmarrnotify_add(x){x=parseInt(x);
if(pmarrnotify.indexOf(x)<0){pmarrnotify.push(x)}}

function pmarrnotify_rem(x){x=parseInt(x);
if(pmarrnotify.indexOf(x)>-1){pmarrnotify.splice(pmarrnotify.indexOf(x),1)}}

// ----------

function manage_esc(){

if(blockuserchange>0){return}

if(ons.style.display=='block'){
hst.style.display='none';hoop(ons,0);inp_focus();return}

if(pan.style.display!='none'){
hst.style.display='none';pab.style.display='none';hoop(pan,0);inp_focus();return}

if(srv_usr_id!=0){hide_user(); hsp.style.display='none'
if(mobile<1 && lof.style.display=='block'){hoop(lof,0)}
if(mobile>0 && lof.style.display=='block'){lof.style.display='none'}
inp_focus();return}

else if(lof.style.display=='block'){
if(mobile<1){hoop(lof,0)}else{lof.style.display='none'}
hsp.style.display='none';inp_focus();return}

if(autoscroll<1){ascroll();inp_focus();return}

if(de('panel_pmlog').style.display=='block'){hidepm_notify()}
hst.style.display='block';pab.style.display='block';hoop(pan,1)}

// ----------

function manage_wfocus(x){ if(mobile<1){
if(x>0){de('hidescreen_blur').style.display='none'}
else{de('hidescreen_blur').style.display='block'}}}

// ----------

function swap_panel(x){
s0=de('panel_rooms')
s1=de('panel_settings')
s2=de('panel_profile')
s3=de('panel_avatar')
s4=de('panel_ctab')
s5=de('panel_pmlog')

switch(x){
case 0:s0.style.display='block'; s1.style.display='none'; s2.style.display='none'; s3.style.display='none'; s4.style.display='none'; s5.style.display='none';break
case 1:s0.style.display='none'; s1.style.display='block'; s2.style.display='none'; s3.style.display='none'; s4.style.display='none'; s5.style.display='none';break
case 2:s0.style.display='none'; s1.style.display='none'; s2.style.display='block'; s3.style.display='none'; s4.style.display='none'; s5.style.display='none';break
case 3:s0.style.display='none'; s1.style.display='none'; s2.style.display='none'; s3.style.display='block'; s4.style.display='none'; s5.style.display='none';break
case 4:s0.style.display='none'; s1.style.display='none'; s2.style.display='none'; s3.style.display='none'; s4.style.display='block'; s5.style.display='none';break
case 5:s0.style.display='none'; s1.style.display='none'; s2.style.display='none'; s3.style.display='none'; s4.style.display='none'; s5.style.display='block';hidepm_notify();break
}}

// ----------

function swap_ampm(u){
x=de('ampm')
if(time_ampm==2){x.value='am/pm';time_ampm=1;if(u>0){settings_snd('ampm',time_ampm)} return}
if(time_ampm==1){x.value='···';  time_ampm=0;if(u>0){settings_snd('ampm',time_ampm)} return}
if(time_ampm==0){x.value=' 24H ';  time_ampm=2;if(u>0){settings_snd('ampm',time_ampm)} return}
}

// ----------

function swap_sound(u){ x=de('sndd');
if(sound_on==1){x.className='panel_button x_bcolor_z x_all_rounded svg_soff';sound_on=0;if(u>0){settings_snd('sound',sound_on)} return}
if(sound_on==0){x.className='panel_button x_bcolor_z x_all_rounded svg_sndd';sound_on=1;if(u>0){settings_snd('sound',sound_on)} return}
}

function sound_opt_init(){
stonn='pointer x_bcolor_z x_left_rounded svg_sndd'
stoff='pointer x_overal x_left_rounded svg_soff'

for(i=0;i<10;i++){acti='snd'+i+'oo'
if(de(acti)){
if(sound_options[i]>0){de(acti).className=stonn}
else{de(acti).className=stoff}
}}}

function sound_opt(opt){
stonn='pointer x_bcolor_z x_left_rounded svg_sndd'
stoff='pointer x_overal x_left_rounded svg_soff'
acti='snd'+opt+'oo';
if(sound_options[opt]>0){sound_options[opt]=0;de(acti).className=stoff}
else{if(sound_on<1){swap_sound(1)};sound_options[opt]=1;de(acti).className=stonn;play_s(opt)}
cookieval=sound_options.join('z'); save_cookie(soundop_cookie_name,soundop_cookie_length,cookieval)}

// ----------

function swap_pmreg(u,l_on,l_off){
x=de('pmreg')
if(pm_reg==1){pm_reg=0; x.value=l_off; if(u>0){settings_snd('pmreg',pm_reg)} return}
if(pm_reg==0){pm_reg=1; x.value=l_on;  if(u>0){settings_snd('pmreg',pm_reg)} return}
}


// ----------

function swap_color(x,y,u){
current_color=x; inp.style.color=y
if(u>0){
settings_snd('color',current_color)
hst.style.display='none';pab.style.display='none';hoop(pan,0);inp_focus()}
}

// ----------

function settings_snd(x,y){
ajax_stt=new XMLHttpRequest()
x=x+'='+y
ajax_stt.open('post','index.php')
ajax_stt.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
ajax_stt.onreadystatechange=settings_ans;ajax_stt.send(x)}

function settings_ans(){
if(ajax_stt.readyState==4 && ajax_stt.status==200){response=ajax_stt.responseText.toString()
if(response.length>1){s=self.location.href.toString();gourl(s)}
}}


// ----------

function escape_str(x){
x=x.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#145;').replace(/"/g, '&quot;')
return x}

// ----------

function replace_all(s, f, r) {return s.replace(new RegExp(f, 'g'), r);}

// ----------

function repl_emoticons(x){
for(key in emos){if (typeof emos[key] !== 'function') {
x=replace_all(x,key,'<span class="'+emos[key]+' chat_area_emoticon"></span>')
}}return x}

// ----------

function repl_links(x){
pattern = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim
x = x.replace(pattern,'<span style="text-decoration:underline" class="pointer x_accent_fg" onclick="wopen(\'$1\',1)">$1</span>')
return x}

// ----------

function emo2input(x){
inp.value=inp.value+' '+x+' '
inp_focus()
}

function add_sti(x){inp.value=x; msg_send(); inp_focus();}


// ----------


function inp_focus(){if(mobile<1 && chatflow<1 && inp.readOnly==false){inp.focus()}}

// ----------

function user_check_form(){
f=document.forms[1]
s='x_accent_bg x_accent_bb panel_input'

if(f.newpass.value.trim().length<3){
f.newpass.value='';f.retype.value='';f.oldpass.value=''
de('rt').style.display='none'}
else{hoop('rt',1)}

if(f.email.value.trim()==f.dbmail.value){
f.email.value=f.dbmail.value; f.answer.value=''
de('qa').style.display='none'}
else{hoop('qa',1)}

if(f.newpass.value.trim().length>2){
if(f.newpass.value!=f.retype.value){f.retype.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.oldpass.value.trim().length<3){f.oldpass.className=s;shoop(de('prosubbutton'),2,200);return false}}

if(f.email.value.trim()!=f.dbmail.value){
if(f.email.value.trim().length<7) {f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf('@')==-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf('.')==-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf(' ')!=-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.answer.value.trim().length<1){f.answer.className=s;shoop(de('prosubbutton'),2,200);return false}}

if(f.newpass.value.trim().length<3 && f.email.value.trim()==f.dbmail.value){shoop(de('prosubbutton'),2,200);return false}

return true}

// ----------

function guest_check_form(){
f=document.forms[1]
s='x_accent_bg x_accent_bb panel_input'

if(f.password.value.trim().length<3){f.password.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.password.value!=f.retype.value){f.retype.className=s;shoop(de('prosubbutton'),2,200);return false}

if(f.email.value.trim().length<7) {f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf('@')==-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf('.')==-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.email.value.indexOf(' ')!=-1){f.email.className=s;shoop(de('prosubbutton'),2,200);return false}

if(f.question.value.trim().length<1){f.question.className=s;shoop(de('prosubbutton'),2,200);return false}
if(f.answer.value.trim().length<1){f.answer.className=s;shoop(de('prosubbutton'),2,200);return false}
return true}

// ----------

function panput_style_back(x,y){x.className='x_bcolor_y x_accent_bb panel_input '+y}


// ---------- scrolling

function nselect(x){
if(allowselect<1 && x>0){
allowselect=1;log.style.cursor='auto'}
else{allowselect=0;log.style.cursor='ns-resize'
if(document.selection){document.selection.empty()}
if(window.getSelection){window.getSelection().removeAllRanges()}}}

function scrolllog(){if(autoscroll>0){log.scrollTop=log.scrollHeight}}

function ascroll(){if(autoscroll<1){hoop(aus,0);autoscroll=1;scrolllog();inp_focus()}}

function mdown(x){if(allowselect<1 && typeof x.preventDefault=='function'){
x.preventDefault()}dragdown=true;dragypos=x.pageY}

function mmove(x,y){
if(dragdown==true && allowselect<1 && dragypos){
x.scrollTop+=((dragypos-y.pageY)/6)
if(autoscroll<1 && rbotto() && dragdown==true){hoop(aus,0);autoscroll=1;return}
if(autoscroll>0 && !rbotto() && dragdown==true){hoop(aus,1);autoscroll=0}}}

function wmove(e){
if(parseInt(e.deltaY)>0){z=15}else{z=-15} log.scrollTop+=z
if(autoscroll<1 && rbotto()){hoop(aus,0);autoscroll=1;return}
if(autoscroll>0 && !rbotto()){hoop(aus,1);autoscroll=0}}

function keyscroll(m){
if(m!=38 && m!=40){return}
if(m==38){log.scrollTop-=15} if(m==40){log.scrollTop+=15}
if(autoscroll<1 && rbotto()){hoop(aus,0);autoscroll=1;return}
if(autoscroll>0 && !rbotto()){hoop(aus,1);autoscroll=0}}

function m2down(x){if(typeof x.preventDefault=='function'){x.preventDefault();dragdown=true;dragypos=x.pageY}}
function m2move(x,y){if(dragdown==true && dragypos){x.scrollTop+=((dragypos-y.pageY)/6);}}
function w2move(e){if(parseInt(e.deltaY)>0){z=15}else{z=-15} onl.scrollTop+=z}

function rbotto(){
if(parseInt(log.scrollHeight-log.scrollTop)<=log.clientHeight){return true}
else{return false}}

// ---------- touches

function ttouch1(e){
tj = e.changedTouches[0]
globx = parseInt(tj.clientX)
globy = parseInt(tj.clientY)
}

// ----------

function ttouch2(e){
tj = e.changedTouches[0]; currx = parseInt(tj.clientX); curry = parseInt(tj.clientY)

if(globx<50 && (currx-globx)>50){if(de('panel_pmlog').style.display=='block'){
hidepm_notify()} hst.style.display='block';pab.style.display='block';hoop(pan,1);return}

if((curry-globy)>50 || (globy-curry)>50){
if(autoscroll>0 && !rbotto()){autoscroll=0;hoop(aus,1)}
setTimeout('if(autoscroll<1 && rbotto()){hoop(aus,0);autoscroll=1}',300)}
}

// ----------

function ext_profile(){
de('user_avatar').style.transform='rotateX(180deg)'
setTimeout("de('user_avatar').style.transform='rotateX(0deg)'",300)
}

// ----------

function show_r_history(x){
if(rooms[current_room][3].length>0){return}
blockroomchange=1
div_roomhistory=x;x.className='';x.innerHTML=lang['pwait']
ajax_get_rmsg=new XMLHttpRequest()
theurl='msgfetch.php?room='+current_room+'&mtoken='+encodeURIComponent(mtoken)+'&tpoi='+tpoint+'&zone='+zone+'&ampm='+time_ampm
ajax_get_rmsg.open('get',theurl)
ajax_get_rmsg.onreadystatechange=rcv_r_history
ajax_get_rmsg.send()}

// ----------

function rcv_r_history(){
if(ajax_get_rmsg.readyState==4){blockroomchange=0; div_roomhistory.innerHTML=''}
if(ajax_get_rmsg.readyState==4 && ajax_get_rmsg.status==200){
rcv=ajax_get_rmsg.responseText.toString()
msgfromdb=multimsg_prepare(rcv)
rooms[current_room][1]=rooms[current_room][1].replace(roomhistbutton,'')
rooms[current_room][3]=msgfromdb
div_roomhistory.innerHTML=msgfromdb
}}

// ----------

function show_p_history(x){
if(phistorycached.indexOf(ext_usr_id)!=-1){return}
blockuserchange=1
phistorycached.push(ext_usr_id)
div_userhistory=x;x.className='';x.innerHTML=lang['pwait']
ajax_get_pmsg=new XMLHttpRequest()
theurl='msgfetch.php?tuid='+ext_usr_id+'&mtoken='+encodeURIComponent(mtoken)+'&tpoi='+tpoint+'&zone='+zone+'&ampm='+time_ampm
ajax_get_pmsg.open('get',theurl)
ajax_get_pmsg.onreadystatechange=rcv_p_history
ajax_get_pmsg.send()}

// ----------

function rcv_p_history(){
if(ajax_get_pmsg.readyState==4){blockuserchange=0; div_userhistory.innerHTML=''}
if(ajax_get_pmsg.readyState==4 && ajax_get_pmsg.status==200){
rcv=ajax_get_pmsg.responseText.toString()
msgfromdb=multimsg_prepare(rcv)
div_userhistory.innerHTML=msgfromdb
}}


// ----------

function multimsg_prepare(r){
try{

r=JSON.parse(r)
msgfromdb=new Array()

for(w in r){
h_id=parseInt(r[w]['id'])
h_color=parseInt(r[w]['color'])
h_usrid=parseInt(r[w]['uid'])
h_time=escape_str(r[w]['time'])
h_uname=escape_str(r[w]['name'])
h_text=escape_str(r[w]['text'])
h_text=msg_format(h_text)

if(typeof badwordsfilter!=='undefined' && badwordsfilter.length>0){
for(i in badwordsfilter){regex=new RegExp('('+badwordsfilter[i]+')','gi'); h_text=h_text.replace(regex,'***')}}

msgft=msg_template.replace('{TIME}',h_time).replace('{GROUP}','1').replace('{NAME}',h_uname).replace('{UID}',h_usrid).replace('{COLOR}',h_color).replace('{TEXT}',h_text)
msgfromdb[h_id]=msgft
}

msgfromdb=msgfromdb.join(' '); 
if(msgfromdb.length<1){msgfromdb=lang['nomsg'];}
return msgfromdb

}catch(e){console.log(e)}}

// ----------

function save_cookie(cname,clength,cvalue){
var expdate = new Date(); expdate.setTime(expdate.getTime() + clength*1000);
expdate = "; expires=" + expdate.toUTCString();cvalue=encodeURIComponent(cvalue);
document.cookie = cname + "=" + cvalue + expdate + "; path=/";}

// ----------

function get_time(){
if(time_ampm==0){return ''}
d=new Date
if(time_ampm==2){h=d.getHours()} // 24h
if(time_ampm==1){h = d.getHours()%12||12} // 12h
h=('0'+h).slice(-2)
m=('0'+d.getMinutes()).slice(-2)
s=('0'+d.getSeconds()).slice(-2)
return h+':'+m+':'+s}

// ----------

function rmb_txt(w){
if(!rmb_unsent){return}
t=inp.value;

if(w=='u_hide'){arr_rmb_users[ext_usr_id]=t
if(arr_rmb_rooms[current_room]){inp.value=arr_rmb_rooms[current_room]}
else{inp.value=''}}

if(w=='u_show'){
arr_rmb_rooms[current_room]=t
if(arr_rmb_users[ext_usr_id]){
inp.value=arr_rmb_users[ext_usr_id]}
else{inp.value=''}}

if(w=='r_hide' && ext_usr_id==0){arr_rmb_rooms[current_room]=t}

if(w=='r_show' && ext_usr_id==0){
if(arr_rmb_rooms[current_room]){inp.value=arr_rmb_rooms[current_room]}
else{inp.value=''}}
}

// ----------

function b64e(str) {
return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,function(match, p1){
return String.fromCharCode(parseInt(p1,16))}))}

function b64d(str){
return decodeURIComponent(Array.prototype.map.call(atob(str),function(c){
return '%'+('00'+c.charCodeAt(0).toString(16)).slice(-2)}).join(''))}

// ----------

function load_hist_init(){
setTimeout("show_r_history(de('welcome_msg'))",600)
setTimeout("scrolllog()",800);setTimeout("scrolllog()",1200)}

// ----------