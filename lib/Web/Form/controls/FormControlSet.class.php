<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

abstract class FormControlSet implements IFormControl
{
	const ID_PATTERN = '/^[a-z0-9_]+$/i';
	const ERROR_VALUE_NOT_AN_ARRAY = 'not_array';

	private $id;
	private $label;
	private $skipWrong;
	private $constraints = array();

	private $defaultValue = array();
	private $value = array();
	private $controls = array();
		
	private $errors = array();
	private $hasInnerErrors = false;

	/**
	 * @return FormControl
	 */
	abstract protected function spawnSingle();
	
	function __construct(
			$id, $label, 
			array $defaultValue = array(),
			$skipWrong = true
		)
	{
		Assert::isScalar($id);
		Assert::isTrue(preg_match(self::ID_PATTERN, $id));
		Assert::isScalar($label);
		Assert::isBoolean($skipWrong);
		
		$this->id = $id;
		$this->label = $label;
		$this->skipWrong = $skipWrong;
		
		$this->defaultValue = $defaultValue;
		
		$this->makeDefaults();
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getStub($defaultValue)
	{
		return $this->spawnSingle($defaultValue);
	}
	
	function getInnerId()
	{
		return $this->id . '[]';
	}
	
	function getLabel()
	{
		return $this->label;
	}
	
	function getControls()
	{
		return $this->controls;
	}
	
	function getIterator()
	{
		return new ArrayIterator($this->controls);
	}
	
	function count()
	{
		return sizeof($this->controls);
	}
	
	function hasInnerErrors()
	{
		return $this->hasInnerErrors;
	}
	
	function hasErrors()
	{
		return !empty($this->errors);
	}
	
	function hasError($id)
	{
		return isset($this->errors[$id]);
	}
	
	function getErrors()
	{
		return $this->errors;
	}
	
	function getValue()
	{
		return $this->value;
	}
	
	function importValue($value)
	{		
		$this->errors = array();
		$this->controls = array();
		$this->hasInnerErrors = false;
		
		if (is_array($value)) {
			$setValue = array();
			
			foreach ($value as $innerValue) {
				$control = $this->spawnSingle();
				if ($control->importValue($innerValue)) {
					$this->controls[] = $control;
					$setValue[] = $control->getValue();
				}
				else if (!$this->skipWrong) {
					$this->controls[] = $control;
					$setValue[] = $control->getValue();
					$this->hasInnerErrors = true;
				}				
			}
			
			$this->setValue($setValue);
		}
		else if (!empty($value) && !is_array($value)) {
			$this->makeDefaults();
			$this->addError(self::ERROR_VALUE_NOT_AN_ARRAY, 'value is not an array');
		}
		
		return $this->hasErrors();
	}
	
	function getConstraints()
	{
		return $this->constraints;
	}
	
	function addConstraint(IFormControlConstraint $constraint)
	{
		$this->constraints[] = $constraint;
		
		return $this;
	}

	function toHtml(array $htmlAttributes = array())
	{
		$s = '';
		foreach ($this->controls as $control) {
			$s .= $control->toHtml($htmlAttributes);
		}
		
		return $s;
	}
	
	protected function setValue(array $value)
	{
		$this->value = $value;
		
		return $this;
	}
	
	protected function addError($id, $message)
	{
		Assert::isScalar($id);
		
		$this->errors[$id] = $message;
		
		return $this;
	}
	
	protected function makeDefaults()
	{
		$this->value = $this->defaultValue;
		
		foreach ($this->value as $value) {
			$control = $this->spawnSingle();
			$success = $control->importValue($value);
			
			Assert::isTrue($success, 'default value should pass constraints, this one didn`t: %s', $value);
			
			$this->controls[] = $control;
		}
	}
}

?>