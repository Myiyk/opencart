<?php
namespace DB;

use App\Bridges\DatabaseTracy\ConnectionPanel;

final class MySQLi {
	private $link;

	public function __construct($hostname, $username, $password, $database, $port = '3306') {
		$this->link = new \mysqli($hostname, $username, $password, $database, $port);

		if ($this->link->connect_error) {
			trigger_error('Error: Could not make a database link (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
			exit();
		}

		ConnectionPanel::info('mysqli', $this->link->host_info, $this);

		$this->link->set_charset("utf8");
		$this->query("SET SQL_MODE = ''");
	}

	public function query($sql) {
		$startTime = microtime(true);

		$query = $this->link->query($sql);

		$time = microtime(true) - $startTime;

		ConnectionPanel::logQuery($sql, array(
			'time' => $time,
			'error' => $this->link->error,
			'rows' => $this->link->affected_rows,
		));

		if (!$this->link->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			trigger_error('Error: ' . $this->link->error  . '<br />Error No: ' . $this->link->errno . '<br />' . $sql);
		}
	}

	public function escape($value) {
		return $this->link->real_escape_string($value);
	}

	public function countAffected() {
		return $this->link->affected_rows;
	}

	public function getLastId() {
		return $this->link->insert_id;
	}

	public function __destruct() {
		$this->link->close();
	}
}