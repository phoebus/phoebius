<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * $oldEntitySet =
 * 	EntityQuery::create(MyEntity::orm())
 * 		->where(
 *			'time',
 *			Expression::in(
 *				array(
 *					Date::now()
 *					Date::create()->spawn('-1 day')
 *				)
 *			)
 * 		)
 * 		->select();
 *
 * LINQ to OrmEntity
 * @ingroup OrmExpression
 */
final class EntityQuery implements IEntityExpression, ISqlValueExpression
{
	/**
	 * @var IQueried
	 */
	private $entity;

	/**
	 * @var string|null
	 */
	private $dbContainer;

	/**
	 * @var array of {@link IEntityExpression}
	 */
	private $expressionChain = array();

	/**
	 * @return EntityQuery
	 */
	static function create(IQueried $entity, $alias = null)
	{
		return new self ($entity, $alias);
	}

	function __construct(IQueried $entity, $alias = null)
	{
		$this->entity = $entity;
		$this->dbContainer =
			$alias
				? $alias
				: $entity->getPhysicalSchema()->getDBTableName();
		$this->expressionChain = new EntityExpressionChain();
	}

	/**
	 * @return EntityQuery
	 */
	function setAndBlock()
	{
		$this->expressionChain->setAndBlock();

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	function setOrBlock()
	{
		$this->expressionChain->setOrBlock();

		return $this;
	}

	/**
	 * Alias for EntityQuery::addExpression()
	 * @return EntityQuery
	 */
	function where($propertyName, IExpression $expression)
	{
		$this->addExpression($propertyName, $expression);
		return $this;
	}

	/*
	 * @return EntityQuery
	 */
	function addExpression($propertyName, IExpression $expression)
	{
		Assert::isScalar($propertyName);

		try {
			$property = $this->entity->getLogicalSchema()->getProperty($propertyName);
		}
		catch (OrmModelIntegrityException $e){
			Assert::isUnreachable(
				'unknown property %s::%s for %s',
				$this->entity->getLogicalSchema()->getEntityName(),
				$propertyName,
				__CLASS__
			);
		}

		$this->expressionChain->add(
			$this->dbContainer,
			$property,
			$property->getType()->getEntityExpression($expression)
		);
	}

	/*
	 * @return EntityQuery
	 */
	function addEntityExpression(IEntityExpression $entityExpression)
	{
		$this->expressionChain->add(
			$entityExpression
		);

		return $this;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function spawnOrBlock()
	{
		$orBlock = new EntityExpressionChain(ExpressionChainPredicate::conditionOr());
		$this->expressionChain->add($orBlock);

		return $orBlock;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function spawnAndBlock()
	{
		$andBlock = new EntityExpressionChain(ExpressionChainPredicate::conditionAnd());
		$this->expressionChain->add($andBlock);

		return $andBlock;
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		return $this->expressionChain->toDalExpression();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->toDalExpression()->toDialectString($dialect);
	}

	/**
	 * @return array of OrmEntity
	 */
	function select()
	{
		return $this->entity->getDao()->getListByCondition($this->toExpression());
	}
}

?>