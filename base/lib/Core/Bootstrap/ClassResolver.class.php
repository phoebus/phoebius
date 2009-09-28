<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Implements a class resolving mechanism used to search finles containing the requested classes
 * to be autoloaded
 * @ingroup Bootstrap
 */
abstract class ClassResolver extends InternalSegmentCache implements IClassResolver
{
	private $foundClasspaths = array();
	private $paths = array();
	private $extension = 'php';
	private $useIncludePath = true;
	private $exclusionRegexps = array(
		'/^\./'
	);

	/**
	 * Searches for the file containing the requested class withing the specified directory
	 * @param string $classname
	 * @param string $rootDirectory
	 * @return string|null returns the absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	abstract protected function findFilePath($classname, $rootDirectory);

	function __construct($useIncludePath = true)
	{
		Assert::isBoolean($useIncludePath);

		$this->useIncludePath = $useIncludePath;
	}

	/**
	 * Drops a resolver cache
	 * @return ClassResolver an object itself
	 */
	function clearCache()
	{
		$this->dropCache();

		return $this;
	}

	/**
	 * Gets the path to a file containing the requested class. If path is not found, NULL
	 * is returned
	 * @param string $classname
	 * @return string|null returns the absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	function getClassPath($classname, $useCacheOnly = false)
	{
		return $this->resolveClassPath($classname, true, $useCacheOnly);
	}

	/**
	 * Searches for the file containing the requested class, and loads it to the scope
	 * @param string $classname
	 * @return boolean whether or not the file was successfully found and loaded
	 */
	function loadClassFile($classname, $useCacheOnly = false)
	{
		$classpath = $this->resolveClassPath($classname, false, $useCacheOnly);

		if ($classpath) {
			try {
				include $classpath;

				return true;
			}
			catch (ExecutionContextException $e) {
				if ($e->getSeverity() == E_WARNING) {
					$trace = $e->getTrace();
					$ex = reset($trace);

					if (isset($ex['args'])) {
						$lastArg = end($ex['args']);
						if (
								   (isset($lastArg['classname']) && $lastArg['classname'] == $classname)
								&& (isset($lastArg['classpath']) && $lastArg['classpath'] == $classpath)
						) {
							unset($this->foundClasspaths[$classname]);
							$this->uncache($classname);

							return $this->resolveClassPath($classname, false, false);
						}
					}
				}

				throw $e;
			}
		}

		return false;
	}

	/**
	 * Adds the custom path to be scanned while resolving the files containing classes
	 * @param string $path
	 * @return ClassResolver
	 */
	function addPath($path)
	{
		foreach (explode(PATH_SEPARATOR, $path) as $path) {
			$this->paths[] = $path;
		}

		return $this;
	}

	/**
	 * Adds a regexp that is applied for each directory entry to skip it
	 * @param string $regexp
	 * @return ClassResolver
	 */
	function addExludeRegexp($regexp)
	{
		Assert::isScalar($regexp);

		$this->exclusionRegexps[] = $regexp;

		return $this;
	}

	/**
	 * Gets the extension that is postfixed to the file names containing classes while resolving
	 * @return string
	 */
	function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Sets the extension (excluding the dot) to be postfixed to the file names containing classes
	 * while resolving
	 * @param string $extension
	 * @return ClassResolver
	 */
	function setExtension($extension)
	{
		Assert::isScalar($extension);

		$this->extension = ltrim($extension, '.');

		return $this;
	}

	/**
	 * Gets the identifier of the class resolver, based on the paths to be scanned while resolving
	 * @return string
	 */
	function getId()
	{
		//sort the path list to avoid different Ids between equal resolvers
		$paths = $this->paths;
		sort($paths);

		return sha1(
			   APP_GUID
			 . get_class($this)
			 . $this->extension
			 . join(PATH_SEPARATOR, $paths)
			 . ($this->useIncludePath ? get_include_path() : 0)
		);
	}

	/**
	 * Gets the unique identifier of the class that needed the cache
	 * @return scalar
	 */
	protected function getCacheId()
	{
		return $this->getId();
	}

	/**
	 * Returns the list of custom paths to be scanned while resolving the classes
	 * @return array
	 */
	function getActualPaths()
	{
		return array_merge(
			$this->useIncludePath
				? explode(PATH_SEPARATOR, get_include_path())
				: array(),
			$this->paths
		);
	}

	/**
	 * Scans the paths for the given class name
	 * @return string|null
	 */
	private function scanClassPaths($classname)
	{
		foreach($this->getActualPaths() as $path) {
			if ($this->isSkippable($path)) {
				continue;
			}

			if (($filePath = $this->findFilePath($classname, $path))) {
				return $filePath;
			}
		}

		return null;
	}

	/**
	 * @return boolean
	 */
	protected function isSkippable($path)
	{
		if (!is_dir($path)) {
			return true;
		}

		$base = basename($path);
		foreach ($this->exclusionRegexps as $exclusionMask) {
			try {
				return preg_match($exclusionMask, $base);
			}
			catch (ExecutionContextException $e) {
				Assert::isUnreachable(
					'wrong regexp given as an exclusion mask: %s',
					$exclusionMask
				);
			}
		}
	}

	/**
	 * Resolves the file path containing the requested class. Firstly, searches withing the cache
	 * provided by {@link InternalSegmentCache}, then invokes the path scanner and comparer
	 * provided by a descendant class
	 * @param string $classname
	 * @param boolean $checkIfExists
	 * @return string|null returns the absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	private function resolveClassPath($classname, $checkIfExists, $useCacheOnly = false)
	{
		Assert::isScalar($classname);
		Assert::isBoolean($checkIfExists);

		if (isset($this->foundClasspaths[$classname])) {
			return $this->foundClasspaths[$classname];
		}

		$classpath = null;
		if ($this->isCached($classname)) {
			$classpath = $this->getCached($classname);
			if ($checkIfExists && $classpath) {
				if (!file_exists($classpath)) {
					$this->uncache($classname);
					$classpath = null;
				}
			}
		}

		if (!$classpath && $useCacheOnly != true) {
			$classpath = $this->scanClassPaths($classname);

			if ($classpath) {
				$this->cache($classname, $classpath);
			}
		}

		if ($classpath) {
			$this->foundClasspaths[$classname] = $classpath;
		}

		return $classpath;
	}
}

?>