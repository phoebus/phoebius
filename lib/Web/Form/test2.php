<?php
class FormControlError extends Enumeration {
	const MISSING = 1;
	const WRONG = 2;
	
	static function missing() 
	{
		return new self (self::MISSING);
	}
	
	static function wrong() 
	{
		return new self (self::WRONG);
	}
}