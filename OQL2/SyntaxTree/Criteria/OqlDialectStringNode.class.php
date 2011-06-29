<?php
/****************************************************************************
 *   Copyright (C) 2009 by Vladlen Y. Koshelev                              *
 *                                                                          *
 *   This program is free software; you can redistribute it and/or modify   *
 *   it under the terms of the GNU Lesser General Public License as         *
 *   published by the Free Software Foundation; either version 3 of the     *
 *   License, or (at your option) any later version.                        *
 *                                                                          *
 ****************************************************************************/

	/**
	 * @ingroup OQL
	**/
	abstract class OqlDialectStringNode extends OqlObjectNode implements ISubjectivity
	{
		protected $class = 'DialectString';
		
		
		function subject($subject, ISubjective $object = null)
		{
			return new SqlIdentifier($subject);
		}
		
		public function toString()
		{
			return 
				$this->object 
					? $this->object->toSubjected($this)->toDialectString(DummyDialect::getInstance())
					: null;
		}
	}
?>