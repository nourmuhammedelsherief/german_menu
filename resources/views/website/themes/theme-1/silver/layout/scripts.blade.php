
<script type="text/javascript" src="{{asset('scripts/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('scripts/jquery.lazy.min.js')}}"></script>
<script type="text/javascript" src="{{asset('scripts/toastr.min.js')}}"></script>
<script src="{{asset('plugins/vanilla-calendar/vanilla-calendar.min.js')}}"></script>
<script type="text/javascript" src="{{asset('scripts/custom.js')}}"></script>
<script type="text/javascript" src="{{asset('scripts/global.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>
    <script type="text/javascript">
        // $('ul.pagination').hide();
		var checkMobile = function(){
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) return true ;
			return false;
		}
		function checkMenuProductSubmit(productId){
		
			var menu = $('#menu-prodact-' + productId);
			
			var check = true;
			if(menu.length > 0){
				// check sizes
				var sizes = menu.find('input.size_price').length;
				if(sizes > 0 && menu.find('input.size_price:checked').length == 0){
					toastr.error('{{trans('messages.error_size_price_required')}}');
					check = false;
				}
				
				// check additions 
				var additions = menu.find('.addition-item');
				if(check)
					$.each(additions , function(k , item){
						var t = $(item);
						var option = t.find('.activity_price');
						if(option.data('min') >= 1){
							if(option.data('choose_one') == true){
								var items = menu.find('[data-main_id="'+option.data('main_id')+'"]:checked');
								if(items.length == 0 && check){
									toastr.error('{{trans('messages.error_option_required')}}');
									check =false;
								}
								console.log(items.length);
							}else if(option.prop('checked') == false && check){
								toastr.error('{{trans('messages.error_option_required')}}');
								check = false;
							}
						}
					});
				console.info('success');
				return check;
			}
			return false;
		}
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": true,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		var isCategoryUp = false;
		var maxWidth = 0;
        $(function() {
			
          
			// when click on categories
			$('.my-categories .itemCat > a').on('click' , function(){
				var thisTag = $(this);
				console.log(thisTag.data('link'));
				$.ajax({
					url : thisTag.data('link') ,
					method: 'GET' , 
					headers : {
						Accept : 'application/json' , 
					}  ,
					data: {
						is_category : true 
 					},
					beforeSend: function(){
					    var height = window.outerHeight - $('#xcategories').height() - 30;
						$('#restaurant-products').html('<div class="preload-custom" style="display:flex;height:'+height+'px"><div class="lds-ripple" style="margin:auto;top:0 !important;"><div></div><div></div></div></div>');
						
						$('.my-categories .itemCat').removeClass('active');
						$('.my-categories .itemCat  div.item').removeClass('active');
						thisTag.parent().addClass('active');
						thisTag.parent().find('div.item').addClass('active');
					},
					success :function (json){
						console.log(json);
						if(json.status == true){	
							$('.menu-category-name h5').html(json.data.category_name);
							$('#menu-sub-categories').html(json.data.sub_categories);
							$('#restaurant-products').html(json.data.products);
							$('#menu-ad').remove();
							$('body').append(json.data.ads_content).ready(function(){
								setTimeout(() => {
									console.log($('#menu-ad'));
								
									$('#menu-ad').trigger('click');
									
								}, 1000);
							
							});
								$("#restaurant-products").animate({ scrollTop:0 }, 1);
								
								window.scrollTo(0 , 450);
								console.log('scoll done');
							
						}
						$('[data-src]').lazy();
					},
					error : function(xhr){
						$('#restaurant-products').html('<p class="text-center">{{trans('messages.no_products')}}</p>');
					}
				});
			}); // end click on category

			// when click on subcategory 
			$('#menu-sub-categories').on('click' , '.item  a' , function(){
				var thisTag = $(this);
				console.log('sub-category');
				console.log(thisTag.data('link'));
				$.ajax({
					url : thisTag.data('link') ,
					method: 'GET' , 
					headers : {
						Accept : 'application/json' , 
					}  ,
					data: {
						is_category : true 
 					},
					beforeSend: function(){
						$('#restaurant-products').html('<div class="preload-custom" style="display:flex;height:380px"><div class="lds-ripple" style="margin:auto;top:0 !important;"><div></div><div></div></div></div>'); // loader
						
					},
					success :function (json){
						console.log(json);
						if(json.status == true){	
							
							$('#menu-sub-categories').html(json.data.sub_categories);
							$('#restaurant-products').html(json.data.products);
							
						}
						$('[data-src]').lazy();
					},
					error : function(xhr){
						$('#restaurant-products').html('<p class="text-center">{{trans('messages.no_products')}}</p>');
					}
				});
			});

			
			  // add add to cart
			@if(session('addToCart'))
			toastr.success("{{trans('messages.saved_cart_success')}}" , "{{trans('messages.cart')}}");
			@endif
			  $('body').on('click' , '.btn-save-cart', function(){
                var tag = $(this);
                var form = $('#silverCartForm-' +tag.data('product_id'));
                console.log(form);
				var formData = new FormData(form[0]);
				const formDataObj = {};
				formData.forEach((value, key) => (formDataObj[key] = value));
				console.log(formDataObj);
				// check data
				console.log(tag.data());
				console.log('end ------------------------');
				var checkMenu = checkMenuProductSubmit(tag.data('product_id'));
				if(!checkMenu) return 0;
				
				// end check
                
                $.ajax({
                    url : form.attr('action') , 
                    method : 'POST' , 
                    headers: {Accept : 'application/json'} , 
                    data: formData , 
                    processData : false , 
                    contentType : false, 
                    success: function(json){
                        
                        console.log(json);
                        if(json.status == true){
                            $('#footer-bar a span').text(json.data.cart_count);
                            
                            $('#menu-prodact-' + tag.data('product_id') + ' .close-menu').trigger('click');
                            toastr.success(json.message , "{{trans('messages.cart')}}");
							$('#cart-count span.count').text(json.data.cart_count);
							$('#cart-count').fadeIn(100);
                        }else if(json.status == 2){
							window.location.replace(json.login_link);
						}
						else{
                            toastr.error("{{trans('messages.fail_to_add_to_cart')}}" , "{{trans('messages.cart')}}");
                        }
                        
                    },
                    error: function(xhr){
                        console.log(xhr);
                        
                        toastr.error("{{trans('messages.fail_to_add_to_cart')}}" , "{{trans('messages.cart')}}");
                    }
                });
            });


			var scrollValue =  $(window).scrollTop();
			
    		$(window).on('scroll', function() {

				var lastProduct = $('.last-product');
				
				// if ({{isset($restaurant->id) ? $restaurant->id : 0}} == 276 && !checkIsMore && $(window).scrollTop() > scrollValue && $(window).scrollTop() >= lastProduct.offset().top + lastProduct.
				// 	outerHeight() - window.innerHeight) {
				// 	console.log(lastProduct.data());
					
				// 	var a = $('.menu-category-' + lastProduct.data('category_id')).next().find('> a');
				// 	// console.log('.menu-category-' + lastProduct.data('category_id'));
				// 	console.log('temp');
				// 	checkIsMore= true;
				// 	a.trigger('click');
					
				// 	// 	window.scrollTo(420, 420);
				    
				// 	// alert('You reached the end of the DIV');
				// 	lastProduct.removeClass('last-product');
				// }
				// console.log( $(window).scrollTop() > scrollValue && $(window).scrollTop() >= lastProduct.offset().top + lastProduct.
				// 	outerHeight() - window.innerHeight);
				scrollValue = $(window).scrollTop();


				
				var $el = $('#xcategories'); 
				var width =  $('body').width();
				
				@if($restaurant->enable_fixed_category == 'true')
				if (scrollValue > 420  && !isCategoryUp){ 
					console.log('up');
					$el.addClass('fixed-categories');
					$('.card.xproducts').addClass('top-190');
					isCategoryUp = true;
				}
				if (scrollValue < 420 && isCategoryUp){
					console.log('down');
					$el.removeClass('fixed-categories');
					$('.card.xproducts').removeClass('top-190');
					isCategoryUp = false;
				} 
				@endif
				if($el.width() != width || $(window).width() < 1000){
					$el.css('width' , width);
					// console.log('xcategories done');
				}else{
					// console.log("width : " + width);
				}

				
				
			});
			$('body').on('click' , '.total_sum' , function () {
				console.log('sum');
				var mealId = $(this).attr('data');
				var all = parseInt(document.getElementById(mealId).textContent);
				var inputName = 'total' + mealId;
				
				let el = document.querySelector(`[name="${inputName}"]`);
			
					el.value = parseInt(el.value) + 1;
				
				
				calcPrice(mealId);
			});
			$('body').on('click' , '.total_min' , function () {
				var mealId = $(this).attr('data');
				var all = parseInt(document.getElementById(mealId).textContent);
				var inputName = 'total' + mealId;
				
				let el = document.querySelector(`[name="${inputName}"]`);
				if(el && el.value > 1)
					el.value = parseInt(el.value) - 1;
				
				console.log('value is : ' + all);
				calcPrice(mealId);
			});
			$('body').on('click'  , '.option_increase' , function () {
				var tag = $(this);
				var optionData= tag.data();
				
				var optionId = $(this).attr('data');
				var mealId = $(this).attr('data-id');
				var inputName = 'qty' + optionId + mealId;
				var tagInput = $('input[name='+inputName+']');
				var quantity = parseInt(tagInput.val());
				
				if(optionData.max < (quantity + 1)){
					
					toastr.info("{{trans('messages.error_option_top_max')}}" + optionData.max);
					// tagInput.val(optionData.max);
					calcPrice(mealId);
					return 0;
				}
				var old = parseInt(document.getElementById(optionId + mealId).textContent);
				var optionPrice = parseInt((this.value));
				let el = document.querySelector(`[name="${inputName}"]`);
				// check if the input checked
				var checkId = 'box' + optionId + mealId + '-fac-radio';
				var checkBox = document.getElementById(checkId);
				if (true) {
						document.getElementById(optionId + mealId).textContent = old + optionPrice;
						el.value = parseInt(el.value) + 1;
				}
				calcPrice(mealId);
				
			});

			
			$('body').on('click' , '.option_decrease' , function () {
				var tag = $(this);
				var optionData = tag.data();
				var optionId = $(this).attr('data');
				var mealId = $(this).attr('data-id');
				var inputName = 'qty' + optionId + mealId;
				var tagInput = $('input[name='+inputName+']');
				var quantity = parseInt(tagInput.val());
				if(optionData.min > (quantity - 1)){
					toastr.info("{{trans('messages.error_option_top_min')}}" + optionData.min);
					// tagInput.val(optionData.max);
					calcPrice(mealId);
					return 0;
				}
				var old = parseInt(document.getElementById(optionId + mealId).textContent);
				var optionPrice = parseInt((this.value));
				var inputName = 'qty' + optionId + mealId;
				let el = document.querySelector(`[name="${inputName}"]`);
				// check if the input checked
				var checkId = 'box' + optionId + mealId + '-fac-radio';
				var checkBox = document.getElementById(checkId);
				if (true) {
					if ((old - optionPrice) >= optionPrice) {
						document.getElementById(optionId + mealId).textContent = old - optionPrice;
						if (parseInt(el.value) > 1) {
							el.value = parseInt(el.value) - 1;
						}
					}
				}
				calcPrice(mealId);
            
        	});

			// start tabet
			
			if($(window).width() > 1000 ){
					$('body').addClass('mobile-width')
					console.log('max');
				}
			// end

			$('.prodcontent').on('click' , '[data-xmenu]' , function(){
				window.location.replace($(this).data('url'));
			});
			// $('#page').on('click' , '[data-x-menu]' , function(){
			// 	console.log('click here here .');
			// 	console.log($(this));
			// 	// window.location.replace($(this).data('url'));
			// });

        }); // end loading function
		
    </script>
@stack('scripts')
</body>
