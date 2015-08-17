<?php
class EmailObfuscatorDefault implements EmailObfuscator {
	
	protected $_callbacks = array(
		'mailto' => 'obfuscateMailtoLinkHref',
		'email' => 'obfuscateMail'
	);
	
	public function getCallback($key) {
		return $this->_callbacks[$key];
	}
	
	public function setCallback($key, $callback) {
		if (!method_exists($this, $callback)) {
			throw new Exception(sprintf(t("The following email obfuscation callback does not exist: %s! Tried to add that for callback key: %s."), $callback, $key));
		}
		$this->_callbacks[$key] = $callback;
	}
	
}
?>