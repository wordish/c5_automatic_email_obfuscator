<?php
interface EmailObfuscator {
	
	public function getCallback($key);
	public function setCallback($key, $callback);
	
}
?>