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
 * @ingroup CustomOrmTypes
 */
final class DatePropertyType extends ObjectPropertyType
{
	function __construct($isNullable = false)
	{
		parent::__construct('Date', null, $isNullable);
	}

	/**
	 * @param Date $logicalValue
	 * @return SqlValueList
	 */
	function makeRawValue($logicalValue)
	{
		return array (
			new ScalarSqlValue(
				$logicalValue->toFormattedString('Y/m/d')
			)
		);
	}

	/**
	 * @return array
	 */
	function getDbColumns()
	{
		return array (
			DBType::create(DBType::DATE)
				->setIsNullable($this->isNullable())
		);
	}
}

?>