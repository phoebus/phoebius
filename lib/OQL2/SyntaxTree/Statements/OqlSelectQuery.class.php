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
	final class OqlSelectQuery extends OqlBindableNodeWrapper
	{
		/**
		 * @return OqlSelectQuery
		**/
		public static function create()
		{
			return new self;
		}
				
		/**
		 * @return Criteria
		**/
		public function toValue()
		{
			Assert::isNotNull($this->node);
			
			$criteria = $this->node->toValue();
			
			if ($criteria->getLimit() instanceof OqlPlaceholder)
				$criteria->setLimit($criteria->getLimit()->getValue());
			
			if ($criteria->getOffset() instanceof OqlPlaceholder)
				$criteria->setOffset($criteria->getOffset()->getValue());
			
			return $criteria;
		}
				
		/**
		 * @return OqlSelectQuery
		**/
		public function bindAll(array $parameters)
		{
			return parent::bindAll($parameters);
		}
		
		/**
		 * @return EntityQuery
		 */
		public function toCriteria()
		{
			Assert::isNotNull($this->node);
			
			return $this->toValue();
		}
		
		protected function checkNode(OqlSyntaxNode $node)
		{
			Assert::isTrue($node instanceof OqlCriteriaNode);
		}
	}
?>