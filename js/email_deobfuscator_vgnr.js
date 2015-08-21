/**
 * Vigenere Cipher for Automatic Email Obfuscator
 * Deciphering Script
 *
 * By Nour Akalay, December 2011
 * 
 * @author Nour Akalay
 * @copyright Copyright (c) 2011, Nour Akalay
 * @license Free to use and abuse under the MIT license.
 *          http://www.opensource.org/licenses/mit-license.php
 */
(function($){

var Vgnr = {

    decrypt: function(key, emailToDecrypt) {
        var alphabet = "!$%'&*#+-./0123456789=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        //var alphabet = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        var alphabetLen = alphabet.length;
        var emailLen = emailToDecrypt.length;
        var letter = "";
        var Result = "";
        var alphabetMatrix = new Array(alphabet.length);

        for(i=0; i < alphabet.length; i++) {
            alphabetMatrix[i] = alphabet.substr(i) + alphabet.substr(0, i);
        }
        var j = -1;
        var result = '';
        for(i=0; i < emailToDecrypt.length; i++) {
            j++;
            letter = emailToDecrypt.charAt(i);
            if (alphabet.indexOf(letter) == -1) {
                j--;
                result += letter;
            } else {
                result += alphabet.charAt(alphabetMatrix[alphabet.indexOf(key.charAt(j))].indexOf(letter));
            }
            if(j == key.length-1) {
                j = -1;
            }
        }
        return result;
    }

};

/*
 * Read the comments in the PHP Ciphering Library to understand what is going on here
 * The only comment I can make is that I gave the file a name of vgnr instead of
 * vigenere, once again for paranoiac reasons. I know, it is extremely weak.
 */
$.fn.deobfuscateEmailLink = function() {
    $(this).each(function() {
        if ($(this).hasClass("obfuscated-link")) {
            var href = $(this).attr("href");
            if (href.indexOf("#KEEP-LOOKING-") > -1) {
                var mail = href.substring(14).split("]#[");
                var key = mail[0];
                var cipheredEmail = mail[1];

                var finalEmail = Vgnr.decrypt(key, cipheredEmail);

                $(this).attr("href", "mailto:" + finalEmail);
            }
        } else {
            var mail = $(this).text().split("]#[");
            var key = mail[0];
            var cipheredEmail = mail[1];

            var finalEmail = Vgnr.decrypt(key, cipheredEmail);

            $(this).text(finalEmail);
        }
    });
};

$(document).ready(function() {
    $("a.obfuscated-link").deobfuscateEmailLink();
    $("span.obfuscated-link-text").deobfuscateEmailLink();
});

})(jQuery);