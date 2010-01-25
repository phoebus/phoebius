<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Represents the MySQL DAL
 *
 * @ingroup Dal_DB
 */
class MySqlDB extends DB
{
	/**
	 * For mysql_affected_rows()
	 *
	 * @var DBQueryResult
	 */
	private $latestQueryResultId;

	/**
	 * @var array of DBQueryResult
	 */
	private $queryResults = array();

	/**
	 * @var resource|null
	 */
	private $link;

	/**
	 * @var MySqlDialect
	 */
	private $myDialect;

	/**
	 * Static alias for the ctor
	 *
	 * @return MySqlDB
	 */
	static function create()
	{
		return new self;
	}

	function connect($force = false)
	{
		if ($this->isConnected() && !$force) {
			return $this;
		}

		$connectionArguments = array(
			$this->getHost(),
			$this->getUser(),
			$this->getPassword(),
			true,
			MYSQL_CLIENT_IGNORE_SPACE
		);

		$link = call_user_func_array(
			$this->isPersistent()
				? 'mysql_pconnect'
				: 'mysql_connect',
			$connectionArguments
		);

		if ($link) {
			$this->link = $link;
		}
		else {
			throw new DBConnectionException($this, mysql_error());
		}

		if (($dbname = $this->getDBName())) {
			if (!mysql_select_db($dbname, $this->link)) {
				throw new DBConnectionException($this, mysql_error($this));
			}
		}

		if ($this->getEncoding()) {
			$this->setEncoding($this->getEncoding());
		}

		return $this;
	}

	function disconnect()
	{
		if ($this->isConnected()) {
			try {
				mysql_close($this->link);
			}
			catch (ExecutionContextException $e) {
				// nothing here
			}

			$this->link = null;
			$this->latestQueryResultId = null;
		}

		return $this;
	}

	function getAffectedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			$this->latestQueryResultId === $result,
			'affected rows number can be obtained only for the latest sent query'
		);

		return mysql_affected_rows($this->link);
	}

	function getFetchedRowsNumber(DBQueryResult $result)
	{
		Assert::isTrue(
			isset($this->queryResults[spl_object_hash($result)]),
			'unknown DBQueryResult'
		);

		return mysql_num_rows($result->getResource());
	}

	function getDialect()
	{
		if (!$this->myDialect) {
			$this->myDialect = new MySqlDialect($this->link);
		}

		return $this->myDialect;
	}

	function setEncoding($encoding)
	{
		parent::setEncoding($encoding);

		if ($this->isConnected()) {
			$result = mysql_set_charset($encoding, $this->link);

			Assert::isTrue(
				$result,
				'invalid encoding "%s" is specified',
				$encoding
			);
		}

		return $this;
	}

	function sendQuery(ISqlQuery $query, $isAsync = false)
	{
		$resource = $this->performQuery($query, $isAsync);

		$this->latestQueryResultId = new DBQueryResult($this, $resource);

		$this->queryResults[spl_object_hash($this->latestQueryResultId)] = true;

		return $resource;
	}

	function getRow(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		Assert::isTrue(
			mysql_num_rows($result) <= 1,
			'query returned too many rows (only one is expected)'
		);

		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);

		if (!is_array($row)) {
			throw new RowNotFoundException($query);
		}

		return $row;
	}

	function getColumn(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$array = array();

			while (($row = mysql_fetch_row($result))) {
				$array[] = reset($row);
			}

			mysql_free_result($result);

			return $array;
		}
		else {
			return array();
		}
	}

	function getCell(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$row = mysql_fetch_row($result);
			$cell = reset($row);

			mysql_free_result($result);

			return $cell;
		}
		else {
			throw new CellNotFoundException($query);
		}
	}

	function getRows(ISqlSelectQuery $query)
	{
		$result = $this->performQuery($query, false);

		if ($result) {
			$array = array();

			while (($row = mysql_fetch_assoc($result))) {
				$array[] = $row;
			}

			mysql_free_result($result);

			return $array;
		}
		else {
			return array();
		}
	}

	/**
	 * @throws DBQueryException
	 * @param ISqlQUery $query
	 * @param boolean $isAsync
	 * @return resource
	 */
	protected function performQuery(ISqlQuery $query, $isAsync)
	{
		$result = mysql_query(
			$query->toDialectString($this->getDialect()),
			$this->link
		);

		if (!$result) {
			$code = mysql_errno($this->link);

			if ($code == 1062) {
				throw new UniqueViolationException($query, mysql_error($this->link));
			}
			else {
				throw new DBQueryException($query, mysql_error($this->link), $code);
			}
		}

		Assert::isTrue(
			is_resource($result) || $result === true
		);

		return $result;
	}

	function isConnected()
	{
		return is_resource($this->link);
	}

	function getGenerator($tableName, $columnName, DBType $type)
	{
		return new LastInsertIdGenerator($this);
	}

	/**
	 * @return int
	 */
	function getLastInsertId()
	{
		return mysql_insert_id($this->link);
	}
}

?>