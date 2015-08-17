<?php
Loader::library("email_obfuscator_default", AutomaticEmailObfuscatorPackage::PKG_HANDLE);
class EmailObfuscatorRtl extends EmailObfuscatorDefault {
	
	public function on_page_view() {
		$html = Loader::helper("html");
		$v = View::getInstance();
		$v->addHeaderItem($html->javascript("email_deobfuscator_rtl.js", "automatic_email_obfuscator"));
	}
	
	public function obfuscateMail($email) {
		$ret = '<span style="unicode-bidi:bidi-override; direction: rtl;">';
		$ret .= strrev($email);
		$ret .= '</span>';
		return $ret;
	}
	
	public function obfuscateMailtoLinkHref($href) {
		return "#MAIL:" . strrev(str_replace("mailto:", "", $href));
	}
	
}
?>