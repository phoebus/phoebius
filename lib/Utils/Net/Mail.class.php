<?php
/* ***********************************************************************************************
*
* Phoebius Framework
*
* **********************************************************************************************
*
* Copyright (c) 2009 Scand Ltd.
*
* This program is free software; you can redistribute it and/or modify it under the terms
* of the GNU Lesser General Public License as published by the Free Software Foundation;
* either version 3 of the License, or (at your option) any later version.
*
* You should have received a copy of the GNU Lesser General Public License along with
* this program; if not, see <http://www.gnu.org/licenses/>.
*
********************************************************************************************** */

/**
 * @ingroup Utils_Net
 *
 * Note: when relay rejects your mail, try to
 * setSendmailAdditionalArgs('-f from.addr@example.com').
 * See 'man sendmail' for details.
 *
 * Original code is written by Anton E. Lebedevich for onPHP.org
 */
final class Mail
{
	private $to;
	private $cc;
	private $text;
	private $subject;
	private $from;
	private $encoding = 'UTF-8';
	private $contentType = 'text/plain';
	private $returnPath;

	private $sendmailAdditionalArgs;

	/**
	 * @return Mail
	 */
	public static function create()
	{
		return new self;
	}

	/**
	 * @return Mail
	 * @throws SendmailException
	 */
	public function send()
	{
		Assert::isNotEmpty($this->to, 'mail to is not set');

		$encoding = $this->encoding;
		$to = $this->to;
		$from = $this->from;
		$subject =
			"=?".$encoding."?B?"
			.base64_encode($this->subject)
			."?=";
		$body = $this->text;
		$returnPath = $this->returnPath;

		$headers = null;

		$returnPathAtom =
			$returnPath
				? $returnPath
				: $from;

		if ($from) {
			$headers .= "From: ".$from."\n";
			$headers .= "Return-Path: ".$returnPathAtom."\n";
		}

		if ($this->cc)
			$headers .= "Cc: ".$this->cc."\n";

		$headers .=
			"Content-type: ".$this->contentType
			."; charset=".$encoding."\n";

		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$headers .= "Date: ".date('r')."\n";

		if (
			!mail(
				$to, $subject, $body, $headers,
				$this->getSendmailAdditionalArgs()
			)
		)
			throw new SendmailException();

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setTo($to)
	{
		$this->to = $to;

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setCc($cc)
	{
		$this->cc = $cc;

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setText($text)
	{
		$this->text = $text;

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setFrom($from)
	{
		$this->from = $from;

		return $this;
	}

	/**
	 * @return Mail
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;

		return $this;
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return Mail
	 */
	public function setContentType($contentType)
	{
		$this->contentType = $contentType;

		return $this;
	}

	public function getSendmailAdditionalArgs()
	{
		return $this->sendmailAdditionalArgs;
	}

	/**
	 * @return Mail
	 */
	public function setSendmailAdditionalArgs($sendmailAdditionalArgs)
	{
		$this->sendmailAdditionalArgs = $sendmailAdditionalArgs;

		return $this;
	}

	public function getReturnPath()
	{
		return $this->returnPath;
	}

	/**
	 * @return Mail
	 */
	public function setReturnPath($returnPath)
	{
		$this->returnPath = $returnPath;

		return $this;
	}
}

?>