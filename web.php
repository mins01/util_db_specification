<?
require('libs/helper.php');
if(is_cli()){
	echo 'web only'.PHP_EOL;
	exit(0);
}
if(!isset($_POST['host'][0]) || !isset($_POST['user'][0]) || !isset($_POST['pw'][0]) || !isset($_POST['database'][0])){
	header('HTTP/1.1 400 Bad Request', true, 400);
	exit(0);
}

$host = $_POST['host'];
$user = $_POST['user'];
$password = $_POST['pw'];
$database = $_POST['database'];
$mysql_lib_type = isset($argv[5])?$argv[5]:null;

$tmstr = date('ymdhis');
$filename = "[DB_SPEC]{$database}($tmstr)";
$download_name = $filename.'.xlsx';

require('libs/DbSpecification.php');
$dbspec = new DbSpecification();
$objPHPExcel = $dbspec->generateExcelObject($host,$user,$password,$database,$mysql_lib_type);
$dbspec->downloadfile($objPHPExcel,$download_name);
// echo "save: {$download_name}".PHP_EOL;
exit;
