<?php
/**
 * Found on http://www.sitepoint.com/forums/showthread.php?t=368939
 * @author pachanga
 */

class SerializableContainer implements IEntityContainer {

	protected $subject;
	protected $serialized;
	protected $class_paths = array();

	public function __construct($subject) {
		$this->subject = $subject;
	}

	public function &getSubject() {
		if($this->serialized) {
			$this->_includeFiles();
			$this->subject = unserialize($this->serialized);
			unset($this->serialized);
		}
		return $this->subject;
	}

	function __sleep() {
		$this->serialized = serialize($this->subject);
		$this->_fillClassPathInfo($this->serialized);
		return array('serialized', 'class_paths');
	}

	private function _includeFiles() {
		foreach($this->class_paths as $path)
		require_once($path);
	}

	private function _fillClassPathInfo($serialized) {
		$classes = $this->_extractSerializedClasses($serialized);
		$this->class_paths = array();
		foreach($classes as $class) {
			$reflect = new ReflectionClass($class);
			$this->class_paths[] = $reflect->getFileName();
		}
	}

	private function _extractSerializedClasses($str) {
		if(preg_match_all('~(\||;)O:\d+:"([^"]+)":\d+:\{~', $str, $m))
		return array_unique($m[2]);
		else
		return array();
	}
}

?>