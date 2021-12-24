jQuery( function( $ ) {
	
	function QM_Model(){
		
		var	security	=	quizmaker.security;
		
		return {
			get: function(data){
				
				data	=	_.extend({security: security}, data);
				
				var dfd = $.Deferred();

				var ajax_url = quizmaker.site_url + '?qm-ajax=' + data.action;
				
				$.getJSON(ajax_url, data, function(response){
					dfd.resolve(response);
				});
				
				return dfd.promise();
			},
			post: function(data){

				data	=	_.extend({security: security}, data);
				
				var dfd = $.Deferred();

				var ajax_url = quizmaker.site_url + '?qm-ajax=' + data.action + '&security=' + security;

				$.ajax({
				  type: "POST",
				  method: "POST",
				  url: ajax_url,
				  data: data,
				  success: function(response){
				  	dfd.resolve(response);
				  },
				  dataType: 'JSONP'
				});
				
				return dfd.promise();

			},
			validate: function(){
				
			}
		};
	};
	
	function QM_Doing_Timer() {
		
		return {
			init: function(params){
				
				this.params		=	params;
				this.container	=	$('.stage-timer');
				this.duration	=	params.duration * 60;
				
				switch(params.theme){
					case 'theme_1':
						this.theme	=	this.theme_1();
					break;
					case 'theme_2':
						this.theme	=	this.theme_2();
					break;
				}
				
				this.start();
			},
			start: function(){
				
				var _self	=	this,
					current	=	this.params.time_passed;
				
				this.theme.init();
				
				
				this.timeout = setInterval(function(){
				
					current++;
				
					if(_self.duration > 0 && current == _self.duration){
					
						_self.end();
					
						if(typeof _self.params.complete == 'function'){
						
							_self.params.complete();
						}
					}
				
					_self.theme.progress(current);
				
				}, 1000);
				
				
			},
			
			end: function(){
				
				clearInterval(this.timeout);
				
			},
			duration_format: function( total_seconds ) {
				
				var miliseconds	=	total_seconds * 1000;
				
				var duration	=	moment.duration( miliseconds );
				
				var hours		=	duration.hours(),
					minutes		=	duration.minutes(),
					seconds		=	duration.seconds();
					
					if( hours	<	10	) {	hours 	= '0' + hours;	}
					if( minutes	<	10	) { minutes	= '0' + minutes;	}
					if( seconds < 	10	) { seconds = '0' + seconds;	}
				
				return '<span class="hour">' + hours + '</span>'+
							'<span class="sepa">:</span>'+
						'<span class="minute">' + minutes + '</span>'+
							'<span class="sepa">:</span>'+
						'<span class="second">' + seconds + '</span>';
			},
			theme_1: function(){
				
				var utility		=	this;
				
				var container		=	this.container,
					duration		=	this.duration,
					duration_format	=	this.duration_format(duration);
					
				return {
					init: function(){
						
						this.template	=	$('<div class="qm-timer-theme-1">'+
												'<div class="progress-bar"><div class="progress-bar-value"></div></div>'+
												'<div class="progress-time"><div class="progress-time-value"></div>'+
												'<div class="duration"><span>Duration:</span><div>' + duration_format + '</div></div>'+
												'</div>'+
											'</div>');
						
						this.bar_value	=	this.template.find('.progress-bar .progress-bar-value');
						this.time_value	=	this.template.find('.progress-time .progress-time-value');
						
						container.html(this.template);
					},
					progress: function( t ){
						
						this.bar_value.css('width', ((t * 100)/duration) + '%');
						this.time_value.html(utility.duration_format(t));
					},
					complete: function(){
						
					},
					timer: function(t){
						var hour	=	parseInt(t / 3600),
							minute	=	parseInt((t % 3600)/60),
							second	=	(t % 3600) % 60;
						
						if( hour	<	10	) {	hour 	= '0' + hour;	}
						if( minute	<	10	) { minute	= '0' + minute;	}
						if( second 	< 	10	) { second 	= '0' + second;	}
						
						return '<span class="hour">' + hour + '</span>'+
									'<span class="sepa">:</span>'+
								'<span class="minute">' + minute + '</span>'+
									'<span class="sepa">:</span>'+
								'<span class="second">' + second + '</span>';
					}
				};
			},
			theme_2: function(){
				
				var container	=	this.container,
					duration	=	this.duration;
				
				return {
					init: function(){
						this.template	=	$('<div class="qm-timer-theme-2">'+
												'<div class="progress-time"><div class="progress-time-value"></div></div>'+
												'<div class="duration"><div class="progress-bar-value"></div></div>'+
											'</div>');
											
						container.html(this.template);
					},
					progress: function(){
						
					},
					complete: function(){
						
					}
				};
			}
		};
	}
	
	function QM_Doing_sidebar() {
		
		var sidebar	=	$('.sidebar-stage-timer');
		
		return {
			init: function(params){
				
				this.events();
				this.setPage(1);
				this.setScroll();
			},
			events: function(){
				var _self	=	this;
				
				$('.sidebar-toolbar-minimize').click(function(e){
					e.preventDefault();
					
					if( !sidebar.hasClass('minimize') ){
						sidebar.removeClass('expand').addClass('minimize');
					}
				});
				
				$('.sidebar-toolbar-expand').click(function(e){
					e.preventDefault();
					
					if( !sidebar.hasClass('expand') ){
						sidebar.removeClass('minimize').addClass('expand');
					}
				});
				
				$('.questions-minilist_page').click(function(){
					var page	=	$(this).data('page'),
						id		=	$(this).data('id');
					
					$('body').trigger( 'quizmaker_event_change_page', { page: page } );
					
					_self.setPage( page );
					
				});
				
				$('.questions-minilist_page .sidebar-question').click(function(){
					
					if($(this).parent('.active').length == 0) {
						return false;
					}
					
					var page	=	$(this).data('page'),
						id		=	$(this).data('id'),
						target	=	$('#' + id);
					
					if (target.length) {
						
						$('html, body').animate({
						  scrollTop: target.offset().top
						}, 1000);
						
						return false;
					}
					
				});
				
			},
			setScroll: function(){
				
				var containerScroll			=	sidebar.find('.container-scroll');
				var heightContainerScroll 	=	sidebar.height() - 185;
				
				currentHeightScroll	=	sidebar.find('.container-scroll').height();
				
				if( heightContainerScroll < currentHeightScroll ){
					
					containerScroll.addClass('active');
					
					sidebar.find('.container-scroll').height(heightContainerScroll);
					
				}else{
					sidebar.find('.container-scroll').height('100%');
					containerScroll.removeClass('active');
				}
				
				
			},
			setPage: function( page ) {
				
				var pagination = $('.questions-minilist_page');
				
				this.activePage = pagination.filter('.page-' + page);
				
				pagination.removeClass('active');
				
				this.activePage.addClass('active');
				
			},
			setQuestion: function( data ) {
				
				var questions	=	this.activePage.children('.sidebar-question');
				
				var question	=	questions.filter(function(){
					
					return $(this).data('id') == data.id;
				});
				
				if(data.hasData == 1){
					question.addClass('done');
				}else{
					question.removeClass('done');
				}
			}
		};
	}
	
	function QM_Doing(){
		
		var model	=	new QM_Model();
		
		var stage_doing		=	$('.doing-stage'),
			stage_questions	=	stage_doing.find('.stage-questions');

		var pagi_items		=	$('.doing-stage .stage-pagination li'),
			total_pages		=	parseInt(stage_doing.data('pages'));

		return {
			sidebar: QM_Doing_sidebar(),
			is_loading_new_page: false,
			current_page: 1,
			init: function(){		
				
				var _self	=	this;
				
				$('body').on('quizmaker_event_change_page', function( event, data ){
					
					_self._showPage( data.page );

				}).on('quizmaker_event_last_page', function(){

					stage_doing.find('.qm-btn-submit-test').addClass('finished');
				});

				// $('body').on('quizmaker_load_new_page_complete', function(){

				// 		MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
				// });
				
				this.sidebar.init();
				this._showPage(1);
				this._events();

				

				if( quizmaker.type_testing == 1 ){
					this.adaptiveEvents();
				}

				if( total_pages == 1 ) {
					$('.quizmaker-direction').css('display', 'none');
				}
			},
			adaptiveEvents: function(){

				var max_round 			=	quizmaker.adaptive_max_round,
					current_round		=	quizmaker.adaptive_round,
					percent_correct		=	quizmaker.adaptive_percent;

				var progress_max_round = new ProgressBar.SemiCircle( '#adaptive_max_round_container', {
				  strokeWidth: 4,
				  color: '#ca0808',
				  trailColor: '#CFCFCF',
				  text: {
				  	value: 'Round 3',
				  	alignToBottom: true
				  },
				  step: function(state, circle, attachment) {

				  		var value = Math.round(circle.value() * 100);

				  		if(max_round > 0){
					  		if (value === 0) {
						      circle.setText( 0 );
						    } else {
						      circle.setText(value + '%');
						    }
						}else{

							circle.setText('<i class="material-icons adaptive_infinite_icon">all_inclusive</i>');
						}
   					}
				});

				var progress_percent = new ProgressBar.SemiCircle( '#adaptive_percent_container', {
				  strokeWidth: 4,
				  color: '#ca0808',
				  trailColor: '#CFCFCF',
				  text: {
				  	value: '1',
				  	alignToBottom: true
				  },
				  step: function(state, circle, attachment) {

				  		var value = Math.round(circle.value() * 100);

				  		if (value === 0) {
					      circle.setText( 0 );
					    } else {
					      circle.setText(value + '%');
					    }
   					}
				});

				progress_max_round.animate( current_round/max_round, {duration: 1000} );
				progress_percent.animate( percent_correct/100, {duration: 1000} );
			},
			_isLoaded: function( page ){
				
				if(stage_questions.find('.qm-page-' + page).length > 0){
					
					return true;
				}else{
					
					return false;
				}
				
			},
			_events: function(){
				
				_self	=	this;
						
				pagi_items.click(function(e){

					if( _self.is_loading_new_page ) {
						return false;
					}
					
					var page_index	=	parseInt($(this).data('index')) + 1;

					_self._showPage( page_index );
						
					e.preventDefault();
				});

				stage_doing.find('.qm_next_page').click(function(e){
					e.preventDefault();

					_self._nextPage();
					
				});

				stage_doing.find('.qm_prev_page').click(function(e){
					e.preventDefault();

					stage_doing.find('.qm_next_page, .qm_prev_page').removeClass('inactive');

					_self._prevPage();

					if( _self.current_page == 1 ) {

						stage_doing.find('.qm_prev_page').addClass('inactive');
					}

				});

				$('body').on('quizmaker_on_next_page', function(){

					_self._nextPage();
				});

				$('body').on('quizmaker_on_prev_page', function(){

					_self._prevPage();
				});

				$('body').on('quizmaker_load_new_page_complete', function(){

					_self.is_loading_new_page = false;
				});
				
				$('body').on('quizmaker_event_change_page', function( event, data ){
					
					_self.set_state_pagination( data.page );
				});
				
				pagi_items.eq(0).addClass('active');
				
			},
			set_state_pagination: function( page ){
				
				var pagi_items	=	$('.doing-stage .stage-pagination li');
				
				pagi_items.removeClass('active');
				
				pagi_items.eq( page - 1 ).addClass('active');
				
			},
			_add_instant_answer: function( question ){

				if(!question){ return false; }

				var _self = this;
				
				question.find('.qm_btn_instant_answer').click(function(e){
					e.preventDefault();

					_self._activeLoading(true);

					model.get({
						action: 'get_instant_answer', 
						post_id: quizmaker.post_id,
						security: quizmaker.security,
						id: parseInt(question.data('id')),
						answer: question.find('input,textarea').serializeArray()
					}).then(function( response ){
						
						var instant_answer = $(response.data);

						question.find('.question_main_content').css('display', 'none');
						question.find('.question_wrap_content').append( instant_answer );
						
						instant_answer.find('.question_next_page').click(function(e){
							e.preventDefault();

							$('body').trigger('quizmaker_on_next_page');
						})
						
						_self._activeLoading(false);
						
					});
				});
			},
			_add_event_question: function( page ) {
				
				var _self		=	this,
					page_index	=	page.data('index');
				
				page.find('.info_explanation').click(function(){
					
					var current_question	=	$(this).parents('.question');
					
					var explanation	=	current_question.find('.explanation');
					
					if(explanation.hasClass('show')){
						explanation.removeClass('show');
						$(this).removeClass('active');
						explanation.hide();
					}else{
						$(this).addClass('active');
						explanation.addClass('show');
						explanation.show();
					}
				});	
				
				page.children('.question').each(function(){
					
					var question	=	$(this);
					
					if(_self.is_question_single(question)){
					
						question = _self._add_event_question_single( question, page_index );
					
					}else if(_self.is_question_multiple(question)) {
					
						question = _self._add_event_question_multiple( question, page_index );
				
					}else if(_self.is_question_fill_blank(question)) {
					
						question = _self._add_event_question_fill_blank( question, page_index );
				
					}else if(_self.is_question_drag_match(question)) {
					
						question = _self._add_event_question_drag_match( question, page_index );
					
					}else if(_self.is_question_group_match(question)) {

						question = _self._add_event_question_group_match( question, page_index );

					}else if(_self.is_question_order(question)) {
						
						question = _self._add_event_question_order( question, page_index );

					}else if(_self.is_question_guess_word(question)) {

						question = _self._add_event_question_guess_word( question, page_index );

					}else if(_self.is_question_keywords(question)) {

						question = _self._add_event_question_keywords( question, page_index );
					}

					question = _self._add_event_start_play( question, page_index );

					_self._add_instant_answer( question );
					
				});
				
				return page;
			},
			_activeLoading: function( status ){

				$('body').trigger('quizmaker_doing_loading', status);

			},
			_nextPage: function(){

				if( _self.is_loading_new_page ) {
						return false;
					}

				var next_page = _self.current_page + 1;

				_self._showPage( next_page );
			},
			_prevPage: function(){

				if( _self.is_loading_new_page ) {
						return false;
					}

				var next_page = _self.current_page - 1;

				_self._showPage( next_page );
			},
			_showPage: function( page ){
				
				if( page <= 0 || page > $('.doing-stage .stage-pagination li').length ) {
					return false;
				}
				
				var _self	=	this;

				this._activeLoading( true );
				this.current_page = page;

				_self.sidebar.setPage( page );

				$('.doing-stage .stage-pagination li').removeClass('active');
					
				$('.doing-stage .stage-pagination li').eq(page-1).addClass('active');
				
				$('body').trigger('quizmaker_event_changed_page', { page: page, is_last: (page == total_pages) } );

				if( page == total_pages ) {

					$('body').trigger('quizmaker_event_last_page');
				}

				if(!this._isLoaded(page))
				{
					$.when(this._loadQuestions( page )).then(function(response){
						
						_self._activeLoading( false );

						stage_questions.find('.qm-page').removeClass('active');
						stage_questions.find('.qm-page-' + page).addClass('active');
						
						$('body').animate({scrollTop:0}, '500');
					});
					
				}else{
					
					this._activeLoading( false );

					stage_questions.find('.qm-page').removeClass('active');
					stage_questions.find('.qm-page-' + page).addClass('active');

					$('body').animate({scrollTop:0}, '500');
				}

				stage_doing.find('.qm_next_page, .qm_prev_page').removeClass('inactive');

				if( $('.doing-stage .stage-pagination li').length == this.current_page ) {

					stage_doing.find('.qm_next_page').addClass('inactive');

				}else if( this.current_page == 1 ){

					stage_doing.find('.qm_prev_page').addClass('inactive');
				}
				
			},
			_loadQuestions: function( page, data ){
				
				var _self	=	this,
					dfd 	= $.Deferred();

				var pre_page = page - 1;
					pre_data = this._get_data_at_page( pre_page );
				
				this.is_loading_new_page = true;

					model.get({
					action: 'get_doing_questions', 
					post_id: quizmaker.post_id,
					security: quizmaker.security,
					page: page,
					pre_page: pre_page,
					pre_data: pre_data
					}).then(function( response ){
						
						if(response == null || (typeof response.is_expired != 'undefined' && response.is_expired == 1)){
							
							dfd.reject(response);
							
						}else{
							var question	=	_self._add_event_question( $(response.questions_html) );
						
							stage_questions.append(question);

							stage_questions.fitVids();
						
							dfd.resolve(response);
						}
						
						$('body').trigger('quizmaker_load_new_page_complete', page);
					});

				
				return dfd.promise();
			},
			_get_data_at_page: function( page ){
				
				if( page < 0 ) {
					return [];
				}
				
				var	questions  =	$('.questions.qm-page-' + page).find('.question');
				
				return questions.find('input,textarea').serializeArray();
			},
			_add_event_start_play: function( question, page ) {
				
				if( question.hasClass('is-starting') ) {

					var qiv = null;

					question.find('.start-intro').click(function(){

						question.removeClass('is-starting').addClass('is-started');

						var qtv = question.find('.qm-timer-value'),
							qtvt = parseInt(qtv.data('timeout'));

							if( qtvt > 0 ){
								
								qiv = setInterval(function(){

									qtvt--;

									qtv.text(qtvt);

									if( qtvt == 0 ) {

										question.addClass('finished');

										clearInterval(qiv);
									}

								}, 1000);
							}

					});
				}
				
				return question;

			},
			_add_event_question_drag_match: function( question, page ){
				
				var _self			=	this,
					container 		=	question.find('.group-drag-match'),
					group_questions =	container.find('.match-question'),
					group_answers	=	container.find('.match-answer');
				
				var hasValue		=	function(){
					
					var inputAnswers	=	group_answers.find('.q'),
						isValue			=	false;
					
					inputAnswers.each(function(){
						
						if($(this).val() != '' && isValue == false) {
							isValue = true;
						}
						
					});
					
					if( isValue ){
						
						_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
					}else{
						_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
					}
					
				}
					
					group_questions.draggable({ 
						containment: container,
						revert: 'invalid',
						cursor: "move"
					});
					
					container.find('.match-question-container').droppable({
						drop: function( event, ui ) {
							
							if($(this).find('.match-question').length == 0) {
								ui.draggable.detach().css({top: 0,left: 0}).appendTo($( this ));
								
								
							}else{
								ui.draggable.draggable( "option", "revert", true );
							}
							
						}
					});
					
					group_answers.each(function(){
						$(this).droppable({
							activeClass: "ui-state-hover",
							hoverClass: "ui-state-active",
							drop: function( event, ui ) {
							
								if($(this).find('.match-question').length == 0) {
									ui.draggable.detach().css({top: 0,left: 0}).appendTo($( this ).find('.value'));
								
									$(this).find('.q').val(ui.draggable.data('value'));
								
								}else{
									ui.draggable.draggable( "option", "revert", true );
								}
								
								$(this).addClass( "ui-state-highlight" );
								
								hasValue();
								
							},
							out: function( event, ui ){
								
								var out_value	=	ui.draggable.data('value'),
									cur_value	=	$(this).find('.q').val();
								
									if(out_value == cur_value){
										$(this).find('.q').val('');
									}
									
									hasValue();
							}
					    });
					});
				
				return question;
			},
			_add_event_question_group_match: function( question, page ){

				var _self				=	this,
					container 			=	question.find('.group-group-match'),
					container_items 	=	container.find('.match-items'),
					container_groups	=	container.find('.match-groups');

				var hasValue =	function(){
					
					if( container.find('.match-items .match-answer').length == 0 ){
						
						_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
					}else{
						_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
					}
					
				}

					container.find('.match-items, .match-group').sortable({
						items: '> div',
						connectWith: container.find('.connectedSortable'),
						stop: function( event, ui ){

							container.find('.match-group').each(function( index, group ){

								var ids = [];

								$(group).find('.match-answer').each(function( index, answer ){

									ids.push( parseInt($(answer).data('id')));
								});

								$(group).find('input').val(ids);
							});

							hasValue();
						}

				    }).disableSelection();

				return question;
			},
			_add_event_question_single: function( question, page ){
				
				var _self	=	this;
				
					question.find('input').change(function(){
					
						_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
					
					});

				var group_radio = question.find('.group-box-radio');

					group_radio.click(function(e){

						group_radio.removeClass('checked');

						$(this).addClass('checked');
					});
				
				return question;
			},
			_add_event_question_multiple: function( question, page ){
				
				var _self	=	this,
					input	=	question.find('input');
				
					input.change(function(){
					
						if(input.is(':checked')){
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						}else{
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
						}
					});

					question.find('.group-box-checkbox').click(function(e){

						if($(this).hasClass('checked')) {

							$(this).removeClass('checked');

						}else{

							$(this).addClass('checked');

						}
					});
				
				return question;
			},
			_add_event_question_fill_blank: function( question, page ){
				
				var _self		=	this,
					textarea	=	question.find('textarea');
					
					textarea.keyup(function(){
						
						if($(this).val() != ''){
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						}else{
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
						}
						
					});
				
				return question;
			},
			_add_event_question_order: function( question, page){
				
				var _self			=	this,
					container 		=	question.find('.group-order');

				setTimeout(function(){

					container.sortable({
						items: '> li',
						placeholder: "ui-sortable-placeholder",
						create: function( event, ui ){
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						},
						change: function( event, ui ){
							
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						}
					});
				}, 1000);
					

				return question;
			},
			_add_event_question_guess_word: function( question, page ){
				
				var _self	=	this,
					input	=	question.find('input');
				
					input.change(function(){
					
						if($(this).val() != ''){
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						}else{
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
						}
					});
				
				return question;
			},
			_add_event_question_keywords: function( question, page ){
				
				var _self		=	this,
					textarea	=	question.find('textarea');
					
					textarea.keyup(function(){
						
						if($(this).val() != ''){
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						}else{
							_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 0 });
						}
						
					});
				
				return question;
			},
			get_question_single_value: function( question ) {

				return question.find('input:checked').val();

			},
			get_question_multiple_value: function( question ) {

				return question.find('input:checkbox:checked').map(function () {
						return this.value;
				}).get();

			},
			get_question_fill_blank_value: function( question ) {

				return question.find('textarea').val();

			},
			get_question_order_value: function( question ) {

				return question.find('input').val();

			},
			get_question_drag_match_value: function( question ) {

				return question.find('input').val();

			},
			is_question_drag_match: function( question ){
				
				if( question.data('type') == 'drag_match' ){
					return true;
				}
				
				return false;
			},
			is_question_group_match: function( question ){
				
				if( question.data('type') == 'group_match' ){
					return true;
				}
				
				return false;
			},
			is_question_single: function( question ){
				
				if( question.data('type') == 'single' ){
					return true;
				}
				
				return false;
			},
			is_question_multiple: function( question ){
				
				if( question.data('type') == 'multiple' ){
					return true;
				}
				
				return false;
			},
			is_question_fill_blank: function( question ){
				
				if( question.data('type') == 'fill_blank' ){
					return true;
				}
				
				return false;
			},
			is_question_order: function( question ){
				
				if( question.data('type') == 'order' ){
					return true;
				}
				
				return false;
			},
			is_question_guess_word: function( question ){

				if( question.data('type') == 'guess_word' ){
					return true;
				}
				
				return false;
			},
			is_question_keywords: function( question ){

				if( question.data('type') == 'keywords' ){
					return true;
				}
				
				return false;
			}
		}
	}
	
	setTimeout(function(){
		QM_Doing().init();
	});
	
});