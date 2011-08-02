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

/**
 * Represents a base form control
 * @ingroup Form
 */
abstract class BaseFormControl implements IFormControl
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var bool
	 */
	private $isOptional;

	/**
	 * @var mixed
	 */
	private $defaultValue;

	/**
	 * @var bool
	 */
	private $isImported;

	/**
	 * @var scalar
	 */
	private $importedValue;

	/**
	 * @var FormControlError
	 */
	private $error;

	/**
	 * @var string|null
	 */
	private $errorMessage;

    /**
     * @var FormControlError[]
     */
    private $registeredErrors = array();

	function __construct($name, $label)
	{
		Assert::isScalar($name);
		Assert::isScalar($label);

		$this->name = $name;
		$this->label = $label;
	}

	function getName()
	{
		return $this->name;
	}

	function getLabel()
	{
		return $this->label;
	}

	function isOptional()
	{
		return $this->isOptional;
	}

	/**
	 * Marks control as optional
	 * @return BaseFormControl
	 */
	function markOptional()
	{
		$this->isOptional = true;

		return $this;
	}

	/**
	 * Marks control as required
	 * @return BaseFormControl
	 */
	function markRequired()
	{
		$this->isOptional = false;

		return $this;
	}

    /**
     * Registers an error with the specified behaviour and message
     * @param FormControlError $error
     * @return BaseFormControl
     */
	function registerError(FormControlError $error)
    {
        $this->registeredErrors[$error->getValue()] = $error;

        return $this;
    }

    /**
     * Whether error is registered
     * @param $id see FormControlError constants
     * @return bool
     */
    function hasRegisteredError($id)
    {
        if ($id instanceof FormControlError)
            $id = $id->getValue();

        return isset($this->registeredErrors[$id]);
    }

    /**
     * Gets the registered error with the specified message and behaviour
     * @param $id see FormControlError constants
     * @return FormControlError
     */
    function getRegisteredError($id)
    {
        if ($id instanceof FormControlError)
            $id = $id->getValue();

        Assert::hasIndex($this->registeredErrors, $id);

        return $this->registeredErrors[$id];
    }

	function setDefaultValue($value)
	{
		$this->defaultValue = $value;

		return $this;
	}

	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	function getValue()
	{
		return
			$this->isImported()
				? $this->getImportedValue()
				: $this->getDefaultValue();
	}

	function hasError($id = null)
	{
		return
                ($id && $this->error && $this->error->is($id))
                || !!$this->error;
	}

	function getError()
	{
		return $this->error;
	}

	function getErrorMessage()
	{
        Assert::isNotEmpty($this->error);

		return $this->error->getMessage();
	}

	function reset()
	{
		$this->dropError();
		$this->dropImportedValue();

		return $this;
	}

    /**
     * Sets the error and returns it
     * @param FormControlError $error
     * @return FormControlError
     */
	protected function setError(FormControlError $error)
	{
        if ($this->hasRegisteredError($error))
            $error = $this->getRegisteredError($error);

		$this->error = $error;

        return $this->error;
	}

	protected function dropError()
	{
		$this->error = null;
	}

    /**
     * @param $value
     * @param bool $considerBehaviour
     * @return void
     */
	protected function setImportedValue($value, $considerBehaviour = true)
	{
        if ($considerBehaviour && $this->hasError()) {
            $behaviour = $this->getError()->getBehaviour();
            if ($behaviour->is(FormControlErrorBehaviour::SET_EMPTY)) {
                $value = null;
            }
            else if ($behaviour->is(FormControlErrorBehaviour::USE_DEFAULT)) {
                $value = $this->getDefaultValue();
            }
        }

		$this->isImported = true;
		$this->importedValue = $value;
	}

	protected function isImported()
	{
		return $this->isImported();
	}

	protected function getImportedValue()
	{
		return $this->importedValue;
	}

	protected function dropImportedValue()
	{
		$this->isImported = false;
		$this->importedValue = null;
	}
}

?>