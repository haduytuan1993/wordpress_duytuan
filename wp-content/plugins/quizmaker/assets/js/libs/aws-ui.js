
AWS_UI_Message	=	function(){

	var timeoutMessage;
				
	return {
		inform: function( params ){
			
			if( typeof params.content == 'undefined' || params.content == '' ){ return false; }

			var _self	=	this,
				element	=	jQuery('<div class="aws-ui-message"></div>');
				
				params	=	_.extend({ type: 'info', is_auto_hide: true, duration: 4000 }, params);
				
				jQuery('body').append( element );
				
				element.addClass( params.type ).addClass('show').html( params.content );
				
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
}

AWS_UI_Ajax_Button = function( container ) {
	
	return {
		init: function() {

			this.events();
		},
		events: function() {

			var _self = this;

			container.find('.wte-ajax').each(function(){

				var _self_button = jQuery(this);

				jQuery(this).click(function(e){
					e.preventDefault();

					_self.action( _self_button );
				});
			});

		},
		action: function( button ) {

			var aws_ui_message = AWS_UI_Message();

			aws_ui_message.loading();

			jQuery.post(wootrigger.ajax_url, {
				action: button.data( 'action' ),
				security: button.data( 'security' ),
				data: button.data('params')
			}, function(response){
				
				if(typeof response.status != 'undefined')
				{
					aws_ui_message.show(response.message, { type: response.status });
				}

				if(typeof button.data('triggers') != 'undefined')
				{
					jQuery.each(button.data('triggers'), function(index, value){

						container.trigger(value);
					});
				}
				
			});
		},
		refresh: function() {

			this.events();
		}
	};
}

AWS_UI_Dialog	=	function( element, options ){ 
		
	options		=	_.extend({width: 400, height: 300, title: 'Dialog'}, options);
		
	var body		=	jQuery('body'),
		template	=	jQuery('<div class="aws-ui-dialog"><div class="overlay"></div>'+
							'<div class="aws-ui-dialog_content">'+
							'<div class="aws-ui-dialog_content_title">' + options.title + '</div>'+
							'<button class="aws-ui-dialog_close"></button>' +
							'<div class="aws-ui-dialog_content_body"></div>' +
							'<div class="aws-ui-dialog_content_action">'+
								'<div class="pull-right">'+
									'<button class="button btn-cancel">Cancel</button>'+
									'<button class="button button-primary btn-ok">OK</button>'+
								'</div>'+
							'</div>' +
						'</div></div>'),
		dlg_close			=	template.find('.aws-ui-dialog_close'),
		dlg_content			=	template.find('.aws-ui-dialog_content'),
		dlg_content_body	=	template.find('.aws-ui-dialog_content_body'),
		dlg_overlay			=	template.find('.overlay'),
		dlg_btn_ok			=	template.find('.btn-ok'),
		dlg_btn_cancel		=	template.find('.btn-cancel');
	
	var data;

	var dialog	=	 {
		init: function(){
			this.events();
		},
		show: function(){
			
			this.setPosition();
			
			dlg_content.find('.aws-ui-dialog_content_body').html(element);

			body.append(template);
		},
		close: function(){
			
			template.remove();
		},
		events: function(){
			var _self	=	this;
			
			jQuery(window).resize(function(){
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
				
				element.trigger('aws-ui-dialog-event-ok', data);

				_self.close();

				e.preventDefault();
			});

			if( options.show_scroll ) {

				this.show_scroll();
			}
			
		},
		setPosition: function(){
			
			var winW	=	jQuery(window).width(),
				winH	=	jQuery(window).height(),
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
		},
		setData: function(response){
			data = response;
		},
		show_scroll: function() {
			dlg_content.find('.aws-ui-dialog_content_body').addClass('scroll');
		}
	};
	
	dialog.init();
	
	return dialog;
}

AWS_UI_Dropdown =	function( container, options ){

	var elements = container.find('.aws-ui-dropdown');

	return {
		init: function() {

			this.events();
		},
		events: function() {

			var _self = this;

			elements.click(function(e){

				_self.action( jQuery(this) );
			});

			elements.find('button').click(function(e){
				e.preventDefault();
			});

			// /* Click outsite
			jQuery(document).click(function(event){
				if(!jQuery(event.target).closest(elements).length) {
			        if(elements.hasClass('open')) {
			            elements.removeClass('open');
			        }
				}
			});
		},
		action: function( current_element ) {

			elements.not(current_element).removeClass('open');

			if(current_element.hasClass('open'))
			{
				current_element.removeClass('open');
			}else{

				current_element.addClass('open');
			}
		},
		refresh: function() {

			elements = container.find('.aws-ui-dropdown');

			this.events();
		}
	}
}

AWS_UI_Toogle	=	function( container, options ){

	return {
		init: function() {

			var _self = this;

			this.events();

			container.find('.aws-ui-switch').each(function(){

				_self.action_switch( jQuery(this), jQuery(this).find('input[type=checkbox]').is(':checked') )
			});
		},
		events: function() {

			var _self = this;

			container.find('.aws-ui-switch').click(function(e){

				_self.action( jQuery(this) );
			});
		},
		action: function( button ) {

			var input = button.find('input[type=checkbox]');

			var is_enabled = 0;

			if(input.is(':checked'))
			{
				is_enabled = 0;

				input.prop('checked', false);

				this.action_switch( button, false );

			}else{

				is_enabled = 1;

				input.prop('checked', true);

				this.action_switch( button, true );
			}

			button.trigger('aws-ui-switch', is_enabled);
		},
		action_switch: function( button, is_checked ) {

			if( is_checked ){
				button.removeClass('aws-ui-switch__off');
				button.addClass('aws-ui-switch__on');
			}else{
				button.removeClass('aws-ui-switch__on');
				button.addClass('aws-ui-switch__off');
			}
		},
		refresh: function() {

			this.events();
		}
	}
}

AWS_Table_Data = function( container, options ){

	var options = jQuery.extend( { delay: 0 }, options );

	var ajax_action 	=	container.data('ajax-action'),
		ajax_security 	=	container.data('ajax-security'),
		ajax_url 		=	container.data('ajax-url'),
		ajax_params 	=	container.data('ajax-params');

	var tbody 			=	container.find('tbody');
		
	return {
		page: 1,
		init: function(){

			var _self = this;

			this.ajax_buttons = new AWS_UI_Ajax_Button( container );
			this.dropdowns    = new AWS_UI_Dropdown( container );
			this.toogles 	  = new AWS_UI_Toogle( container );

			delay = options.delay * 1000;

			container.on('aws-table-data-no-data', function( event, response ){

				_self.hide_pagination();
				_self.clear_data();
			});

			container.on('aws-table-data-loading-data', function( event, response ){

				_self.load_data( 1 );
			});

			container.on('aws-table-data-refresh-data', function(){

				_self.load_data( _self.page );
			});

			container.find('input.aws-ui-table-data-checkall').click(function(){
				if(jQuery(this).is(':checked')){
					container.find('input.select_row').prop('checked', true);
				}else{
					container.find('input.select_row').prop('checked', false);
				}
			});

			setTimeout(function(){

				_self.load_data( 1 );
				
			}, delay);
			
		},
		post: function( params ){

			params = jQuery.extend( ajax_params, params );

			return jQuery.post( ajax_url, {
				action: ajax_action,
				security: ajax_security,
				data: params
			} );
		},
		set_content: function( data ){

			if(data.status == 'success') {

				tbody.html( data.html );

				this.page = data.pagination.page;

				this.load_pagination( data.pagination );
			}

			if( typeof data.exclude != 'undefined' ) {

				container.data('exclude', data.exclude);
			}

			container.trigger( 'aws-table-data-after-loading-data', data );

			this.ajax_buttons.refresh();
			this.dropdowns.refresh();
			this.toogles.refresh();
		},
		has_data: function() {

			return container.find('tbody tr').length;
		},
		load_data: function( page ){
			
			var _self = this,
				dfd   = jQuery.Deferred();

			container.addClass('loading');

			jQuery.when( this.post({page: page, method: false}) ).then(function( response ){
				
				_self.set_content( response );

				container.removeClass('loading');

				dfd.resolve( response );
			});

			return dfd.promise();
		},
		action: function( name, params ){

			var params = jQuery.extend({ data: {is_refresh: 1} }, params);
			

			var _self = this,
				dfd   = jQuery.Deferred();

			container.addClass('loading');

			jQuery.when( this.post({ method: name, action_data: params.data }) ).then(function( response ){

				if( params.is_refresh ){
					tbody.empty();
					_self.set_content( response );
				}

				container.removeClass('loading');

				if( typeof response.message != 'undefined' ){
					AWS_UI_Message().inform( response.message );
				}

				container.find('input.aws-ui-table-data-checkall, input.select_row').prop('checked', false);

				dfd.resolve( response );
			});

			return dfd.promise();
		},
		edit: function( data ){

			var _self = this;

			jQuery.when( this.post({ method: 'edit' }) ).then(function( response ){

				
			});
		},
		get_selected: function(){

			var ids	=	[]
										
			jQuery('input.select_row:checked').map(function(){

			 	ids.push(jQuery(this).val());

			});

			return ids;
		},
		load_pagination: function( pagination ){

			var _self 		 = this;
			var pagi_element = container.find('.aws-ui-pagination'),
				pagi_pages   = pagi_element.find('.pages');

			if(pagi_element.length == 0){ return false; }

			pagi_pages.empty();

			pagi_element.css('opacity', 1);

			if( pagination.total == 0 ){
				pagi_element.css('opacity', 0);
				return false;
			}

			for(var i = 0; i < pagination.pages; i++){
				var page = (i+1),
					page_element = jQuery('<a class="page" data-page="' + page + '">' + page + '</a>');

					if(i == pagination.page - 1){
						page_element.addClass('active');
					}

					page_element.click(function(e){
						e.preventDefault();

						var dfd  = jQuery.Deferred(),
							page = jQuery(this).data('page');

						jQuery.when( _self.load_data( page ) ).then(function(){

							container.data('page', page);
						});
					});

					pagi_pages.append(page_element);
			}

			pagi_element.find('.page-next').click(function(){
				pagi_element.find('.active').next('.page').trigger('click');
			});

			pagi_element.find('.page-prev').click(function(){
				pagi_element.find('.active').prev('.page').trigger('click');
			});
		},
		hide_pagination: function(){

			var pagi_element = container.find('.aws-ui-pagination'),
				pagi_pages   = pagi_element.find('.pages');

				pagi_element.css('opacity', 0);
				pagi_pages.empty();
		},
		clear_data: function(){

			container.find('tbody tr').not('.nodata').remove();
		},
		refresh: function(){

			this.load_data( this.page );
		}
	};
}