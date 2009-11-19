<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
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
 * @ingroup Core_Types_Complex
 */
final class Time implements IBoxable
{
	/**
	 * @var int
	 */
	private $hour = '00';

	/**
	 * @var int
	 */
	private $minute = '00';

	/**
	 * @var int
	 */
	private $second = '00';

	/**
	 * @return Time
	**/
	static function create($input = null)
	{
		return new self ($input);
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
	 * @return Date
	 */
	static function now()
	{
		return new self(time());
	}

	function __construct($input = null)
	{
		$this->setValue($input);
	}

	// currently supports '01:23:45', '012345', '1234', '12'
	private function setValue($input = null)
	{
		if (!$input) {
			$input = time();
		}

		if (strlen((string)$input) > 8 && TypeUtils::isInteger($input)) {
			// unix timestamp
			list ($this->hour, $this->minute, $this->second) = explode(':', date('H:i:s', $input));
		}
		else {
			$chunks = preg_split('/[^\d]+/', $input);
			$setters = array('hour', 'minute', 'second');

			foreach ($chunks as $k => $v)
			{
				$this->{'set'.$setters[$k]}($v);
			}
		}
	}

	function getHour()
	{
		return (int) $this->hour;
	}

	/**
	 * @return Time
	**/
	function setHour($hour)
	{
		$hour = (int) $hour;

		if ((0 > $hour) || ($hour > 23)) {
			throw new OutOfRangeException('hour');
		}

		$this->hour = sprintf('%02d', $hour);

		return $this;
	}

	function getMinute()
	{
		return (int) $this->minute;
	}

	/**
	 * @return Time
	**/
	function setMinute($minute)
	{
		$minute = (int) $minute;

		if ((0 > $minute) || ($minute > 59)) {
			throw new OutOfRangeException('minute');
		}

		$this->minute = sprintf('%02d', $minute);

		return $this;
	}

	function getSecond()
	{
		return (int) $this->second;
	}

	/**
	 * @return Time
	**/
	function setSecond($second)
	{
		$second = (int) $second;


		if ((0 > $second) || ($second > 59)) {
			throw new OutOfRangeException('second');
		}

		$this->second = sprintf('%02d', $second);

		return $this;
	}

	/**
	 * @return string
	 */
	function toHourMinuteTimeString($delimiter = ':')
	{
		return join($delimiter, array($this->hour, $this->minute));
	}

	/**
	 * @return string
	 */
	function toFormattedString($delimiter = ':')
	{
		return join($delimiter, array($this->hour, $this->minute, $this->second));
	}

	function __toString()
	{
		return $this->toFormattedString();
	}

	/**
	 * @return string
	 */
	function getValue()
	{
		return $this->toFormattedString();
	}

	/**
	 * @return int
	 */
	function toMinutes()
	{
		return
			($this->hour * 60)
			+ $this->minute
			+ round($this->second / 100, 0);
	}

	/**
	 * @return int
	 */
	function toSeconds()
	{
		return
			($this->hour * 3600)
			+ ($this->minute * 60)
			+ $this->second;
	}
}

?>