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
 * *:* relation implementation
 * @ingroup Orm_Types
 */
class ManyToManyContainerPropertyType extends ContainerPropertyType
{
	/**
	 * @var IQueryable
	 */
	private $proxy;

	/**
	 * @var IOrmProperty
	 */
	private $container;

	/**
	 * @var IOrmProperty
	 */
	private $encapsulant;

	function __construct(
			IQueryable $proxy,
			IOrmProperty $container,
			IOrmProperty $encapsulant
		)
	{
		$this->proxy = $proxy;
		$this->container = $container;
		$this->encapsulant = $encapsulant;

		parent::__construct(
			$container->getType()->getContainer(),
			$encapsulant->getType()->getContainer()
		);
	}

	/**
	 * @return IQueryable
	 */
	function getProxy()
	{
		return $this->proxy;
	}

	/**
	 * @return IOrmProperty
	 */
	function getContainerProxyProperty()
	{
		$this->container;
	}

	/**
	 * @return IOrmProperty
	 */
	function getEncapsulantProxyProperty()
	{
		$this->encapsulant;
	}

	/**
	 * @return string
	 */
	function getImplClass()
	{
		return null;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->proxy->getLogicalSchema()->getEntityName() . '::orm()',
			$this->getContainer()->getLogicalSchema()->getName() . '::orm()->getLogicalSchema()->getProperty(\'' . $this->container->getName() . '\')',
			$this->getEncapsulant()->getLogicalSchema()->getName() . '::orm()->getLogicalSchema()->getProperty(\'' . $this->encapsulant->getName() . '\')',
		);
	}
}

?>