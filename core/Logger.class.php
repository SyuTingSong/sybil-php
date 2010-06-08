<?php
class Logger {
	private $name;
	private $delay;
	private $logs;
	private $pdo;
	public function __construct($name, $delayWrite=true) {
		$this->name = $name;
		$this->delay = $delayWrite;
		$this->logs = array();

		$this->pdo = new PDO('mysql:host=192.168.0.218;port=13300;dbname=logger', 'user', 'password', array(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8;'));
		$this->createTable();
	}
	protected function createTable() {
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS {$this->name} (time CHAR(24), message TEXT) ENGINE=MyISAM;");
	}
	protected function columns() {
		return "(time, message)";
	}
	public function __destruct() {
		if($this->delay) {
			$columns = $this->columns();
			$prefix = "INSERT INTO {$this->name} $columns VALUES ";
			foreach($this->logs as $log) {
				$values = "('".implode("','", $log)."')";
				$this->pdo->exec($prefix.$values);
				$error = $this->pdo->errorInfo();
			}
		}
	}
	public function log($msg) {
		list($us, $s) = explode(' ', microtime());
		$now = $s.substr($us, 1);
		$msg = mysql_escape_string($msg);
		if($this->delay) {
			array_push($this->logs, array($now, $msg));
		} else {
			$columns = $this->columns();
			$sql = "INSERT INTO {$this->name} {$columns} VALUES ('$now', '$msg');";
			$this->pdo->exec($sql);
		}
	}
}
?>