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
	 * @var array of FormControl by id
	 */
	private $controls = array();
	
	/**
	 * @var array of FormControl by id
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
		$id = $control->getId();
		Assert::isFalse(isset($this->controls[$id]), 'control with id %s already exists', $id);
		
		$this->controls[$id] = $control;
		
		return $this;
	}
	
	/**
	 * @return Form itself
	 */
	function addButton($id, $name, $callback = null)
	{
		if (is_string($callback)) {
			Assert::isTrue(method_exists($this, $callback), 'unknown method %s::%s', get_class($this), $callback);
			
			$callback = array($this, $callback);
		}

		$button = FormControl::button($id, $name, $callback);
		
		$this->addControl($button);
		
		$this->buttons[$id] = $button;
		
		return $this;
	}
	
	/**
	 * @return Form itself
	 */
	function setHiddenValue($id, $value)
	{
		$field = FormControl::hidden($id, $value);
		
		$this->addControl($field);
		
		$this->fieldsToSign[$id] = $field;
		
		return $this;
	}
	
	function getControl($id)
	{
		Assert::hasIndex($this->controls, $id, 'know nothing about control %s within form %s', $id, $this->id);
		
		return $this->controls[$id];
	}
	
	/**
	 * Handles requst
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
			$id = $control->getId();
			$value = 
				isset($variables[$id])
					? $variables[$id]
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
			$yield[$control->getId()] = $control->getValue();
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