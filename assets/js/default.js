$(document).ready(function () {
	$('.tooltip').twipsy();
	$('.pills').tabs();
	$('a[rel="close"]').click(function(){
		$(this).closest('.alert-message').fadeOut();
	})

	$('.tweetheader').click(function(){
		id = $(this).attr('id');
		$('div[rel="'+id+'"]').slideToggle('fast',function(){});
	});
});