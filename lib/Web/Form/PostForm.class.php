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
	 * @var bool
	 */
	private $signed;

	/**
	 * @var IFormControl[]
	 */
	private $fieldsToSign = array();

	/**
	 * @var array
	 */
	private $privateValues = array();

	/**
	 * @var XorCipher
	 */
	protected $signer;

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

		$signName = $this->getSignatureFieldName();
		if (!isset($variables[$signName]))
			throw new PostFormException('Missing signature');

		if (!$this->importSignature($variables[$signName]))
			throw new PostFormException('Malformed signature');

		if (isset($this->privateValues['referrer'])) {
			if ($this->privateValues['referrer'] != (string)$request->getHttpReferer()) {
				throw new PostFormException(
					'Unexpected referrer `'.$this->privateValues['referrer'].'`, expected `'.$request->getHttpReferer().'`'
				);
			}
		}

		foreach ($this->buttons as $id => $callback) {
			if (isset($variables[$id]) && is_callable($callback)) {
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

		$this->privateValues['referrer'] = (string) $request->getHttpUrl();

		$this->addControl(FormControl::hidden($this->getSignatureFieldName(), $this->exportSignature()));

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

	protected function importSignature($string)
	{
		$decrypted = $this->signer->decrypt($string);
		if (!$decrypted)
			return false;

		try {
			$data = unserialize($decrypted);
		}
		catch (ErrorException $e) {
			return false;
		}

		foreach ($data['fields'] as $key => $value)
			$this->setHiddenValue($key, $value);

		foreach ($data['values'] as $key => $value)
			$this->privateValues[$key] = $value;

		return true;
	}

	protected function exportSignature()
	{
		$fields = array();
		foreach ($this->fieldsToSign as $name => $control) {
			$fields[$name] = $control->getValue();
		}

		$values = $this->privateValues;

		return $this->signer->encrypt(serialize(array(
			                                        'fields' => $fields,
			                                        'values' => $values
		                                        )));
	}

	protected function getSignatureFieldName()
	{
		return 'signature:' . sha1($this->signer->encrypt($this->getId()));
	}
}

?>