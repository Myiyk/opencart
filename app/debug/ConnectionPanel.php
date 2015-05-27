<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace App\Bridges\DatabaseTracy;

use Nette;
use Nette\Database\Helpers;
use Tracy;


/**
 * Debug panel for Nette\Database.
 *
 * @author     David Grudl
 */
class ConnectionPanel extends Nette\Object implements Tracy\IBarPanel
{
	/** @var int */
	public static $maxQueries = 100;

	/** @var int logged time */
	private static $totalTime = 0;

	/** @var int */
	private static $count = 0;

	/** @var array */
	private static $queries = array();

	/** @var string */
	public static $name;

	/** @var string */
	public static $dns;

	/** @var bool|string explain queries? */
	public static $explain = TRUE;

	/** @var bool */
	public static $disabled = FALSE;

	/** @var mixed database connection */
	public static $connection;

	public static function logQuery($sql, $data)
	{
		if (self::$disabled) {
			return;
		}

		$defaultData = array(
			'time' => 0.0,
			'error' => '',
			'explain' => false,
			'source' => false,
			'rows' => 0,
		);

		$backtrace = debug_backtrace();

		foreach ($backtrace as $level) {
			if (in_array($sql, $level['args'])) { // sql is in arguments
				if (isset($level['file'], $level['line'])) { // debug know where is code
					$defaultData['source'] = array($level['file'], $level['line']);
				}
			} else {
				break;
			}
		}

		$data = array_merge($defaultData, $data);

		self::$count++;
		self::$totalTime += $data['time'];

		if (self::$count < self::$maxQueries) {
			self::$queries[] = array($sql, $data);
		}
	}

	public static function info($name, $dns, $connection)
	{
		self::$name = $name;
		self::$dns = $dns;
		self::$connection = $connection;
	}


	public static function renderException($e)
	{
		if (!$e instanceof \PDOException) {
			return;
		}
		if (isset($e->queryString)) {
			$sql = $e->queryString;

		} elseif ($item = Tracy\Helpers::findTrace($e->getTrace(), 'PDO::prepare')) {
			$sql = $item['args'][0];
		}
		return isset($sql) ? array(
			'tab' => 'SQL',
			'panel' => Helpers::dumpSql($sql),
		) : NULL;
	}


	public function getTab()
	{
		$name = self::$name;
		$count = self::$count;
		$totalTime = self::$totalTime;

		ob_start();
		require __DIR__ . '/templates/ConnectionPanel.tab.phtml';
		return ob_get_clean();
	}


	public function getPanel()
	{
		self::$disabled = TRUE;

		if (!self::$count) {
			return;
		}

		$dns = self::$dns;
		$name = self::$name;
		$count = self::$count;
		$totalTime = self::$totalTime;
		$queries = self::$queries;

		$callable = method_exists(self::$connection, 'query');

		foreach ($queries as &$query) {
			list($sql, $data) = $query;
			$error = $data['error'];

			$explain = NULL;
			if ($callable && !$error && self::$explain && preg_match('#\s*\(?\s*SELECT\s#iA', $sql)) {
				try {
					$cmd = is_string(self::$explain) ? self::$explain : 'EXPLAIN';
					$explain = self::$connection->query("$cmd $sql");
				} catch (\Exception $e) {
					dump($e);
				}
			}
			$query[1]['explain'] = $explain;
		}

		ob_start();
		require __DIR__ . '/templates/ConnectionPanel.panel.phtml';
		return ob_get_clean();
	}

}
