<?

class DbQuery{
	// private $host = '';
	// private $id = '';
	// private $pw = '';
	// private $db = '';

	private $mysqli = null;
	private $mysql = null;
	private $mysqlLibType = ''; //mysql , mysqli

	function __construct($host,$user,$password,$database,$mysql_lib_type=null){
		$this->connect($host,$user,$password,$database,$mysql_lib_type);
	}

	function connect($host,$user,$password,$database,$mysql_lib_type=null){
		if($mysql_lib_type==null && class_exists('Mysqli') || $mysql_lib_type=='mysqli'){
			$this->mysqli =  new mysqli($host,$user,$password,$database);
			$this->mysqli->set_charset('utf8');
		}else if($mysql_lib_type==null && function_exists('mysql_connect')|| $mysql_lib_type=='mysql'){
			$this->mysql =  mysql_connect($host,$user,$password);
			mysql_set_charset('utf8');
			mysql_select_db($database,$this->mysql);
		}else{
			exit('Not Supported mysql_*, Mysqli!');
		}
	}
	function escape($str){
		if(isset($this->mysqli)){
			return $this->mysqli->real_escape_string($str);
		}else if(isset($this->mysql)){
			return mysql_real_escape_string($str,$this->mysql);
		}else{
			exit(__METHOD__.'Not Supported mysql_*, Mysqli!');
		}
	}
	function getRows($sql){
		$rows = array();
		if(isset($this->mysqli)){
			$rs = $this->mysqli->query($sql);
			// var_dump($rs);
			if($this->mysqli->error){
				printf($this->mysqli->error);
				exit(0);
			}else if($rs===true){
			}else{
				while ($row = $rs->fetch_assoc()) {
				 $rows[] = $row;
				}
			}
		}else if(isset($this->mysql)){
			$rs = mysql_query($sql,$this->mysql);
			while ($row = mysql_fetch_assoc($rs)) {
			 $rows[] = $row;
			}
		}else{
			exit('Not Supported mysql_*, Mysqli!');
		}
		return $rows;

	}

}