<?php
class EmailObfuscationHelper {
	
	private static $_obfuscator = null;
	
	public function __construct() {
		Loader::model("interfaces/email_obfuscator", AutomaticEmailObfuscatorPackage::PKG_HANDLE);
		if (!defined("EMAIL_OBFUSCATOR_CLASS_PACKAGE")) {
			define("EMAIL_OBFUSCATOR_CLASS_PACKAGE", AutomaticEmailObfuscatorPackage::PKG_HANDLE);
		}
		if (!defined("EMAIL_OBFUSCATION_METHOD") && defined("EMAIL_OBFUSCATOR_CLASS")) {
			// Backwards compatibility
			// Do not use this definition in the future
			$th = Loader::helper("text");
			define("EMAIL_OBFUSCATION_METHOD", str_replace("email_obfuscator_", "", $th->uncamelcase(EMAIL_OBFUSCATOR_CLASS)));
		}
		if (!defined("EMAIL_OBFUSCATION_METHOD")) {
			// Default obfuscation method
			define("EMAIL_OBFUSCATION_METHOD", 'html');
		}
		
		$handle = "email_obfuscator_" . EMAIL_OBFUSCATION_METHOD;
		$cls = Object::camelcase($handle);
		if (!class_exists($cls)) {
			Loader::library($handle, EMAIL_OBFUSCATOR_CLASS_PACKAGE);
		}
		self::$_obfuscator = new $cls;
	}
	
	public function on_page_view() {
		if (method_exists(self::$_obfuscator, "on_page_view")) {
			self::$_obfuscator->on_page_view();
		}
	}
	
	public function findAndObfuscate($content) {
		// Regexps modified from the MIT licenced "Transparent Email Obfuscation" add-on:
		// http://www.concrete5.org/marketplace/addons/transparent-email-obfuscation/
		$content = preg_replace_callback('/(<a[^>]*)(href=")(mailto:)([^"]+)([^>]*>)/', create_function('$matches','return EmailObfuscationHelper::mailtoCallback($matches);'), $content);
		$content = preg_replace_callback('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]+\b(?!([^<]+)?>)/i', create_function('$matches','return EmailObfuscationHelper::mailCallback($matches);'), $content);
		return $content;
	}
	
	public static function mailtoCallback($matches) {
		$cb = self::$_obfuscator->getCallback('mailto');
		if (method_exists(self::$_obfuscator, $cb)) {
			$parts = array($matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
			$linkCls = "obfuscated_link";
			$hasClass = false;
			if (strpos($parts[0], "class=\"") !== false) {
				$parts[0] = str_replace("class=\"", "class=\"" . $linkCls . " ", $parts[0]);
				$hasClass = true;
			}
			if (strpos($parts[4], "class=\"") !== false) {
				$parts[4] = str_replace("class=\"", "class=\"" . $linkCls . " ", $parts[4]);
				$hasClass = true;
			}
			if (!$hasClass) {
				$parts[0] = $parts[0] . 'class="' . $linkCls . '" ';
			}
			return $parts[0] . $parts[1] .  call_user_func(array(self::$_obfuscator, $cb), $parts[2] . $parts[3]) . $parts[4];
		}
	}
	
	public static function mailCallback($matches) {
		$cb = self::$_obfuscator->getCallback('email');
		if (method_exists(self::$_obfuscator, $cb)) {
			return '<span class="obfuscated_link_text">' . call_user_func(array(self::$_obfuscator, $cb), $matches[0]) . '</span>';
		}
	}
	
}
?>