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

/** *
 * Low-level mechanism for matching the request and cutting the named parameters
 * out of it.
 *
 * Features:
 *  - matching path against the pattern
 *  - matching query string parameters against the regular expression constraints
 *  - setting custom parameters
 *
 * Route.match() determines whether request matches the specified patterns, and returns
 * an object containing the parameters resolved on parsing the request
 *
 *
 * Path pattern matching:
 * ======================
 *
 * Path pattern is particle-based: it defines the constrains for each particle of a path
 * to match. Every particle may be assigned to a named parameter. Any particle may be checked
 * against a regular expression.
 *
 * Pattern: /
 * Matching path: /
 * Result: {}
 *
 * Pattern: /blah
 * Matching paths: /blah
 * Result: {}
 *
 * Pattern: /blah/
 * Matching paths: /blah/
 * Result: {}
 *
 * Pattern: /:placheholder
 * Matching paths: /<anything>
 * Result: { placeholder: "<anything>" }
 *
 * Pattern: /:placeholder/
 * Matching paths: /<anything>/
 * Result: { placeholder: "<anything>" }
 *
 * Pattern: /blah:placeholder/
 * Matching paths: /blah/
 * Result: { placeholder: "blah" }
 *
 * Pattern: /blah|blah2:placeholder/
 * Matching paths: /blah/, /blah2/
 * Result: { placeholder: "blah" } or { placeholder: "blah2" }
 *
 * Pattern: /blah/[0-9]+:id/
 * Matching paths: /blah/1/, /blah/105/
 * Result: { id: 1 } or { id: 105 }
 *
 *
 * Query string matching:
 * ======================
 *
 * Just pass an object of regular expressions to match each named parameter against it.
 *
 * For example, if you pass the following array:
 * > { id: /^[0-9]+$/ }
 *
 * then route will match the request only if it contains a numeric "id" parameter:
 * Will match: /?id=100
 * Will NOT match: /?id=blah
 *
 */
class Route
{
	/**
	 * @var _PathPattern
	 */
	private $pathMatcher;
	private $queryStringRegs = array();
	private $routeData = array();
	private $method;
	private $name;

	/**
	 * @return Route
	 */
	static function create(
			$name = null,
			$pattern = null,
			array $data = array(),
			RequestMethod $method = null
	) {
		return new self ($name, $pattern, $data, $method);
	}

	/**
	 * @return Route
	 */
	static function get(
			$name = null,
			$pattern = null,
			array $data = array()
	) {
		return new self ($name, $pattern, $data, RequestMethod::get());
	}

	/**
	 * @return Route
	 */
	static function post(
			$name = null,
			$pattern = null,
			array $data = array()
	) {
		return new self ($name, $pattern, $data, RequestMethod::get());
	}

	/**
	 * @return Route
	 */
	static function any(
			$name = null,
			$pattern = null,
			array $data = array()
	) {
		return new self ($name, $pattern, $data);
	}

	function __construct(
			$name = null,
			$pattern = null,
			array $data = array(),
			RequestMethod $method = null
		)
	{
		Assert::isScalarOrNull($name);
		Assert::isScalarOrNull($pattern);

		if ($pattern) {
			$path = parse_url($pattern, PHP_URL_PATH);
			$query = parse_url($pattern, PHP_URL_QUERY);

			if ($path) {
				$this->pathMatcher = new _PathPattern($path);
			}

			if ($query) {
				parse_str($query, $this->queryStringRegs);
			}
		}

		$this->name = $name;
		$this->routeData = $data;
		$this->method = $method;
	}

	/**
	 * Gets the route name
	 * @return string|null
	 */
	function getName()
	{
		return $this->name;
	}

	function match(WebRequest $request)
	{
		if ($this->method) {
			if ($this->method->getValue() != $request->getRequestMethod()) {
				return;
			}
		}

		$url = $request->getHttpUrl();
		$data = $this->routeData;

		if ($this->pathMatcher) {
			$pathData = $this->pathMatcher->match($url->getVirtualPath());
			if (!is_array($pathData))
				return;

			$data = array_merge($data, $pathData);
		}

		$query = $url->getQuery();
		foreach ($this->queryStringRegs as $qsArg => $qsReg) {
			if (
					isset($query[$qsArg])
					&& ($qsReg || preg_match($qsReg, $query[$qsArg]))
			) {
				$data[$qsArg] = $query[$qsArg];
			}
			else return;
		}

		return $data;
	}

