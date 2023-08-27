

$(function(){
	// rate popup
	$('.form-group.rate-us i').on('click' , function(){
		var thisTag = $(this);
		var key = thisTag.parent().parent().data('key');
		
		thisTag.parent().parent().find('input[name='+key+']').prop('value' , thisTag.data('num'));
		var items = thisTag.parent().parent().find('i.fas');
		$.each(items , function(k , v){
			var item = $(v);
			if(item.data('num') <= thisTag.data('num')){
				item.addClass('active');
			}else item.removeClass('active');
		});
	});

	// ads popup
	setTimeout(() => {
		
		$('#menu-ad').trigger('click');
	}, 1000);

	var menuAd = $('#menu-ad');
	menuAd.find('.close-menu').on('click' , function(){
		console.log('close');
		menuAd.find('iframe').remove();
	});
	
	$('body').on('click' , '.btn-custom-modal' , function(){
		console.log('done');
		$('.modal-backdrop').hide();
	});
});