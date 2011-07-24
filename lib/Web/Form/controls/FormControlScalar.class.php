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

abstract class FormControlScalar implements IFormControl
{	
	private $id;
	private $label;

	private $defaultValue;
	private $importedValue;
	
	private $errors = array();
	private $constraints = array();
	
	function __construct($id, $label, $defaultValue = null)
	{
		Assert::isScalar($id);
		Assert::isScalar($label);
		Assert::isScalarOrNull($defaultValue);
		
		$this->id = $id;
		$this->label = $label;
		$this->defaultValue = $defaultValue;
	}

	function getId()
	{
		return $this->id;
	}
	
	function getLabel()
	{
		return $this->label;
	}

	function getValue()
	{
		return 
			is_null($this->importedValue)
				? $this->defaultValue
				: $this->importedValue;	
	}

	/**
	 * @return boolean whether import was successful or not
	 */
	function importValue($value)
	{
		// reset control state
		$this->errors = array();
		$this->importedValue = null;
		
		$noImport = false;
		foreach ($this->constraints as $constraint) {
			if (!$constraint->check($value)) {
				$this->addError($constraint->getId(), $constraint->getMessage());
				if ($constraint->rejectsImportedValue()) {
					$noImport = true;
				}
			}
		}
		
		if (!$noImport)
			$this->importedValue = $value;
		
		return $this->hasErrors();
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
	
	/**
	 * @return FormControlScalar
	 */
	function addError($id, $message)
	{
		Assert::isScalar($id);
		Assert::isScalar($message);
		
		$this->errors[$id] = $message;
		
		return $this;
	}
	
	/**
	 * @return FormControlScalar
	 */
	function addConstraint(IFormControlConstraint $constraint)
	{
		$this->constraints[] = $constraint;
		
		return $this;
	}
}

?>