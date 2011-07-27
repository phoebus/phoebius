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
	 * @var bool
	 */
	private $hasInnerErrors = false;

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

	function __get($name)
	{
		return $this->getControl($name)->getValue();
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
	 * @return Form itself
	 */
	function setHiddenValue($name, $value)
	{
		if (isset($this->fieldsToSign[$name])) {
			$this->fieldsToSign[$name]->importValue($value);
		}
		else {
			$field = FormControl::hidden($name, $value);

			$this->addControl($field);

			$this->fieldsToSign[$name] = $field;
		}

		return $this;
	}

	/**
	 * @param  $name
	 * @return FormControl
	 */
	function getControl($name)
	{
		Assert::hasIndex($this->controls, $name, 'know nothing about control `%s` within form %s', $name, $this->id);

		return $this->controls[$name];
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
			if (!$result)
				$this->hasInnerErrors = false;
		}

		return $this->hasErrors();
	}

	function reset()
	{
		$this->errors = true;
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
	function dumpHead(array $htmlAttributes = array())
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
		foreach ($this->getHiddenControls() as $control) {
			$yield .= $control->toHtml();
		}

		return $yield;
	}

	/**
	 * @return string
	 */
	function dumpHeel()
	{
		echo '</form>';
	}

	function hasErrors()
	{
		return !empty($this->errors) || $this->hasInnerErrors;
	}

	function hasInnerErrors()
	{
		return $this->hasInnerErrors;
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
	 * @return IFormControl[]
	 */
	protected function getHiddenControls()
	{
		$yield = array();
		foreach ($this->controls as $control) {
			if (
					$control instanceof HiddenFormControl
					|| $control instanceof HiddenFormControlSet
			) {
				$yield[] = $control;
			}
		}

		return $yield;
	}
}

?>