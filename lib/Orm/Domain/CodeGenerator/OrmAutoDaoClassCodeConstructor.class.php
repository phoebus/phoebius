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
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmAutoDaoClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	function getClassName()
	{
		return 'Auto' .  $this->ormClass->getDaoName();
	}

	function isPublicEditable()
	{
		return false;
	}

	protected function getClassType()
	{
		return 'abstract';
	}

	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	function __construct()
	{
		parent::__construct(\$this->getDatabase(), {$this->ormClass->getEntityName()}::orm());
	}

	protected function getDatabase()
	{
		return DBPool::getDefault();
	}
EOT;
	}

	protected function getExtendsClassName()
	{
		return 'RdbmsDao';
	}
}

?>