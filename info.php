<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings(); $options=get_options(); get_language(); 

$info_url=''; $info_line='';
if(isset($_GET['q']) && $_GET['q']=='mtw'){$info_line=$lang['multi_conn'];}
if(isset($_GET['q']) && $_GET['q']=='rem'){$info_line=$lang['kicked_out'];}
if(isset($_GET['q']) && $_GET['q']=='ban'){$info_line=$lang['info_ban'];}
if(isset($_GET['q']) && $_GET['q']=='nop'){$info_line=$lang['error_noperms'];}

require_once('templates/info.pxtm'); 

?>