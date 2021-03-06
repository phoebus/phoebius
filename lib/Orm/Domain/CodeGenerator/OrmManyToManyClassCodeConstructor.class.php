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
 * Many-to-many worker generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmManyToManyClassCodeConstructor extends OrmContainerClassCodeConstructor
{
	function isPublicEditable()
	{
		return true;
	}

	function getClassName()
	{
		return $this->propertyType->getContainerClassName($this->ormProperty);
	}

	protected function getExtendsClassName()
	{
		return $this->propertyType->getAutoContainerClassName($this->ormProperty);
	}
}

?>