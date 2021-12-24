jQuery( function( $ ) {
	
	$('.qm-checkall').change(function(e){
			
		var input_name	=	$(this).val();
		
		if($(this).is(':checked')){
			$('.' + input_name).prop('checked', true);
		}else{
			
			$('.' + input_name).prop('checked', false);
		}
	});
	
	$('.qm-table-pagination .qm-pagi').click(function(e){
		e.preventDefault();
		
		var pagi		=	$(this).parent('.qm-table-pagination'),
			page		=	$(this).data('page'),
			tableData	=	$('.' + pagi.data('container'));
			
			tableData.find('.qm-table-page').removeClass('show');
			
			pagi.find('.qm-pagi').removeClass('active');
			
			$(this).addClass('active');
			
			tableData.find('.' + page).addClass('show');
	});
	
	var panel_tabs	=	$('.qm-panel-tabs');
	
	$.each(panel_tabs, function(index, pnt){
		
		var tabs		=	$(pnt).find('.qm-tabs'),
			tabs_item	=	tabs.children('li');
		
		var tabs_panel	=	$(pnt).find('.qm-panel-tabs');
		
		tabs_item.click(function(e){
			
			var tab_data	=	$(this).data('tab');
			
			tabs_item.removeClass('active');
			
			tabs_panel.css('display', 'none');
			
			$(this).addClass('active');
			
			tabs_panel.filter('.' + tab_data + '_data').css('display', 'block');
						
			e.preventDefault();
			
		});
		
		tabs_item.filter('.default').trigger('click');
		
	});

	QM_Verify   =	function(){

		return {

			init: function(){

				$.post(quizmaker.ajax_url + '?t=' + (new Date().valueOf()), {
					action: 'quizmaker_check_verify',
					security: quizmaker.security
				}, function( response ){
					
					if(response.status == 0){
						window.location.href = quizmaker.verify_link;
					}
				});
			}
		}
	}
	
	
	QM_Dialog	=	function(element, options){ 
		
		options		=	_.extend({width: 400, height: 300, title: 'Dialog'}, options);
		
		var defer 		= $.Deferred();
		
		var body		=	$('body'),
			template	=	$('<div class="qm-dialog"><div class="overlay"></div>'+
								'<div class="qm-dialog_content">'+
								'<div class="qm-dialog_content_title">' + options.title + '</div>'+
								'<button class="qm-dialog_close"></button>' +
								'<div class="qm-dialog_content_body"></div>' +
								'<div class="qm-dialog_content_action">'+
									'<div class="qm-pull-right">'+
										'<button class="button btn-cancel">Cancel</button>'+
										'<button class="button button-primary btn-ok">OK</button>'+
									'</div>'+
								'</div>' +
							'</div></div>'),
			dlg_close			=	template.find('.qm-dialog_close'),
			dlg_content			=	template.find('.qm-dialog_content'),
			dlg_content_body	=	template.find('.qm-dialog_content_body'),
			dlg_overlay			=	template.find('.overlay'),
			dlg_btn_ok			=	template.find('.btn-ok'),
			dlg_btn_cancel		=	template.find('.btn-cancel');
		
		var dialog	=	 {
			init: function(){
				this.events();
			},
			show: function(){
				
				this.setPosition();
				
				dlg_content.find('.qm-dialog_content_body').html(element);
				body.append(template);
				
				return defer.promise();
			},
			close: function(){
				
				template.remove();
			},
			events: function(){
				var _self	=	this;
				
				$(window).resize(function(){
					_self.setPosition();
				});
				
				dlg_overlay.click(function(){
					_self.close();
				});
				
				dlg_close.click(function(){
					_self.close();
				});
				
				dlg_btn_cancel.click(function(){
					_self.close();
				});
				
				dlg_btn_ok.click(function(e){
					
					defer.resolve();
					_self.close();
					e.preventDefault();
				});
				
			},
			setPosition: function(){
				
				var winW	=	$(window).width(),
					winH	=	$(window).height(),
					dgW		=	options.width,
					dgH		=	options.height,
					dgTop	=	10,
					dgLeft	=	0;
				
					if(winW < dgW){
						dgW	=	winW - 10;
					}
					
					if(winH < dgH){
						dgH	=	winH - 70;
					}
					
					dgLeft	=	(winW - dgW)/2;
					dgTop	=	(winH - dgH)/2;
				
					dlg_content.css({width: dgW, height: dgH, top: dgTop, left: dgLeft});
					
					dlg_content_body.css({height: (dgH - 41 - 48)});
			}
		};
		
		dialog.init();
		
		return dialog;
	}
	
	QM_Model	=	function( params ){
		
		params	=	_.extend({ 
			is_validated: true
		}, params);
		
		var	security	=	quizmaker.security;
		
		return {
			get: function(data){
				
				var dfd = $.Deferred();
				
				data	=	_.extend({security: security}, data);
				
				if( params.is_validated ){
					$.getJSON(quizmaker.ajax_url, data, function(response){
						
						if(typeof response != 'undefined'  || response != null || typeof response.error != 0){

							dfd.resolve(response);
						} else {

							dfd.reject(response.error);
						}
						
					});
				}else{
					
					dfd.reject('Error');
				}
				
				return dfd.promise();
			},
			post: function( data ){

				var dfd = $.Deferred();
				
				data	=	_.extend({security: security}, data);
				
				if( params.is_validated ){
					$.post(quizmaker.ajax_url, data, function(response){
						
						if(typeof response != 'undefined'  || response != null || typeof response.error != 0){

							dfd.resolve(response);
						} else {

							dfd.reject(response.error);
						}
						
					});
				}else{
					
					dfd.reject('Error');
				}
				
				return dfd.promise();
			},
			save: function( data ){
				var dfd = $.Deferred();
				
				data	=	_.extend({security: security}, data);
				
				if( params.is_validated ){
					$.post(quizmaker.ajax_url, data, function(response){
						
						if(typeof response != 'undefined'  || response != null || typeof response.error == 0){
							
							dfd.resolve(response);
						} else {
							
							dfd.reject(response.error);
						}
						
					});
				}else{
					
					dfd.reject('Error');
				}
				
				return dfd.promise();
			},
			validate: function(){
				
			}
		};
	};
	
	QM_Switch_Number	=	function(){
		
		var elements	=	$('.qm-input-switch-number');
		
		var setValue	=	function(el, state) {
			
			
			var elSlide		=	el.find('.qm-input-switch-number-input-slide'),
				elInput		=	el.find('.qm-input-switch-number-input-on'),
				elInfoOn	=	el.find('.qm-input-switch-number-info-on'),
				elInfoOff	=	el.find('.qm-input-switch-number-info-off'),
				elInputVal	=	elInput.find('input'),
				offVal		=	elInputVal.data('off');
				
				if(typeof offVal == 'undefined'){
					offVal	=	0;
				}
				
				if( typeof state == 'undefined' || state == offVal ) {
					state = 'off';
				}
								
				el.data('state', state);
				el.addClass(state);
				
				if(state == 'off') {
				
					elSlide.removeClass('on').addClass('off');
					
					elInput.find('input').val(offVal);
					elInfoOn.css('display', 'none');
					elInfoOff.css('display', 'block');
						
		
				}else {
											
					elSlide.removeClass('off')
					elSlide.addClass('on');
					
					elInfoOn.css('display', 'block');
					elInfoOff.css('display', 'none');
					
					var value	=	elInputVal.val();
					
					if( value <= offVal ) {
						value	=	offVal + 1;
					}
					
					elInputVal.val(value);
					
				}
			
		};
		
		elements.each(function(index, value){
			
			var el			=	$(this),
				elSlide		=	$(this).find('.qm-input-switch-number-input-slide'),
				elInput		=	$(this).find('.qm-input-switch-number-input-on'),
				elInputVal	=	elInput.find('input'),
				offVal		=	elInputVal.data('off');
			
			var state		=	'off',
				value		=	elInputVal.val();
				
				if(value > offVal ){
					state	=	'on';
				}
				
				if(typeof offVal == 'undefined'){
					offVal	=	0;
				}
				
				setValue( $(this), state );
				
				elSlide.click(function(e){
					
					if(elSlide.hasClass('off')){
						
						setValue( el, 'on' );
						elInputVal.focus();
						
					}else{
						
						setValue( el, 'off' );
						
					}
			
					e.preventDefault();
				});
				
				elInputVal.blur(function(){
					
					var value	=	$(this).val();
					
					if(value == '' || !$.isNumeric(value) || value <= offVal){
						
						$(this).val(offVal + 1);
					}
					
				});
			
		});
	}
	
	QM_Switch_Select	=	function(){
		
		var elements	=	$('.qm-input-switch-select');
		
		var setValue	=	function(el, value) {
			
			var elSlide		=	el.find('.qm-input-switch-select-input-slide'),
				elInput		=	el.find('.qm-input-switch-select-input-on'),
				elInfoOn	=	el.find('.qm-input-switch-select-info-on'),
				elInfoOff	=	el.find('.qm-input-switch-select-info-off'),
				elInputVal	=	elInput.find('input'),
				offVal		=	elInputVal.data('off'),
				onVal		=	elInputVal.data('on'), 
				state;
				
				if(typeof offVal == 'undefined'){
					offVal	=	0;
				}
				
				if(typeof onVal == 'undefined'){
					onVal	=	1;
				}
				
				if( value == onVal ){
					state	=	'on';
				}else{
					state	=	'off';
				}
				
								
				el.data('state', state);
				el.addClass(state);
				
				if(state == 'off') {
				
					elSlide.removeClass('on').addClass('off');
					
					elInputVal.val(offVal);
					elInfoOn.css('display', 'none');
					elInfoOff.css('display', 'block');
						
		
				}else {
											
					elSlide.removeClass('off')
					elSlide.addClass('on');
					
					elInfoOn.css('display', 'block');
					elInfoOff.css('display', 'none');
					
					elInputVal.val(onVal);
					
				}
			
		};
		
		elements.each(function(index, value){
			
			var el			=	$(this),
				elSlide		=	$(this).find('.qm-input-switch-select-input-slide'),
				elInput		=	$(this).find('.qm-input-switch-select-input-on'),
				elInputVal	=	elInput.find('input'),
				offVal		=	elInputVal.data('off'),
				onVal		=	elInputVal.data('on');
				
				setValue( $(this), elInputVal.val() );
				
				elSlide.click(function(e){
					
					value = elInput.find('input').val();
					
					if(value == onVal ){
						state	=	'on';
					}else{
						state	=	'off';
					}
					
					if(state == 'off'){
						
						setValue( el, onVal );
						elInputVal.focus();
						
					}else{
						
						setValue( el, offVal );
						
					}
			
					e.preventDefault();
				});
			
		});
	}

	QM_Select2			=	function( options ){

		$('.qm-select2').each(function(){

			var _self = $(this);

			var exclude = jQuery( this ).data('exclude');

			if( !exclude ) { exclude = []; }

			
			
			_self.select2({
				theme: 'aws',
				placeholder: jQuery( this ).data('placeholder'),
				escapeMarkup: function (markup) { return markup; },
				allowClear: true,
			    minimumResultsForSearch: 20,
			    minimumInputLength: 3,
				ajax: {
				    url: quizmaker.ajax_url,
				    dataType: 'json',
				    delay: 250,
				    data: function( params ) {

				    	var current_exclude = jQuery( this ).val();

						if( !current_exclude ) { current_exclude = []; }

						exclude = exclude.concat(current_exclude);

						return {
							term:     params.term,
							page: 	  params.page,
							action:   jQuery( this ).data( 'action' ),
							security: jQuery( this ).data( 'security' ),
							exclude:  exclude,
							limit:    jQuery( this ).data( 'limit' ),
							others:   jQuery( this ).data( 'others' )
						};
					},
				placeholder: jQuery( this ).data('placeholder'),
			    processResults2: function (data, params) {
			      // parse the results into the format expected by Select2
			      // since we are using custom formatting functions we do not need to
			      // alter the remote JSON data, except to indicate that infinite
			      // scrolling can be used
			      params.page = params.page || 1;
			      console.log(data);
			      return {
			        results: data.items,
			        pagination: {
			          more: (params.page * 30) < data.total_count
			        }
			      };
			    },
			    processResults: function( data ) {
					var terms = [];
					if ( data ) {
						jQuery.each( data, function( id, text ) {
							terms.push( { id: id, text: text } );
						});
					}
					return {
						results: terms
					};
				},
				
			    cache: true
			  }
			}).on('quizmaker:clear_select2', function(){

				_self.val('');
				_self.trigger('change');
			});
		});
	}
	
	QM	=	function(){
		
		var frameWPMedia;
	
		return {
			QuestionLightbox: function(params){
				
				params	=	_.extend({title: 'Add question', visible_items: 5}, params);
				
				var template		=	$('<div id="lightbox-questions">'+
										'<div class="search-box">'+
											'<input type="text" placeholder="Search for a question..."/>'+
											'<div class="drop-panel"></div>'+
										'</div>'+
										'<div class="result-search"></div>'+
										'</div>'),
					search_box		=	template.find('.search-box'),
					search_input	=	search_box.find('input'),
					search_result	=	template.find('.result-search');
				
				var time_search;
				
				var questionModel	=	new QM_Model();
				
				var results			=	[];
				
				var setHeight		=	function( panel, values ){
					
					if(values.length > params.visible_items) {
						panel.css({'height': ( 28 * params.visible_items ) + 'px'});
					}else{
						panel.css({'height': 'auto'});
					}
					
				};
				
				var panel_results		=	function( value ){
					
					var item	=	$('<div class="item"><button class="ion-ios-close-outline qm-remove" data-id="'+value.ID+'">close</button><span class="item-content"></span></div>');
					
					item.find('.item-content').html(value.post_title);
					item.find('.qm-remove').click(function(){
												
						var id	=	$(this).data('id');
						
						results	=	_.reject(results, function(q){
							return q.ID == id;
						});
												
						item.remove();
						
					});
					
					search_result.append(item);
					
					results.push(value);
				};
				
				var panel_drop_results	=	function( values ){
					
					var panel	=	$('.drop-panel'),
						item	=	$('<div class="item"/>');
						
					var total	=	values.length;
					
					panel.empty();
					panel.css('display', 'block');
					
					if(values.length > 0){
						$.each(values, function(index, value){
						
							var new_item	=	item.clone();
						
							new_item.data('value', value);
							new_item.html(value.post_title);
							
							new_item.click(function(){
								
								panel_results($(this).data('value'));
								
								$(this).addClass('selected').unbind('click');
								
								if(panel.children('.item').length == 0){
									panel.empty();
									panel.css('display', 'none');
								}
								
							});
						
							panel.append(new_item);
						});
					}else{
						
						panel.empty();
					}
					
					setHeight(panel, values);
				}
				
				search_input.keyup(function(){
					clearTimeout(time_search);
					
					time_search = setTimeout(function(){
						
						if(search_input.val() == '' || search_input.val().length < 4) {
							clearTimeout(time_search);
							return false;
						}
						
						var s_val	=	search_input.val();
						
						questionModel.get({
							action: 'get_questions', 
							s: s_val,
							pid: meta_boxes_test_script.post_id,
							nids: _.pluck(results, 'ID')
						}).then(function(responses){
							
							if(responses.data.length > 0 || responses.data.length == 0){
								panel_drop_results(responses.data);
							}
							
						});
						
					}, 300);
				});
				
				$('body').click(function(event){
					
					if(!$(event.target).is('.drop-panel') && !$(event.target).is('.item'))
					{
						$('.drop-panel').css('display', 'none');
						
					}
				});
				
				return {
					show: function(callback){
						
						QM_Dialog(template, params).show().then(function(){
							
							if(typeof callback == 'function'){
								callback(results);
							}
						});
						
					},
					close: function(){
					
						body.find('#lightbox-questions').remove();
					}
				};
			},
			UserLightbox: function(params){
				params	=	_.extend({ title: 'Assign Users', visible_items: 5 }, params);
				
				var template		=	$('<div id="lightbox-users">'+
										'<div class="search-box">'+
											'<input type="text" placeholder="Search for users..."/>'+
											'<div class="drop-panel"></div>'+
										'</div>'+
										'<div class="result-search"></div>'+
										'</div>'),
					search_box		=	template.find('.search-box'),
					search_input	=	search_box.find('input'),
					search_result	=	template.find('.result-search');
				
				var time_search;
				
				var userModel	=	new QM_Model();
				
				var results			=	[];
				
				var setHeight		=	function( panel, values ){
					
					if(values.length > params.visible_items) {
						panel.css({'height': ( 28 * params.visible_items ) + 'px'});
					}else{
						panel.css({'height': 'auto'});
					}
					
				};
				
				var panel_results		=	function( value ){
					
					var item	=	$('<div class="item"><button class="ion-ios-close-outline qm-remove" data-id="'+value.id+'">close</button><span class="item-content"></span></div>');
					
					item.find('.item-content').html(value.name);
					item.find('.qm-remove').click(function(){
												
						var id	=	$(this).data('id');
						
						results	=	_.reject(results, function(q){
							return q.id == id;
						});
												
						item.remove();
						
					});
					
					search_result.append(item);
					
					results.push(value);
				};
				
				var panel_drop_results	=	function( values ){
					
					var panel	=	$('.drop-panel'),
						item	=	$('<div class="item"/>');
						
					var total	=	values.length;
					
					panel.empty();
					panel.css('display', 'block');
					
					if(values.length > 0){

						$.each(values, function(index, value){
						
							var new_item	=	item.clone();
						
							new_item.data('value', value);
							new_item.html(value.name);
							
							new_item.click(function(){
								
								panel_results($(this).data('value'));
								
								$(this).addClass('selected').unbind('click');
								
								if(panel.children('.item').length == 0){
									panel.empty();
									panel.css('display', 'none');
								}
								
							});
						
							panel.append(new_item);
						});
					}else{
						
						panel.empty();
					}
					
					setHeight(panel, values);
					
				}
				
				search_input.keyup(function(){
					
					clearTimeout(time_search);
					
					time_search = setTimeout(function(){
						
						if(search_input.val() == '' || search_input.val().length < 4) {
							clearTimeout(time_search);
							return false;
						}
						
						var s_val	=	search_input.val();
						
						userModel.get({
							action: 'get_users', 
							s: s_val,
							pid: meta_boxes_test_script.post_id,
							nids: _.pluck(results, 'id')
						}).then(function(responses){
							
							if(responses.data.length > 0 || responses.data.length == 0){
								panel_drop_results(responses.data);
							}
							
						});
						
					}, 300);
				});
				
				$('body').click(function(event){
					
					if(!$(event.target).is('.drop-panel') && !$(event.target).is('.item'))
					{
						$('.drop-panel').css('display', 'none');
						
					}
				});
				
				return {
					show: function(callback){
						
						QM_Dialog(template, params).show().then(function(){
							
							if(typeof callback == 'function'){
								callback(results);
							}
						});
						
					},
					close: function(){
					
						body.find('#lightbox-users').remove();
					}
				};
			},
			RankingLightbox: function( params ) {
				params	=	_.extend({ title: 'New Ranking' }, params);
				
				var template		=	$('<div id="lightbox-ranking">'+
											'<div class="group-input"></div>'+
										'</div>');
				
				return {
					show: function(callback){
						
						QM_Dialog(template, params).show().then(function(){
							
							if(typeof callback == 'function'){
								callback(results);
							}
						});
						
					},
					close: function(){
						
						body.find('#lightbox-ranking').remove();
					}
				};
			},
			WPMedia: function( params ){
				
				if ( frameWPMedia ) {
					frameWPMedia.close();
					return;
				}
				
				frameWPMedia = wp.media({
					title: 'Select or Upload Media Of Your Chosen Persuasion',
					button: {
						text: 'Use this media'
					},
					multiple: false
				});
				
				frameWPMedia.on( 'select', function() {
	
					var attachment = frameWPMedia.state().get('selection').first().toJSON();
					
					if(typeof params.selection == 'function') {
						
						params.selection( attachment );
					}
					
				});

				frameWPMedia.open();
			},
			Message: function(){
				
				var timeoutMessage;
				
				return {
					inform: function( message, params ){
						
						var _self	=	this,
							element	=	$('<div class="qm-js-message"></div>');
						
							params	=	_.extend({ type: 'qm-success', is_auto_hide: true, duration: 4000 }, params);
							
							element.addClass(params.type).addClass('show').html(message);
							
							this.element	=	element;
							
							element.click(function(e){
								e.preventDefault();
								
								_self.hide();
							});
							
							if(params.is_auto_hide){
								timeoutMessage	=	setTimeout(function(){
							
									_self.hide();
									
								}, params.duration);
							}
							
							$('body').append( element );
						
					},
					hide: function(){
						
						var _self	=	this;
						
							clearTimeout(timeoutMessage);
						
							this.element.removeClass('show');
						
							setTimeout(function(){
							
								_self.element.remove();
								
							}, 500);
						
					}
				}
			},
			Loading: function(){

				var element;

				return {
					show: function(){

						element	=	$('<div class="qm-js-loading"><div class="qm-wrap-loader"><span class="qm-loader-1"></span></div><div class="overlay"></div></div>');

						$('body').append( element );

					},
					hide: function(){

						var _self	=	this;

						element.remove();

					}
				}
			}
		}
	}
	
	$(document).ready(function(){
		
		QM_Switch_Number();
		QM_Switch_Select();

		setTimeout(function(){

			QM_Select2();

		}, 1000);
		
	});
	
});





