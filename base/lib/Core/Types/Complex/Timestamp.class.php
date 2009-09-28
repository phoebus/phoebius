<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @ingroup ComplexCoreTypes
 */
final class Timestamp extends Date
{
	/**
	 * @var Time
	 */
	private $time;

	/**
	 * @return Timestamp
	 */
	static function create($value = null)
	{
		return new self($value);
	}

	/**
	 * @return Date
	 */
	static function cast($value)
	{
		try {
			return new self($value);
		}
		catch (ArgumentException $e) {
			throw new TypeCastException(new Type(__CLASS__), $value);
		}
	}

	/**
	 * @return OrmPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new TimestampPropertyType(
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return Timestamp
	 */
	static function now()
	{
		return new self(time());
	}

	function getHour()
	{
		return $this->time->getHour();
	}

	function getMinute()
	{
		return $this->time->getMinute();
	}

	function getSecond()
	{
		return $this->time->getSecond();
	}

	/**
	 * @return Time
	 */
	function getTime()
	{
		return $this->time;
	}

	/**
	 * @return Timestamp
	 */
	function setTime(Time $time)
	{
		$this->import(
			mktime(
				$time->getHour(),
				$time->getMinute(),
				$time->getSecond(),
				$this->getMonth(),
				$this->getDay(),
				$this->getYear()
			)
		);

		return $this;
	}

	function equals(Date $timestamp)
	{
		return ($this->getStamp() == $timestamp->getStamp());
	}

	/**
	 * @return string
	 */
	function toFormattedString($format = 'd-m-Y H:i:s')
	{
		return parent::toFormattedString($format);
	}

	protected function import($int)
	{
		parent::import($int);

		$this->int = $int;
		$this->time = new Time($int);
	}
}

?>