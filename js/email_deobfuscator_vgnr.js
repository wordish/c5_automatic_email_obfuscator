/*
 * Vigenere Cipher for Automatic Email Obfuscator
 * Deciphering Script
 *
 * Copyright 2011, Nour Akalay
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * December 2011
 */
(function($){

function decrypt(Key, Email_to_decrypt) {
	var Alphabet = "!$%'&*#+-./0123456789=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";
	//var Alphabet = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	var Alphabet_len = Alphabet.length;
	var Email_len = Email_to_decrypt.length;
	var letter = "";
	var Result = "";
	var Alphabet_Matrix = new Array(Alphabet_len);

	for(i=0; i<Alphabet_len; i++) {
		Alphabet_Matrix[i] = Alphabet.substr(i) + Alphabet.substr(0, i);
	}
	var j = -1;
	for(i=0; i<Email_len; i++) {
		j++;
		letter = Email_to_decrypt.charAt(i);
		if(Alphabet.indexOf(letter) == -1) {
			j--;
			Result += letter;
		} else {
			Result += Alphabet.charAt(Alphabet_Matrix[Alphabet.indexOf(Key.charAt(j))].indexOf(letter));
		}
		if(j == Key.length-1) {
			j = -1;
		}
	}
	return Result;
}

/*
 * Read the comments in the PHP Ciphering Library to understand what is going on here
 * The only comment I can make is that I gave the file a name of vgnr instead of
 * vigenere, once again for paranoiac reasons. I know, it is extremely weak.
 */
$(document).ready(function() {
	$("a.obfuscated_link").deobfuscateEmailLink();
	$("span.obfuscated_link_text").deobfuscateEmailLink();
});
$.fn.deobfuscateEmailLink = function() {
	$(this).each(function() {
		if ($(this).hasClass("obfuscated_link")) {
			var href = $(this).attr("href");
			if (href.indexOf("#KEEP#LOOKING#") > -1) {
				var mail = href.substring(14).split("]#[");
                var key = mail[0];
                var ciphered_email = mail[1];

                Final_email = decrypt(key, ciphered_email);

				$(this).attr("href", "mailto:" + Final_email);
			}
		} else {
            var mail = $(this).text().split("]#[");
                var key = mail[0];
                var ciphered_email = mail[1];

              Final_email = decrypt(key, ciphered_email);

            $(this).text(Final_email);
		}
	}); //end each
};

})(jQuery);
/*    //example
    alert(PolyalphabeticCipher("use the force luke","warthog") + "\n" + PolyalphabeticDecipher(PolyalphabeticCipher("use the force luke","warthog"),"warthog"))
*/