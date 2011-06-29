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
	final class OqlOperatorNodeMutator extends OqlSyntaxNodeMutator
	{
		private static $operatorMap = array(
			'='				=> BinaryLogicalOperator::EQUALS,
			'!='			=> BinaryLogicalOperator::NOT_EQUALS,
			'and'			=> BinaryLogicalOperator::EXPRESSION_AND,
			'or'			=> BinaryLogicalOperator::EXPRESSION_OR,
			'>'				=> BinaryLogicalOperator::GREATER_THAN,
			'>='			=> BinaryLogicalOperator::GREATER_OR_EQUALS,
			'<'				=> BinaryLogicalOperator::LOWER_THAN,
			'<='			=> BinaryLogicalOperator::LOWER_OR_EQUALS,
			'like'			=> BinaryLogicalOperator::LIKE,
			'notlike'		=> BinaryLogicalOperator::NOT_LIKE,
			'ilike'			=> BinaryLogicalOperator::ILIKE,
			'notilike'		=> BinaryLogicalOperator::NOT_ILIKE,
			'similar to'	=> BinaryLogicalOperator::SIMILAR_TO,
			'notsimilar to'	=> BinaryLogicalOperator::NOT_SIMILAR_TO,
			'+'				=> BinaryLogicalOperator::ADD,
			'-'				=> BinaryLogicalOperator::SUBSTRACT,	// or PrefixUnaryExpression::MINUS
			'*'				=> BinaryLogicalOperator::MULTIPLY,
			'/'				=> BinaryLogicalOperator::DIVIDE,
			
			'isnull'		=> UnaryPostfixLogicalOperator::IS_NULL,
			'isnotnull'		=> UnaryPostfixLogicalOperator::IS_NOT_NULL,
			'istrue'		=> UnaryPostfixLogicalOperator::IS_TRUE,
			'isfalse'		=> UnaryPostfixLogicalOperator::IS_FALSE,
			
			'not'			=> PrefixUnaryLogicalOperator::NOT,
			
			'in'			=> InSetLogicalOperator::IN,
			'notin'			=> InSetLogicalOperator::NOT_IN
		);
		
		/**
		 * @return OqlOperatorNodeMutator
		**/
		public static function me()
		{
			return parent::instance(__CLASS__);
		}
		
		/**
		 * @return OqlValueNode
		**/
		public function process(OqlSyntaxNode $node, OqlSyntaxNode $rootNode)
		{
			$iterator = OqlSyntaxTreeDeepRecursiveIterator::me();
			$node = $iterator->reset($node);
			
			$operator = '';
			
			do {
				$value = $node->toValue();
				$operator .= is_bool($value)
					? ($value === true ? 'true' : 'false')
					: $value;
			
			} while ($node = $iterator->next());
			
			Assert::hasIndex(self::$operatorMap, $operator);
			
			return OqlValueNode::create()->setValue(
				self::$operatorMap[$operator]
			);
		}
	}
?>