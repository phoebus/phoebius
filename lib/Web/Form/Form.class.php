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
 * Primitive form
 * @ingroup Form
 */
class Form
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var HttpUrl
	 */
	private $action;

	/**
	 * @var FormEnctype
	 */
	private $enctype;

	/**
	 * @var RequestMethod
	 */
	private $method;

	/**
	 * @var IFormControl[]
	 */
	private $controls = array();

	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @param string $id Form identifier
	 * @param HttpUrl $action form action (url to which it will be sumbitted); may be null
	 * @param RequestMethod $method action method, default is get
	 */
	function __construct($id, HttpUrl $action, RequestMethod $method = null)
	{
		Assert::isScalar($id);

		$this->id = $id;
		$this->action = $action;
		$this->method =
			$method
				? $method
				: RequestMethod::get();
		$this->enctype = new FormEnctype(FormEnctype::ENCODED);
	}

	function __isset($name)
	{
		return isset ($this->controls[$name]);
	}

	function __get($name)
	{
		return $this->getControl($name);
	}

	function getId()
	{
		return $this->id;
	}

	/**
	 * @return FormEnctype
	 */
	function getEnctype()
	{
		return $this->enctype;
	}

	/**
	 * Sets form enctype. If there are file controls inside, a multipart enctype is set implicitly
	 * @param FormEnctype $enctype
	 * @return Form
	 */
	function setEnctype(FormEnctype $enctype)
	{
		$this->enctype = $enctype;

		return $this;
	}

	/**
	 * @return RequestMethod
	 */
	function getMethod()
	{
		return $this->method;
	}

	/**
	 * Adds the control identified by name.
	 *
	 * IF control is file then form enctype is changed to multipart.
	 *
	 * @return Form itself
	 */
	function addControl(IFormControl $control)
	{
		$name = $control->getName();
		Assert::isFalse(isset($this->controls[$name]), 'control with name %s already exists', $name);

		$this->controls[$name] = $control;

		if (
				$control instanceof FileFormControl
				|| $control instanceof FileFormControlSet
		) {
			$this->setEnctype(new FormEnctype(FormEnctype::MULTIPART));
		}

		return $this;
	}

	/**
	 * @param  $name
	 * @return IFormControl
	 */
	function getControl($name)
	{
		Assert::hasIndex($this->controls, $name, 'know nothing about control `%s` within form %s', $name, $this->id);

		return $this->controls[$name];
	}

	/**
	 * @return IFormControl[]
	 */
	function getControls()
	{
		return $this->controls;
	}

	function import(array $variables)
	{
		foreach ($this->controls as $control) {
			$name = $control->getName();
			$value =
				isset($variables[$name])
					? $variables[$name]
					: null;

			$result = $control->importValue($value);
		}

		return !$this->hasErrors();
	}

	function reset()
	{
		$this->errors = array();
		foreach ($this->controls as $control) {
			$control->reset();
		}

		return $this;
	}

	function export()
	{
		$yield = array();
		foreach ($this->controls as $control) {
			$yield[$control->getName()] = $control->getValue();
		}

		return $yield;
	}

	/**
	 * Signs the form and returns <form> cap and hidden fields
	 * @return string
	 */
	function getHeadHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['action']));
		Assert::isFalse(isset($htmlAttributes['method']));
		Assert::isFalse(isset($htmlAttributes['enctype']));

		$htmlAttributes['action'] = $this->action;
		$htmlAttributes['method'] = $this->method;
		$htmlAttributes['enctype'] = $this->enctype->getValue();

		return
			HtmlUtil::getTagCap('form', $htmlAttributes)
			. $this->dumpHidden();
	}

	/**
	 * @return string
	 */
	protected function dumpHidden()
	{
		$yield = '';
		foreach ($this->getControls() as $control) {
			if ($control instanceof HiddenFormControl || $control instanceof HiddenFormControlSet)
				$yield .= $control->toHtml();
		}

		return $yield;
	}

	/**
	 * @return string
	 */
	function getHeelHtml()
	{
		return '</form>';
	}

	function hasErrors()
	{
		return !empty($this->errors) || $this->hasInnerErrors();
	}

	function hasFormErrors()
	{
		return !empty($this->errors);
	}

	function hasInnerErrors()
	{
		foreach ($this->getControls() as $control) {
			if ($control->hasError())
				return true;
		}
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
}

?>