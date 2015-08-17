<?php
/*
 * Vigenere Cipher for Automatic Email Obfuscator
 * Ciphering Library
 *
 * Copyright 2011, Nour Akalay
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * December 2011
 */
Loader::library("email_obfuscator_default", "automatic_email_obfuscator");
class EmailObfuscatorVigenere extends EmailObfuscatorDefault {

	public function on_page_view() {
		$html = Loader::helper("html");
		$v = View::getInstance();
		$v->addHeaderItem($html->javascript("email_deobfuscator_vgnr.js", "automatic_email_obfuscator"));
	}

	public function obfuscateMail($email) {
		// check http://en.wikipedia.org/wiki/Vigen%C3%A8re_cipher for an explanation
		//of the vigenere cipher.

		//$alphabet contains only characters accepted in email addresses formation
		$alphabet = '!$%\'&*#+-./0123456789=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
		$alphabet_len = strlen($alphabet);
		//shuffle the alphabet and select a string the length of the email address
		//that string is the key to decipher the email
		$shuffled_alphabet = str_shuffle($alphabet);
		srand(time());
		$email_len = strlen($email);
		$key = substr($shuffled_alphabet, 0, $email_len);
		$key_len = strlen($key);
		
		for ($i=0; $i<$alphabet_len; $i++) {
			$alphabet_matrix[$i] = substr($alphabet, $i) . substr($alphabet, 0, $i);
		}
		$j = -1;
		for ($i=0; $i<$email_len; $i++) {
			$j++;
			if (strpos($alphabet,$email{$i}) === false ) {
				$j--;
				$ciphered_email .= $email{$i};
			} else {
				$pos_in_alpha = strpos($alphabet, $key{$j});
				$ciphered_email .= strtr($email{$i}, $alphabet, $alphabet_matrix[$pos_in_alpha]);
			}
			if ($j === $key_len-1) {
				$j = -1;
			}
		}
		//I concatenate the key and the encrypted email and separate them with ]#[
		//I assume chances for this string to ever appear in a cryoted email are pretty slim
		return $key . "]#[" . $ciphered_email;
	}

  	public function obfuscateMailtoLinkHref($href) {
		$href = $this->obfuscateMail(str_replace("mailto:", "", $href));
		//I replaced the original #MAIL: for paranoiac reasons
		//I believe robots scavenging for emails are designed to go around attempts
		//to obfuscate email addresses. I Also believe they're getting more and more efficient.
		//It seems logical to think that they probably look for strings like "mail"
		//and probably ":" so I just make sure nothing at all even suggests the presence of
		//an email address by using "#Keep#Looking#" (Sadic Humour) and no ":" instead of "#MAIL:".
		//As I said, pure paranoia.
		return "#KEEP#LOOKING#" . $href;
	}
}
?>