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
	final class OqlProtoDAONodeMutator extends OqlSyntaxNodeMutator
	{
		/**
		 * @return OqlProtoDAONodeMutator
		**/
		public static function me()
		{
			return parent::instance(__CLASS__);
		}
		
		/**
		 * @return OqlProtoDAONode
		**/
		public function process(OqlSyntaxNode $node, OqlSyntaxNode $rootNode)
		{
			$class = $node->toValue();
			
			Assert::isTrue(TypeUtils::isExists($class));
			Assert::isTrue(TypeUtils::isInherits($class, 'IDaoRelated'));
			
			// TODO: nothing more expected assertion?
			
			return OqlProtoDAONode::create()->setObject(
				call_user_func(array($class, 'dao'))
			);
		}
	}
?>