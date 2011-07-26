<?php






/// Filters  + mutators
interface _IFormControl {
	function hasError();
	function isMissing();
	function isWrong();
	function reset();
	
	// plain value
	function importValue($value);
	function exportValue();
	
	// mutated value
	function setValue($value);
	function getValue();
	
	// default value
	function importDefaultValue();
	function exportDefaultValue();
	
	function setDefaultValue();
	function getDefaultValue();
	
	
}

interface IFormValueFilter {
	function apply($value);
}

interface IFormValueMutator {
	function getObject($plainValue);
	function getPlain($object);
}

// {{{
class SelectFormControl {
	function importList(array $hash);
	function setList(array $objects);
}

$s = SelectFormControl::create(
	OQL::select('name from User')->getHashById(),
	$this->getCurrentUser()->getId()
);
$s->importValue(1); // ok
$s->setValue($this->getCurrentUser());

$s->importDefaultValue($this->getCurrentUser());
// }}}



