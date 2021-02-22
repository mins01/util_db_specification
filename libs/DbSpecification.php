<?
require(dirname(__FILE__).'/DbQuery.php');
include(dirname(__FILE__).'/PHPExcel/Classes/PHPExcel.php');

class DbSpecification{

	public function __construct()
	{

	}

	public function generateExcelObject($host,$user,$password,$database,$mysql_lib_type=null)
	{
		$tmstr = date('ymdhis');
		$filename = "[DB명세서]{$database}($tmstr)";
		$dbquery = new DbQuery($host,$user,$password,$database,$mysql_lib_type);

		//--- 동작 DBNAME 설정
		$v_dabatase = $dbquery->escape($database);
		$sql = "set @DBNAME:='{$v_dabatase}'";
		$rows = $dbquery->getRows($sql);
		// print_r($rows);

		//--- database 목록
		$sql = "SELECT a.TABLE_NAME,
		a.TABLE_COMMENT,
		a.CREATE_TIME
		FROM information_schema.TABLES a
		WHERE a.TABLE_SCHEMA = @DBNAME
		ORDER BY a.TABLE_NAME;";
		$rows = $dbquery->getRows($sql);
		// print_r($rows);
		$trowss = array();
		foreach($rows as $row){
			$trowss[$row['TABLE_NAME']] = array('table'=>$row,'columns'=>array());
		}
		// print_r($trowss);

		//--- columns
		$sql = "SELECT
		a.TABLE_NAME,
		b.ORDINAL_POSITION,
		b.COLUMN_NAME,
		b.DATA_TYPE,
		b.COLUMN_TYPE,
		b.COLUMN_KEY,
		b.IS_NULLABLE,
		b.EXTRA,
		b.COLUMN_DEFAULT,
		b.COLUMN_COMMENT
		FROM information_schema.TABLES a
		JOIN information_schema.COLUMNS b ON a.TABLE_NAME = b.TABLE_NAME AND a.TABLE_SCHEMA = b.TABLE_SCHEMA
		WHERE a.TABLE_SCHEMA = @DBNAME
		ORDER BY a.TABLE_NAME, b.ORDINAL_POSITION";
		$rows = $dbquery->getRows($sql);
		foreach($rows as $row){
			$TABLE_NAME = $row['TABLE_NAME'];
			unset($row['TABLE_NAME']);
			$trowss[$TABLE_NAME]['columns'][]=$row;
		}
		// print_r($trowss);

		// ============= 엑셀 가공부

		// $objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load("[def].xlsx");

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("created by db_specification")
		->setLastModifiedBy("created by db_specification")
		->setTitle("{$database} 명세서")
		->setSubject("{$database} 명세서")
		->setDescription("{$database} 명세서")
		->setKeywords("데이터베이스 명세서")
		->setCategory("데이터베이스 명세서");

		// 최초 시트에 기본 정보 기입
		$i0 = 0;
		$sheet = $objPHPExcel->getSheet($i0);
		$sheet->setTitle("{$database} 명세서");
		$sheet->setCellValue('C2', "{$database} 데이터베이스 명세서");
		$sheet->setCellValue('C5', "{$host}");
		$sheet->setCellValue('C6', "{$user}");
		$sheet->setCellValue('C7', "{$database}");
		$sheet->setCellValue('C9', date('Y-m-d H:i:s'));

		// 두번째 시트에 테이블 목록을 넣음
		//$objPHPExcel->createSheet(null);
		$border = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) ) );

		$i0++;
		$sheet = $objPHPExcel->getSheet($i0);
		$sheet->setTitle("테이블 목록");
		$icnt = 1;
		foreach ($trowss as $rs) {
			$icnt++;
			$sheet->setCellValue('A'.$icnt, ($icnt-1))
			->setCellValue('B'.$icnt, $rs['table']['TABLE_NAME'])
			->setCellValue('C'.$icnt, $rs['table']['TABLE_COMMENT'])
			->setCellValue('D'.$icnt, $rs['table']['CREATE_TIME']);
			$sheet->getStyle('A'.$icnt.':D'.$icnt)->applyFromArray($border);
			$sheet->getStyle('A'.$icnt.':D'.$icnt)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
		}
		// 세번째 시트부터 테이블 구조를 넣음
		$icnt = 1;
		foreach ($trowss as $rs) {
			// $sheet = $objPHPExcel->createSheet(null)->copy();
			$sheet = $objPHPExcel->getSheet(2)->copy();
			// $sheet->setTitle($r['table']['TABLE_NAME']);
			$t = sprintf('%03d',$icnt);
			$sheet->setTitle(iconv_substr("{$t} ".$rs['table']['TABLE_NAME'],0,30));
			$objPHPExcel->addSheet($sheet);
			$icnt++;

			$sheet->setCellValue('D1', $database);
			$sheet->setCellValue('G1', $rs['table']['TABLE_NAME']);
			$sheet->setCellValue('D2', $rs['table']['CREATE_TIME']);
			$sheet->setCellValue('G2', $rs['table']['TABLE_COMMENT']);


			$cicnt = 4;
			foreach ($rs['columns'] as $crows) {
				$sheet->setCellValue('A'.$cicnt, $crows['ORDINAL_POSITION'])
				->setCellValue('B'.$cicnt, $crows['COLUMN_NAME'])
				->setCellValue('C'.$cicnt, $crows['DATA_TYPE'])
				->setCellValue('D'.$cicnt, $crows['COLUMN_TYPE'])
				->setCellValue('E'.$cicnt, $crows['COLUMN_KEY'])
				->setCellValue('F'.$cicnt, $crows['IS_NULLABLE'])
				->setCellValue('G'.$cicnt, $crows['EXTRA'])
				->setCellValue('H'.$cicnt, $crows['COLUMN_DEFAULT'])
				->setCellValue('I'.$cicnt, $crows['COLUMN_COMMENT']);
				$sheet->getStyle('A'.$cicnt.':I'.$cicnt)->applyFromArray($border);
				$sheet->getStyle('A'.$cicnt.':I'.$cicnt)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				$cicnt++;
			}
			// $sheet->setTitle($r['table']['TABLE_NAME']);


		}
		//-- 불필요 부분 삭제
		$objPHPExcel->setActiveSheetIndex(0); //맨 처음 인덱스를 선택하도록
		$objPHPExcel->removeSheetByIndex(2); // TABLE 구조 시트 삭제
		return $objPHPExcel;
	}
	public function savefile($objPHPExcel,$download_name)
	{
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($download_name);

	}
}