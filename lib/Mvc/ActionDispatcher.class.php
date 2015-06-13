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
 ************************************************************************************************/

/**
 * Invokes a Controller class (that implements IController) with the provided route data
 *
 * @ingroup Mvc
 */
class ActionDispatcher implements IDispatcher
{
	/**
	 * @var RouteData
	 */
	private $routeData;

	/**
	 * @var WebRequest
	 */
	private $request;

	/**
	 * @param RouteData $routeData
	 * @param WebRequest $request
	 * @return void
	 */
	function dispatch(RouteData $routeData, WebRequest $request)
	{
		$this->routeData = $routeData;
		$this->request = $request;

		if (!isset($routeData['controller'], $routeData['action']))
			throw new DispatchException(
				'Missing controller or action inside RouteData',
				$routeData,
				$request
			);

		$className = $this->getClassName($routeData['controller']);
		$methodName = $this->getMethodName($routeData['action']);

		if (!class_exists($className))
			throw new DispatchException(
				$className . ' controller not found',
				$routeData,
				$request
			);

		$controllerObject = new $className($routeData, $request);

		$rc = new ReflectionObject($controllerObject);

		if (!$rc->hasMethod($methodName))
			throw new DispatchException(
				$className . '::' . $methodName . ' not found',
				$routeData,
				$request
			);

		$result = $this->invoke($controllerObject, $rc->getMethod($methodName));

		if (empty($result) || is_array($result)) {
			$result = new ViewResult(
				new View("{$routeData['controller']}/{$routeData['action']}.view.php"),
				is_array($result) ? $result : array()
			);
		}
		else if (is_scalar($result)) {
			$result = new ContentResult($result);
		}


		if ($result instanceof IActionResult) {
			$result->handle($request->getResponse());
		}
	}

	protected function getRouteData()
	{
		return $this->routeData;
	}

	protected function getRequest()
	{
		return $this->request;
	}

	/**
	 * Gets the name of controller class. This method DOES NOT check the existance of the method.
	 *
	 * By default, this method appends the "Controller" postfix to the value of "controller"
	 * parameter and upercases the first letter
	 *
	 * @param string $controllerName requested controller
	 *
	 * @return string
	 */
	protected function getClassName($controller)
	{
		$controller = preg_replace_callback('{\-(\w)}', function($m){return strtoupper($m[1]);}, $controller);

		return ucfirst($controller) . 'Controller';
	}

	/**
	 * Gets the name of action method. This method DOES NOT check the existance of the method
	 *
	 * @param string $action requested action
	 *
	 * @return string
	 */
	protected function getMethodName($action)
	{
		$action = preg_replace('{\-(\w)}', '_$1', $action);

		return 'action_' . ($action);
	}

	/**
	 * Collects arguments to be passed to the found action method and invokes it returning its result.
	 *
	 * @param string $action requested action
	 * @param ReflectionMethod $method a method that corresponds the action
	 *
	 * @return mixed result that may be processed by ActionBasedController::makeActionResult()
	 */
	protected function invoke(ActionController $controller, ReflectionMethod $method)
	{
		$argumentsToPass = array();

		foreach ($method->getParameters() as $parameter) {
			$argumentsToPass[$parameter->name] = $this->filterArgumentValue($controller, $method, $parameter);
		}

		return $method->invokeArgs($controller, $argumentsToPass);
	}

	// {{{ action resolver

	/**
	 * Gets the actual parameter value to be used when invoking action method
	 *
	 * @param ReflectionParameter $argument
	 *
	 * @return mixed
	 */
	protected function filterArgumentValue(ActionController $controller, ReflectionMethod $method, ReflectionParameter $argument)
	{
		$get = $this->request->getGetVars();
		if (isset($get[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $this->request[$argument->name]);
		}
		else if (isset($this->routeData[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $this->routeData[$argument->name]);
		}
		else {
			$value = null;
		}

		if (!is_null($value)) {
			return $value;
		}

		// check whether it is optional or have the default value
		if ($argument->allowsNull()) {
			return null;
		}
		if ($argument->isDefaultValueAvailable()) {
			return $argument->getDefaultValue();
		}
		elseif ($argument->isArray()) {
			return array();
		}
		else {
			throw new DispatchException(
				'Unable to call '.get_class($controller). '::' . $method->getName().': ' . $argument->getName() . ' missing',
				$this->routeData,
				$this->request
			);
		}
	}

	/**
	 * Casts the parameter value which is expected to be an array according to
	 * action method signature
	 *
	 * @param string $action requested action name
	 * @param string $name name of the parameter
	 * @param mixed $value obtained value
	 *
	 * @return array|null
	 */
	protected function getArrayValue($name, $value)
	{
		if (is_array($value)) {
			return $value;
		}
	}

	/**
	 * Casts the parameter value which is expected to be an instance of a class according to action
	 * method signature
	 *
	 * @param string $action requested action name
	 * @param string $name name of the parameter
	 * @param ReflectionClass $class
	 * @param mixed $value obtained value
	 *
	 * @return object|null
	 */
	protected function getClassValue($name, ReflectionClass $class, $value)
	{
		if (is_object($value)) {
			if (is_a($value, $class->getName())) {
				return $value;
			}
		}
		else if ($class->implementsInterface('IObjectCastable')) {
			try {
				return call_user_func_array(
					array($class->name, 'cast'),
					array($value)
				);
			}
			catch (CastException $e){}
		}
		else if ($class->implementsInterface('IDaoRelated') && is_scalar($value)) {
			try {
				$dao = call_user_func(array($class->name, 'dao'));
				return $dao->getEntityById($value);
			}
			catch (OrmEntityNotFoundException $e){}
		}
	}

	/**
	 * Casts the obtained value to the type expected in action method signature.
	 *
	 * This is low-level method. Consider reimplementing
	 * ActionBasedController::getClassValue() and
	 * ActionBasedController::getArrayValue()
	 *
	 * @param ReflectionParameter $argument
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function getActualParameterValue(ReflectionParameter $argument, $value)
	{
		if ($argument->isArray()) {
			return $this->getArrayValue($argument->name, $value);
		}
		else if (($class = $argument->getClass())) {
			return $this->getClassValue($argument->name, $class, $value);
		}
		else {
			return $value;
		}
	}

	// }}}
}

?>