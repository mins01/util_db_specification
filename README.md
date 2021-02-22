# util_db_specification
* PHP를 사용한 DATABASE 명세서 생성 프로그램
* XLSX 파일로 만들어짐

# USE
## CLI
php cli.php host user password dabatase
## WEB
http://~~~/util_db_specification/index.php

# github
https://github.com/mins01/util_db_specification

## test version
* PHP 5.5.38 (linux)
* PHP 7.1.5 (WIN)

## require module
for PHPExcel
* PHP extension php_zip enabled 1)
* PHP extension php_xml enabled
* PHP extension php_gd2 enabled (if not compiled in)
* 1) php_zip is only needed by PHPExcel_Reader_Excel2007, PHPExcel_Writer_Excel2007,
   PHPExcel_Reader_OOCalc. In other words, if you need PHPExcel to handle .xlsx or .ods
   files you will need the zip extension, but otherwise not.

## 외부 라이브러리
PHPExcel ( LGPL )
