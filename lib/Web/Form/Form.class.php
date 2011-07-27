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
	 * @var XorCipher
	 */
	private $signer;

	/**
	 * @var boolean
	 */
	private $signed;

	/**
	 * @var HttpUrl|null
	 */
	private $referrer;

	/**
	 * @var IFormControl[]
	 */
	private $controls = array();

	/**
	 * @var callback[]
	 */
	private $buttons = array();

	private $fieldsToSign = array();
	private $errors = array();
	private $hasInnerErrors = false;

	/**
	 * @param string $id Form identifier
	 * @param HttpUrl $action form action (url to which it will be sumbitted); may be null
	 * @param string|null $sign form signature to protect it from vulnerabilities;
	 */
	function __construct($id, HttpUrl $action = null, $sign = null)
	{
		Assert::isScalar($id);

		$this->id = $id;
		$this->action = $action;
		$this->method = new RequestMethod(RequestMethod::POST);

		if ($sign)
			$this->signer = new XorCipherer($sign);
	}

	function setCallerUrl(HttpUrl $url)
	{
		$this->referrer = $url;

		return $this;
	}

	/**
	 * If the form may be protected from various vulnerabilites
	 * @return bool
	 */
	function isSignable()
	{
		return !!$this->signer;
	}

	/**
	 * Whether form is already signed
	 * @return bool
	 */
	function isSigned()
	{
		return $this->signed;
	}

	function __get($name)
	{
		return $this->getControl($name)->getValue();
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

		Assert::isCallback($callback);

		$this->addControl(FormControl::button($name, $label));

		$this->buttons[$name] = $callback;

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

	/**
	 * Handles request
	 * @return mixed true on success, array of errors on handle failure
	 */
	function handle(WebRequest $request)
	{
		if (!$request->getRequestMethod()->equals($this->method))
			throw new FormException("Wrong request method");

		$variables = $request->getPostVars();

		if ($this->isSigned()) {
			$signName = $this->getSignName();
			if (!isset($variables[$signName]))
				throw new FormException("Missing sign");

			if (!$this->importSign($variables[$signName]))
				throw new FormException("Malformed sign");

			if (isset($this[$this->getReferrerName()])) {
				if ($this[$this->getReferrerName()] != (string)$request->getHttpReferer()) {
					throw new FormException();
				}
			}
		}

		foreach ($this->buttons as $id => $callback) {
			if (isset($variables[$id])) {
				call_user_func($callback, $variables);
				return;
			}
		}

		$this->process($variables);
	}

	/**
	 * Signs the form. This method may be overridden to import fields to be signed, just call Form::setHiddenValue()
	 * @return Form
	 */
	function sign(WebRequest $request = null)
	{
		Assert::isNotEmpty($this->signer, 'form cannot be signed');
		Assert::isFalse($this->signed, 'form already signed');

		if ($request)
			$this->setHiddenValue($this->getReferrerName(), $request->getHttpReferer());

		$this->addControl(FormControl::hidden($this->getSignName(), $this->exportSign()));

		$this->signed = true;

		return $this;
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
	 * Overridden. Called when form is submitted
	 */
	protected function process(array $variables)
	{
		 if ($this->import($variables)) {
		 	// process me
		 }
	}

	/**
	 * Signs the form and returns <form> cap and hidden fields
	 * @return string
	 */
	function dumpHead(array $htmlAttributes = array())
	{
		Assert::isTrue($this->isSigned(), 'sign me please me');
		Assert::isFalse(isset($htmlAttributes['action']));
		Assert::isFalse(isset($htmlAttributes['method']));

		$htmlAttributes['action'] = $this->action;
		$htmlAttributes['method'] = $this->method;

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

	protected function importSign($string)
	{
		Assert::isNotEmpty($this->signer, 'form is sign-less');

		$decrypted = $this->signer->decrypt($string);
		if (!$decrypted)
			return false;

		try {
			$data = unserialize($decrypted);
		}
		catch (ExecutionContextException $e) {
			return false;
		}

		foreach ($data as $key => $value)
			$this->setHiddenValue($key, $value);

		return true;
	}

	protected function exportSign()
	{
		Assert::isNotEmpty($this->signer, 'form is sign-less');

		$data = array();
		foreach ($this->fieldsToSign as $name => $control) {
			$data[$name] = $control->getValue();
		}

		return $this->signer->encrypt($data);
	}

	protected function getSignName()
	{
		Assert::isNotEmpty($this->signer, 'form is sign-less');

		return '__' . sha1($this->signer->encrypt($this->id));
	}

	protected function getReferrerName()
	{
		return $this->getSignName() . '_referrer';
	}

}

?>