<?php
Loader::library("email_obfuscator_default", AutomaticEmailObfuscatorPackage::PKG_HANDLE);
class EmailObfuscatorHtml extends EmailObfuscatorDefault {
	
	public function on_page_view() {
		$html = Loader::helper("html");
		$v = View::getInstance();
		$v->addHeaderItem($html->javascript("email_deobfuscator_html.js", "automatic_email_obfuscator"));
	}
	
	public function obfuscateMail($email) {
		$ret = "";
		$email = str_replace("@", "(at)", $email);
		for ($i=0; $i < strlen($email); $i++) {
			$ret .= "&#" . ord($email[$i]) . ";";
		}
		return $ret;
	}
	
	public function obfuscateMailtoLinkHref($href) {
		$href = $this->obfuscateMail(str_replace("mailto:", "", $href));
		return "#MAIL:" . $href;
	}
	
}
?>