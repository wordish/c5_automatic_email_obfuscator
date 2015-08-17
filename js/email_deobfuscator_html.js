(function($){

$(document).ready(function() {
	$("a.obfuscated_link").deobfuscateEmailLink();
	$("span.obfuscated_link_text").deobfuscateEmailLink();
});
$.fn.deobfuscateEmailLink = function() {
	$(this).each(function() {
		if ($(this).hasClass("obfuscated_link")) {
			var href = $(this).attr("href");
			var raw = $('<div/>').html(href).text();
			$(this).attr("href", raw.replace("#MAIL:", "mailto:").replace(/\(at\)/g, "@"));
		} else {
			$(this).html($(this).html().replace("(at)", "@"));
		}
	});
};

})(jQuery);