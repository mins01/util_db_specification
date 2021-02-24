<?
require(dirname(__FILE__).'/DbQuery.php');
include(dirname(__FILE__).'/PHPExcel/Classes/PHPExcel.php');

class DbSpecification{

	public function __construct()
	{

	}

	public function getSheetName($icnt,$TABLE_NAME){
		$t = sprintf('%03d',$icnt);
		return iconv_substr("{$t})".$TABLE_NAME,0,30);
	}
	public function generateExcelObject($host,$user,$password,$database,$mysql_lib_type=null)
	{
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
		#b.DATA_TYPE,
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
		->setTitle("{$database} 정의서")
		->setSubject("{$database} 정의서")
		->setDescription("{$database} 정의서")
		->setKeywords("데이터베이스 정의서")
		->setCategory("데이터베이스 정의서");

		//-- 기본 설정
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10); //기본크기 10pt
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true); //자동 줄 바꿈

		// 최초 시트에 기본 정보 기입
		$i0 = 0;
		$sheet = $objPHPExcel->getSheet($i0);
		// $sheet->setTitle("{$database} 정의서"); //"표지"로 고정함
		$sheet->setCellValue('C4', "데이터베이스 {$database} 정의서");
		$sheet->setCellValue('C7', "{$host}");
		$sheet->setCellValue('C8', "{$user}");
		$sheet->setCellValue('C9', "{$database}");
		$sheet->setCellValue('C11', date('Y-m-d H:i:s'));

		// 두번째 시트에 테이블 목록을 넣음
		//$objPHPExcel->createSheet(null);
		$borderStyle = array(
			'borders' => array(
				'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ),
				'color' => array('rgb' => '000000'),
			)
		);
		$linkStyle = array(
		    'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => '0000ff'),
						"underline" => true,
		    ));

		$i0++;
		$sheet = $objPHPExcel->getSheet($i0);
		$sheet->setTitle("테이블 목록");
		$icnt = 1;
		foreach ($trowss as $rs) {
			$icnt++;
			$sheet->setCellValue('A'.$icnt, ($icnt-1))
			->setCellValue('B'.$icnt, $rs['table']['TABLE_NAME'])
			->setCellValue('C'.$icnt, $rs['table']['TABLE_COMMENT']);
			// ->setCellValue('D'.$icnt, $rs['table']['CREATE_TIME']); //미사용로 바뀜
			$sheet->getStyle('A'.$icnt.':D'.$icnt)->applyFromArray($borderStyle);
			$sheet->getStyle('A'.$icnt.':D'.$icnt)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

			$t = $this->getSheetName(($icnt-1),$rs['table']['TABLE_NAME']);
			$sheet->getCell('B'.$icnt)->getHyperlink()->setUrl("sheet://'{$t}'!A1");
			$sheet->getStyle('B'.$icnt.':B'.$icnt)->applyFromArray($linkStyle);


		}
		// 세번째 시트부터 테이블 구조를 넣음
		$icnt = 1;
		foreach ($trowss as $rs) {
			// $sheet = $objPHPExcel->createSheet(null)->copy();
			$sheet = $objPHPExcel->getSheet(2)->copy();
			// $sheet->setTitle($r['table']['TABLE_NAME']);

			$t = $this->getSheetName($icnt,$rs['table']['TABLE_NAME']);
			$sheet->setTitle($t);
			$objPHPExcel->addSheet($sheet);
			$icnt++;

			// $sheet->setCellValue('D1', $database);
			$sheet->setCellValue('C4', $rs['table']['TABLE_NAME']);
			// $sheet->setCellValue('D2', $rs['table']['CREATE_TIME']);
			$sheet->setCellValue('H4', $rs['table']['TABLE_COMMENT']);


			$cicnt = 7;
			foreach ($rs['columns'] as $crows) {
				$sheet->setCellValue('A'.$cicnt, $crows['ORDINAL_POSITION'])
				->setCellValue('B'.$cicnt, $crows['COLUMN_NAME'])
				// ->setCellValue('C'.$cicnt, $crows['DATA_TYPE'])
				->setCellValue('D'.$cicnt, $crows['COLUMN_TYPE'])
				->setCellValue('E'.$cicnt, $crows['COLUMN_KEY'])
				->setCellValue('F'.$cicnt, $crows['IS_NULLABLE'])
				->setCellValue('G'.$cicnt, $crows['EXTRA'])
				->setCellValue('H'.$cicnt, $crows['COLUMN_DEFAULT'])
				->setCellValue('I'.$cicnt, $crows['COLUMN_COMMENT']);
				$sheet->getStyle('A'.$cicnt.':I'.$cicnt)->applyFromArray($borderStyle);
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
	public function downloadfile($objPHPExcel,$download_name)
	{
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Content-Disposition: attachment;filename="'.rawurlencode($download_name).'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		// header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$this->savefile($objPHPExcel,'php://output');
	}
}
