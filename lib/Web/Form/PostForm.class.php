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
class PostForm extends Form
{
	/**
	 * @var callback[]
	 */
	private $buttons = array();

	/**
	 * @var XorCipher
	 */
	private $signer;

	/**
	 * @var bool
	 */
	private $signed;

	/**
	 * @var IFormControl[]
	 */
	private $fieldsToSign = array();

	/**
	 * @param string $id Form identifier
	 * @param HttpUrl $action form action (url to which it will be sumbitted)
	 * @param string $sign form signature to protect it from vulnerabilities;
	 */
	function __construct($id, HttpUrl $action, $sign)
	{
		$this->signer = new XorCipherer($sign);

		parent::__construct($id, $action, RequestMethod::post());
	}

	/**
	 * Whether form is already signed
	 * @return bool
	 */
	function isSigned()
	{
		return $this->signed;
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
	 * Handles request
	 * @return mixed true on success, array of errors on handle failure
	 * @throws PostFormException
	 */
	function handle(WebRequest $request)
	{
		if (!$request->getRequestMethod()->equals($this->getMethod()))
			throw new PostFormException("Wrong request method");

		$variables = array_replace(
			$request->getPostVars(),
			$request->getFilesVars()
		);

		if ($this->isSigned()) {
			$signName = $this->getSignName();
			if (!isset($variables[$signName]))
				throw new PostFormException("Missing sign");

			if (!$this->importSign($variables[$signName]))
				throw new PostFormException("Malformed sign");

			if (isset($this[$this->getReferrerName()])) {
				if ($this[$this->getReferrerName()] != (string)$request->getHttpReferer()) {
					throw new PostFormException();
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
	function sign(WebRequest $request)
	{
		Assert::isFalse($this->isSigned(), 'form already signed');

		$this->setHiddenValue($this->getReferrerName(), $request->getHttpReferer());

		$this->addControl(FormControl::hidden($this->getSignName(), $this->exportSign()));

		$this->signed = true;

		return $this;
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
	protected function dumpHidden()
	{
		Assert::isTrue($this->isSigned(), 'sign me please me');

		return parent::dumpHidden();
	}

	protected function importSign($string)
	{
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
		$data = array();
		foreach ($this->fieldsToSign as $name => $control) {
			$data[$name] = $control->getValue();
		}

		return $this->signer->encrypt($data);
	}

	protected function getSignName()
	{
		return '__' . sha1($this->signer->encrypt($this->getId()));
	}

	protected function getReferrerName()
	{
		return $this->getSignName() . '_referrer';
	}
}

?>