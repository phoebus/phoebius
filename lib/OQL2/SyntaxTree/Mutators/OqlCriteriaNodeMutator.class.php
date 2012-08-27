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
	final class OqlCriteriaNodeMutator extends OqlSyntaxNodeMutator
	{
		private static $methodMap = array(
			'from'		=> null,
			'where'		=> 'setCondition',
			'order by'	=> 'addOrderBy',
			'limit'		=> 'setLimit',
			'offset'	=> 'setOffset',
			'having'	=> null,
			'group by'	=> null
		);

		/**
		 * @return OqlCriteriaNodeMutator
		**/
		public static function me()
		{
			return parent::instance(__CLASS__);
		}

		/**
		 * @return OqlCriteriaNode
		**/
		public function process(OqlSyntaxNode $node, OqlSyntaxNode $rootNode)
		{
			$iterator = OqlSyntaxTreeRecursiveIterator::me();
			$current = $iterator->reset($node);

			while ($current) {
				if ($current->toValue() == 'from') {
					$next = $iterator->next();
					$entity = $next->toValue();

					$query = new EntityQuery($entity->getOrmEntity());

					break;
				}

				$current = $iterator->next();
			}

			Assert::isNotEmpty($query);

			$iterator = OqlSyntaxTreeRecursiveIterator::me();
			$current = $iterator->reset($node);

			while ($current) {
				// properties, group by, having projections
				if ($current instanceof OqlObjectProjectionNode) {
					$query->addProjection($current->toValue());

				// from, where, order by, limit, offset
				} elseif ($current instanceof OqlTokenNode) {
					Assert::hasIndex(self::$methodMap, $current->toValue());

					if ($setter = self::$methodMap[$current->toValue()]) {
						$next = $iterator->next();
						Assert::isNotNull($next);

						if ($next->toValue() instanceof OrderChain)
							foreach ($next->toValue() as $_)
								$query->{$setter}($_);
						else
							$query->{$setter}($next->toValue());
					}
				}

				$current = $iterator->next();
			}

			return OqlCriteriaNode::create()->setObject($query);
		}
	}
?>