<?
require('libs/helper.php');
if(!is_cli()){
	echo 'cli only'.PHP_EOL;
	exit(0);
}

if(!isset($argv[4])){
	echo 'cmd : '.PHP_EOL.'php '.basename(__FILE__).' host user password dabatase [mysql_lib_type(mysql,mysqli)]'.PHP_EOL;
	exit(0);
}
$host = $argv[1];
$user = $argv[2];
$password = $argv[3];
$database = $argv[4];
$mysql_lib_type = isset($argv[5])?$argv[5]:null;

$tmstr = date('ymdhis');
$filename = "[DB_SPEC]{$database}($tmstr)";
$download_name = 'output/'.$filename.'.xlsx';

require('libs/DbSpecification.php');
$dbspec = new DbSpecification();
$objPHPExcel = $dbspec->generateExcelObject($host,$user,$password,$database,$mysql_lib_type);
$dbspec->savefile($objPHPExcel,$download_name);
echo "save: {$download_name}".PHP_EOL;
exit;
