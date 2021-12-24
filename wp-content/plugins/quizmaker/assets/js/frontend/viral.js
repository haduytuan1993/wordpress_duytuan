
jQuery( function( $ ) {

	QM_Engine_Viral		=	function( container, options ) {

		options = $.extend({ 
			total: 1, 
			pages: 1,
			is_form: 'no',
			is_instant_answer: 'no',
			is_show_answers: 'yes'
		}, options);

		var engine_questions = new QM_Engine_Questions( container, options );

		return {
			current_page: 1,
			init: function(){

				engine_questions.init();

				this._events();

				this.page(1);
			},
			_events: function() {

				var _self = this;

				var next = container.find('.pagination .next'),
					prev = container.find('.pagination .prev');

				var submit_btn = container.find('.qm-submit');

				engine_questions.on('change', function(){
					
					if( _self.is_last_page() ) {
						
						container.trigger('_qm_engine_last_page');
					}
				});

				submit_btn.click(function(e){

					e.preventDefault();

					submit_btn.addClass('fadeOut');
					submit_btn.animate({height: 0}, 500);

					if( options.is_form == 'yes' ){

						container.find('.action-form').addClass('s');

					}else{

						_self.load_html_result( engine_questions.get_result() ).done(function(response){

							_self.show_result( response.html, response.questions );

						});
					}
				});
				
				engine_questions.on('complete', function( answers ){

					submit_btn.addClass('bounceIn').addClass('animated');

				});


				next.click(function(e){
					e.preventDefault();

					next.removeClass('s');

					_self.next();
				});

				prev.click(function(e){
					e.preventDefault();

					prev.removeClass('s');

					_self.prev();
				});

				container.on('quizmaker-page-change', function( event, page ){
					
					container.find('.pagination li').removeClass('active');

					container.find('.pagination li').eq(page - 1).addClass('active');
				});

				container.find('.pagination a').click(function(e){
					e.preventDefault();

					_self.page( $(this).data('page') );

					_self.current_page = parseInt($(this).data('page'));
				});

				container.on('_qm_engine_last_page', function(){

					if( !_self.is_last_pages() ){

						next.addClass('s');
					}else{

						next.removeClass('s');
					}

					if( _self.current_page > 1 ){

						prev.addClass('s');

					}else{

						prev.removeClass('s');
					}
				});

				if ( options.is_form == 'yes' ) {

					var form = container.find('.wpcf7');

					form.on('wpcf7:mailsent', function(){

						_self.load_html_result( engine_questions.get_result() ).done(function( response ){

							setTimeout(function(){

								container.find('.action-form').removeClass('s');

								_self.show_result( response.html, response.questions );

							}, 2000);

						});
						
					});
				}

			},
			load_html_result: function( answers ){

				return $.post( qmviral.ajax_url, { 
					action: 'quizmaker_load_html_result', 
					security: options.result_nonce, 
					data: answers 
				});
			},
			show_result: function( content, questions ) {

				container.find('.results').html( content );

				if( options.is_show_answers == 'yes' ) {

					engine_questions.show_anwers( questions );

				}else{

					engine_questions.disable();
				}
			},
			finish: function(){

				
			},
			page: function( at ) {

				var _self = this;

				var pages = container.find('.page');

				pages.each(function(){

					if( $(this).data('page') == at ) {

						if( !$(this).hasClass('loaded') ){

							$(this).children('.question').each(function(){

								engine_questions.add_question( $(this) );
							});

							$(this).addClass('loaded');
						}

						pages.removeClass('active');
						$(this).addClass('active');

						_self.current_page = at;
					}
						
				});

				container.trigger('quizmaker-page-change', at);

			},
			is_last_page: function(){

				var _self = this;

				var page = container.find('.page').filter(function(){

					return $(this).data('page') == _self.current_page;
				});
				
				if( page.find('.s-1').length == page.find('.question').length ) {

					return true;
				}

				return false;
			},
			is_last_pages: function() {

				if( this.current_page < options.pages ) {

					return false;
				}

				return true;
			},
			pagination: function() {


			},
			next: function() {

				if( !this.is_last_pages() ) {

					this.current_page++;

					this.page( this.current_page );

					container.trigger('_qm_engine_last_pages');
				}
			},
			prev: function() {

				if( this.current_page > 1 ) {

					this.current_page--;

					this.page( this.current_page );

					container.trigger('_qm_engine_last_pages');
				}
			}
		}
	}
	
	$(document).ready(function(){
		
		$('.quizmaker-viral').each(function(){

			var viral = new QM_Engine_Viral( $(this), $(this).data('params') );

			viral.init();

		});
		
	});
	
});