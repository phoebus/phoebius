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

// reviewed submission process:
// * initialize
// * if request method, form id and signature are valid (throw exception):
//   * choose form handler (may be default: Form::handle())
//     * import values (control errors)
//       * process (form errors)
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
	 * @var RequestMethod
	 */
	private $method;

	/**
	 * @var array of FormControl by name
	 */
	private $controls = array();

	/**
	 * @var array of FormControl by name
	 */
	private $buttons = array();

	/**
	 * @var array
	 */
	private $fieldsToSign = array();

	/**
	 * @var array
	 */
	private $errors = array();
	private $hasInnerErrors = false;

	function __construct($id, HttpUrl $action)
	{
		Assert::isScalar($id);

		$this->id = $id;
		$this->action = $action;
		$this->method = new RequestMethod(RequestMethod::POST);
	}

	/**
	 * @return Form itself
	 */
	function addControl(IFormControl $control)
	{
		$name = $control->getName();
		Assert::isFalse(isset($this->controls[$name]), 'control with name %s already exists', $name);

		$this->controls[$name] = $control;

		return $this;
	}

	/**
	 * @return Form itself
	 */
	function addButton($name, $label, $callback = null)
	{
		if (is_string($callback)) {
			Assert::isTrue(method_exists($this, $callback), 'unknown method %s::%s', get_class($this), $callback);

			$callback = array($this, $callback);
		}

		$button = FormControl::button($name, $label, $callback);

		$this->addControl($button);

		$this->buttons[$name] = $button;

		return $this;
	}

	/**
	 * @return Form itself
	 */
	function setHiddenValue($name, $value)
	{
		$field = FormControl::hidden($name, $value);

		$this->addControl($field);

		$this->fieldsToSign[$name] = $field;

		return $this;
	}

	function getControl($name)
	{
		Assert::hasIndex($this->controls, $name, 'know nothing about control `%s` within form %s', $name, $this->id);

		return $this->controls[$name];
	}

	/**
	 * Handles request
	 * @return mixed true on success, array of errors on handle failure
	 */
	function handle(WebRequest $request)
	{
		if (!$request->getRequestMethod()->equals($this->method))
			return false;

		$variables = $request->getPostVars();

		$this->process($variables);
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
				$this->hasErrors = false;
		}

		return $this->hasErrors;
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
	 * Overridden. Called when form is submitted
	 */
	protected function process(array $variables)
	{
		 if ($this->import($variables)) {
		 	// process me
		 }
	}

	/**
	 * @return string
	 */
	function dumpHead(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['action']));
		Assert::isFalse(isset($htmlAttributes['method']));

		$htmlAttributes['action'] = $this->action;
		$htmlAttributes['method'] = $this->method;

		return HtmlUtil::getTagCap('form', $htmlAttributes);
	}

	/**
	 * @return string
	 */
	function dumpHidden()
	{
		//fieldsToSign
		//
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
}

?>