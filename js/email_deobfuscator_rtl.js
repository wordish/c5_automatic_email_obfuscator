(function($){

$.fn.deobfuscateEmailLink = function() {
    $(this).each(function() {
        if ($(this).hasClass("obfuscated-link")) {
            var href = $(this).attr("href");
            if (href.indexOf("#MAIL:") > -1) {
                var mail = href.substring(6);
                $(this).attr("href", "mailto:" + mail.split("").reverse().join(""));
            }
        } else {
            // The inner span that contains the address
            // This is just for easier copy pasting 
            $(this).html($(this).find("span:eq(0)").html().split("").reverse().join(""));
        }
    });
};

$(document).ready(function() {
    $("a.obfuscated-link").deobfuscateEmailLink();
    $("span.obfuscated-link-text").deobfuscateEmailLink();
});

})(jQuery);