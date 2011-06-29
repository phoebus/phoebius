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
	final class OqlPlaceholder implements IExpression
	{
		private $name	= null;
		private $value	= null;
		private $binded	= false;
		
		/**
		 * @return OqlPlaceholder
		**/
		public static function create($name)
		{
			return new self($name);
		}
		
		public function __construct($name)
		{
			Assert::isScalar($name);
			Assert::isFalse($name == '');
			
			$this->name = $name;
		}
		
		public function getName()
		{
			return $this->name;
		}
		
		public function getValue()
		{
			return $this->value;
		}
		
		public function isBinded()
		{
			return $this->binded;
		}
		
		/**
		 * @return OqlPlaceholder
		**/
		public function bind($value)
		{
			$this->value = $value;
			$this->binded = true;
			
			return $this;
		}

		function toSubjected(ISubjectivity $object)
		{
			Assert::isTrue($this->binded, 'placeholder %s not yet binded', $this->name);
			
			$me = clone $this;
			$me->value = $object->subject($this->value, $this);
			
			return $me;
		}
		
		public function toDialectString(IDialect $dialect)
		{
			if ($dialect instanceof DummyDialect)
				return '$'.$this->name;

			Assert::isTrue($this->binded, 'placeholder %s not yet binded', $this->name);
			
			$value = $this->value;
			
			if ($value instanceof ISqlCastable) {
				$value = $value->toDialectString($dialect);
			}
			
			return $value;
		}
		
		public function __toString()
		{
			return 
				$this->binded
					? $this->value
					: '$'.$this->name;
		}
	}
?>