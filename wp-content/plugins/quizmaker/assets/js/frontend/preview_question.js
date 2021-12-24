jQuery( function( $ ) {

	function QM_Doing(){

		return {
			is_loading_new_page: false,
			current_page: 1,
			init: function(){	

				var _self = this;	

				var question	=	$('.question');

				var page_index = 0;

				question.find('.info_explanation').click(function(){

					question.find('.explanation').toggleClass('show');
				});
				
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

			},
			_events: function(){
				
				_self	=	this;

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