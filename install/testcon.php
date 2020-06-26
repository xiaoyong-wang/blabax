<?php 

if(!isset($_POST['dbhost']) || !isset($_POST['dbname']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass']) || !isset($_POST['dbcset']) || !isset($_POST['dbsock'])){print '0';die();}

function process_error($x){print $x;die();}

$dbss=array();
$dbss['host']=trim($_POST['dbhost']);
$dbss['name']=trim($_POST['dbname']);
$dbss['user']=trim($_POST['dbuser']);
$dbss['pass']=trim($_POST['dbpass']);
$dbss['cset']=trim($_POST['dbcset']);
$dbss['sock']=trim($_POST['dbsock']);

require_once '../incl/mysqli_functions.php';

@neutral_dbconnect(); 
if(neutral_query('SET NAMES '.$dbss['cset'])){print 'ok';}

?>