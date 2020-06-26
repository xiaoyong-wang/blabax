<?php require_once('lang_english.utf8'); ?>

<!DOCTYPE html>
<html lang="en">

<head><title><?php print $lang['installing'];?></title>
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
if(!function_exists('mysqli_query')){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<div class="x_bcolor_bg" style="padding:20px"><?php print $lang['err_msql'];?></div>
</div></body></html>
<?php die();}?>

<?php
if(!function_exists('json_decode')){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<div class="x_bcolor_bg" style="padding:20px"><?php print $lang['err_json'];?></div>
</div></body></html>
<?php die();}?>

<div class="holder">

<script>
self.location.href='step1.php'
</script>

</div>
</body>
</html>