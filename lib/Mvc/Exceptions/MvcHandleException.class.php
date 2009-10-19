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
 * @ingroup Mvc_Exceptions
 */
abstract class MvcHandleException extends ParameterTypeException
{
	/**
	 * @param IControllerContext $context
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 * @param string $message
	 */
	function __construct(IControllerContext $context, $parameterName, $parameterValue, $message = 'unexpected parameter type')
	{
		Assert::isScalar($parameterName);

		parent::__construct($context->getRouteContext(), $parameterName, $parameterValue, $message);

		$this->parameterName = $parameterName;
		$this->parameterValue = $parameterValue;
	}
}

?>