	/**
	 * @return HttpUrl
	 */
	function makeUrl(array $data, HttpUrl $url = null)
	{
		$data = array_replace_recursive($this->routeData, $data);
		if (!$url)
			$url = new HttpUrl();

		if ($this->pathMatcher)
			$url->setPath($this->pathMatcher->reverse($data));

		foreach ($this->queryStringRegs as $qsArg => $qsReg) {
			if ($qsReg) {
				Assert::hasIndex($data, $qsArg);
				Assert::isTrue(preg_match($qsReg, $data[$qsArg]));
			}

			if (isset($data[$qsArg]))
				$url->addQueryArgument($qsArg, $data[$qsArg]);
		}

		// import untouched data variables
		$used = array_replace(
			$this->pathMatcher
				? array_fill_keys($this->pathMatcher->getPlaceholderKeys(), null)
				: array(),
			$this->routeData
		);
		$onlyNew = array_diff_key(
			$data,
			$used
		);
		foreach ($onlyNew as $key => $value)
			$url->addQueryArgument($key, $value);

		return $url;
	}
}

/**
 * @internal
 */
final class _PathPattern
{
	const DELIMITER = '/';

	private $pattern;
	private $parts = array();
	private $particles = array();
	private $placeholders = array();

	private $isGreedy = false;

	function __construct($pattern)
	{
		Assert::isScalar($pattern);

		$this->pattern = $pattern;

		$this->setupPattern();
	}

	function getPlaceholderKeys()
	{
		return array_diff($this->placeholders, array(""));
	}

	function match($path)
	{
		$path_parts = explode(self::DELIMITER, $path);

		if (sizeof($path_parts) != sizeof($this->particles)) {
			return false;
		}

		if ($this->isGreedy) {
			$greedyCapture = array_slice($path_parts, sizeof($this->particles) - 1);
			$path_parts = array_slice($path_parts, 0, sizeof($this->particles) - 1);
			$path_parts[] = join(self::DELIMITER, $greedyCapture);
		}

		$data = array();

		foreach ($this->particles as $idx => $partRegex) {
			$part = $path_parts[$idx];
			$placeholder = $this->placeholders[$idx];
			if (preg_match($partRegex, $part)) {
				if ($placeholder)
					$data[$placeholder] = $part;
			}
			else {
				$data = null;
				break;
			}
		}

		return $data;
	}

	function reverse(array $data)
	{
		$path = array();

		foreach ($this->parts as $i => $part) {
			$placeholderName = $this->placeholders[$i];

			if ($this->placeholders[$i]) {
				Assert::hasIndex(
					$data, $this->placeholders[$i],
					'specify value for placehodler `%s` to reverse path `%s`',
					$placeholderName, $this->pattern
				);

				$placeholderValue = $data[$placeholderName];

				if ($placeholderValue instanceof IdentifiableOrmEntity)
					$placeholderValue = $placeholderValue->_getId();

				Assert::isTrue(
					preg_match($this->particles[$i], $placeholderValue),
					'value `%s` for placeholder `%s` should match regex %s',
					$placeholderValue, $placeholderValue, $this->particles[$i]
				);

				$path[] = $placeholderValue;
			}
			else {
				$path[] = $part;
			}
		}

		return join('/', $path);
	}

	private function setupPattern()
	{
		foreach (explode(self::DELIMITER, $this->pattern) as $part) {
			Assert::isFalse(
				$this->isGreedy,
				'only the last placeholder can be greedy, pattern %s is wrong',
				$this->pattern
			);

			$placeholder = null;

			if (preg_match('/:[a-zA-Z0-9]+\*?$/', $part)) {
				$p = explode(":", $part);
				$placeholder = end($p);
				if (substr($placeholder, -1) == '*') {
					$this->isGreedy = true;
					$placeholder = substr($placeholder, 0, -1);
				}
				$part = join(":", array_slice($p, 0, -1));

				if (!$part) {
					$part = ".+"; // empty named part matched everything
				}
			}

			$this->parts[] = $part;
			$this->particles[] = "{^{$part}$}";
			$this->placeholders[] = $placeholder;
		}
	}
}
