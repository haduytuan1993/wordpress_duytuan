jQuery( function( $ ) {
	
	var question_type	=	$('#random_question_type');
	
	if(question_type.val() == 'selected'){
		$('#random-questions-1').css('display', 'block');
	}
	
	if(question_type.val() == 'per'){
		$('#random-questions-2').css('display', 'block');
	}
	
	question_type.change(function(){
		$('#random-questions-1, #random-questions-2').css('display', 'none');
		
		if($(this).val() == 'selected'){
			$('#random-questions-1').css('display', 'block');
		}
		
		if($(this).val() == 'per'){
			$('#random-questions-2').css('display', 'block');
		}
	});

	var FixedQuestions = function(){

		return {
			init: function(){

				this.ajax_action     = $('.qm-list-fixed-questions').data('ajax-action');
				this.ajax_security   = $('.qm-list-fixed-questions').data('ajax-security');
				this.ajax_url 		 = $('.qm-list-fixed-questions').data('ajax-url');
				this.ajax_params 	 = $('.qm-list-fixed-questions').data('ajax-params');

				this.table_data = new AWS_Table_Data($('.qm-list-fixed-questions'), { delay: 1 });
				
				this.events();

				this.table_data.init();
			},
			events: function(){
				var _self	=	this;
				
				$('#add-fixed-question').click(function(e){

					var question_ids = $('#input-add-fixed_questions').val();

					if( question_ids ){
						
						$.when(_self.table_data.action( 'add', { data: { is_refresh: 1, ids: question_ids } } )).then(function(){
							
							_self.table_data.refresh();

							$('#input-add-fixed_questions').val('').trigger('change');
						});
						
					}
					
					e.preventDefault();
				});

				$('#order-fixed-question').click(function(e){
					
					var btn				=	$(this);
					
						btn.addClass('disabled');
					
					if(_self.table_data.has_data() > 0){

						var order_data = [];

						$.post(_self.ajax_url, {
										action: _self.ajax_action,
										security: _self.ajax_security,
										data: {
											test_id: _self.ajax_params.test_id,
											method: 'get_order',
											action_data:{is_refresh: 0}
										}
									}, function( response ){

							var html_questions = $(response.html);

							var dialog = new AWS_UI_Dialog( html_questions, { title: 'Order Questions', width: 500, height: 600, show_scroll: true } );

							dialog.show();

							html_questions.sortable({
								stop: function( event, ui ) {

									order_data = $(this).sortable( "toArray", { attribute: 'data-id' } );
								}
							});

							html_questions.on('aws-ui-dialog-event-ok', function(){

								if( order_data.length > 0 ){

									$.post(_self.ajax_url, {
										action: _self.ajax_action,
										security: _self.ajax_security,
										data: { 
											test_id: _self.ajax_params.test_id,
											method: 'update_order',
											action_data: {is_refresh: 1, ids: order_data} }
									}, function(){
										_self.table_data.refresh();
									});

								}
							});

							html_questions.on('aws-ui-dialog-event-cancel', function(){

								_self.table_data.refresh();
							});

							btn.removeClass('disabled');
						});
					}
					
					e.preventDefault();
				});

				$('#remove-fixed-question').click(function(e){
					
					var question_ids	=	_self.table_data.get_selected(),
						btn				=	$(this);
					
						btn.addClass('disabled');
					
					if(question_ids.length > 0){

						$.when(_self.table_data.action( 'remove', { data: { is_refresh: 1, ids: question_ids } } )).then(function(){

							_self.table_data.refresh();

							btn.removeClass('disabled');
						});
					}
					
					e.preventDefault();
				});
			}
		}
	}
		
	var AssignUsers	=	function(){
		
		return {
			init: function(){

				this.table_data = new AWS_Table_Data($('.qm-list-assign-users'), { delay: 3 });
				
				this.events();

				this.table_data.init();
			},
			events: function(){
				var _self	=	this;
				
				$('#lightbox-assign-users').click(function(e){
					
					var user_ids = $('#input-assign-users').val();

					if( user_ids ){

						$.when(_self.table_data.action( 'add', { data: { ids: user_ids } } )).then(function(){

							_self.table_data.refresh();
							$('#input-assign-users').val('').trigger('change');
						});
						
					}
					
					e.preventDefault();
				});
				
				$('#remove-assign_users').click(function(e){
					
					var user_ids	=	_self.table_data.get_selected(),
						btn			=	$(this);
					
						btn.addClass('disabled');
					
					if(user_ids.length > 0){

						$.when(_self.table_data.action( 'remove', { data: { ids: user_ids } } )).then(function(){

							_self.table_data.refresh();
							btn.removeClass('disabled');
						});
					}
					
					e.preventDefault();
				});
				
				$('#email-assign-users').click(function(e){
					e.preventDefault();

					var ids	=	_self.table_data.get_selected(),
						btn		=	$(this);
					
					btn.addClass('disabled');
					
					if(ids.length > 0){

						$.when(_self.table_data.action( 'email', { data: {ids: ids}, is_refresh: false } )).then(function(){

							btn.removeClass('disabled');
						});
					}
					
					e.preventDefault();
					
				});
			},
			is_save_validate: function( data ){
				if(data == '' || typeof data == 'undefined') { return false; }
				
				return true;
			},
			is_remove_validate: function( data ){
				
				if(typeof data == 'undefined') { return false; }
				
				return true;
			},
			is_email_add_validate: function( users_id ){
				
				if(typeof users_id == 'undefined' || users_id.length == 0) return false;
				
				return true;				
			}
		}
	};
	
	var RankingData	=	function(){
		
		return {
			init: function(){
				this.events();
			},
			events: function(){
				
				var _self	=	this;
				
				$('#qm-table-ranking_data .edit_ranking').click(function(e){
					e.preventDefault();
					
					_self.edit( $(this) );
					
				});
				
				$('#qm-table-ranking_data .save_ranking').click(function(e){
					e.preventDefault();
										
					_self.save( $(this).data('id') );
					
				});
				
				$('#qm-table-ranking_data .close_ranking').click(function(e){
					e.preventDefault();
										
					_self.close( $(this).data('id') );
					
				});
				
				$('#qm-table-ranking_data .remove_ranking').click(function(e){
					e.preventDefault();
					
					_self.remove( $(this) );
					
				});
				
				$('#add-ranking').click(function(e){
					e.preventDefault();
					
					_self.add();
					
				});
			},
			edit: function( element ){
				
				var row_data	=	element.parent('td').parent('tr'),
					row_datas	=	$('#qm-table-ranking_data .row_data'),
					row_inputs	=	$('#qm-table-ranking_data .qm-table-group-input'),
					row_input	=	row_data.next('.qm-table-group-input');
	
					if(!row_input.hasClass('active')){
		
						row_inputs.removeClass('active');
						row_input.addClass('active');
		
						row_datas.removeClass('active');
						row_data.addClass('active');
		
					}else{
		
						row_data.removeClass('active');
						row_input.removeClass('active');
					}
				
			},
			add: function(){
				
				var _self	=	this;
				
				var container_data	=	$('#qm-table-ranking_data .tbody_data'),
					row				=	$('#qm-table-ranking_data .qm-table-group-input-dump').clone();

				row.removeClass('qm-table-group-input-dump').addClass('qm-table-group-input active');
				
				row.find('.close_add_ranking').click(function(e){
					e.preventDefault();
					
					row.remove();
				});
				
				row.find('.add_ranking').click(function(e){
					e.preventDefault();
					
					var min_val		=	row.find('input[name=qm_ranking_min]').val(),
						max_val		=	row.find('input[name=qm_ranking_max]').val(),
						name_val	=	row.find('input[name=qm_ranking_name]').val(),
						cer_val		=	row.find('select[name=qm_ranking_certificate]').val();

					var inputs 		=	row.find('input, select');

					var data = {};

					inputs.each(function(){

						data[$(this).attr('name').replace('qm_ranking_', '')] = $(this).val();

					});

					_self.store( data ).then(function( response ){

						var id		=	response.id;

						var new_row		=	row.clone(),
							row_data	=	$('.row_data.dump').clone();

						var edit_action		=	row_data.find('.edit_ranking'),
							remove_action	=	row_data.find('.remove_ranking');
							
							edit_action.data('id', id);
							remove_action.data('id', id);
							
							edit_action.removeClass('hide').click(function(e){
								e.preventDefault();

								_self.edit( $(this) );
							});

							remove_action.removeClass('hide').click(function(e){
								e.preventDefault();

								_self.remove( $(this) );
							});

							row_data.removeClass('dump');
							
							row_data.data('id', id);

							row_data.find('.min_data').html(min_val);
							row_data.find('.max_data').html(max_val);
							row_data.find('.name_data').html(name_val);
							row_data.find('.certificate_data').html(response.data.certificate);

							container_data.append(row_data);

						var new_input	=	row.clone();

							new_input.removeClass('active').addClass('qm-table-group-input');
							
							new_input.find('input[name=qm_ranking_min]').attr('name', 'qm_ranking_' + id + '_min').val(min_val);
							new_input.find('input[name=qm_ranking_max]').attr('name', 'qm_ranking_' + id + '_max').val(max_val);
							new_input.find('input[name=qm_ranking_name]').attr('name', 'qm_ranking_' + id + '_name').val(name_val);
							new_input.find('select[name=qm_ranking_certificate]').attr('name', 'qm_ranking_' + id + '_certificate').val(cer_val);

							new_input.find('.add_ranking, .close_add_ranking').remove();
							new_input.find('.save_ranking').removeClass('hide').data('id', id).click(function(e){

								_self.save($(this).data('id'));

								e.preventDefault();
							});

							container_data.append(new_input);
							container_data.find('.ranking_nodata').addClass('hide');

							row.remove();
							
							QM().Message().inform( 'Success!' );
							
					}, function( error ){

						QM().Message().inform( error, {type: 'qm-error'} );
					});
					
				});
				
				$('#qm-table-ranking_data tbody.tbody_add').append(row);
				
				
			},
			store: function(data){
				
				return QM_Model({ is_validated: this.is_save_validate(data) }).get({
					action: 'quizmaker_save_ranking',
					tid: meta_boxes_test_script.post_id,
					data: data
				});
			},
			save: function( id ){
				
				var _self		=	this,
					group_input = 	$('.row_data[data-id=' + id + ']').next('.qm-table-group-input.active'),
					data 		=	{ id: id };

				group_input.find('input,select').each(function(){

					data[$(this).attr('name').replace('qm_ranking_' + id + '_', '')] = $(this).val();

				});
		
				this.store(data).then(function( response ){
					
					if(typeof response.data != 'undefined'){
						_self.updateActiveRowData( response.data );
						_self.closeActiveRowData();
						
						QM().Message().inform( 'Success!' );
					}
					
				}, function( error ){

					QM().Message().inform( error, {type: 'qm-error'} );
				});
			},
			close: function( id){
				this.closeActiveRowData();
			},
			remove: function( element ){
				
				var row_data	=	element.parent('td').parent('tr'),
					input_data	=	row_data.next('.qm-table-group-input');
				
				var id			=	element.data('id');
				
				if(this.is_remove_validate(id)){
					
					QM_Model().get({
						action: 'quizmaker_remove_ranking',
						tid: meta_boxes_test_script.post_id,
						id: id
					}).then(function(){
						
						row_data.remove();
						input_data.remove();
						
						QM().Message().inform( 'Remove Success!' );
					});
				}
				
				return false;
			},
			updateActiveRowData: function( data ){
				
				var container 	=	$('#qm-table-ranking_data'),
					row_data	=	container.find('.row_data.active');
					
				row_data.find('.min_data').html(data.min);
				row_data.find('.max_data').html(data.max);
				row_data.find('.name_data').html(data.name);
				row_data.find('.certificate_data').html(data.certificate);
			},
			closeActiveRowData: function(){
				
				var container = $('#qm-table-ranking_data'),
					row_data	=	container.find('.row_data.active'),
					input_data	=	row_data.next('.qm-table-group-input.active');
					
					row_data.removeClass('active');
					input_data.removeClass('active');
				
			},
			is_save_validate: function( data ){
				
				if(data == '' || typeof data == 'undefined') { return false; }
				
				if((parseInt(data.min) > parseInt(data.max)) || (data.name == '')){ return false; }
				
				return true;
			},
			is_remove_validate: function( data ){
				
				return true;
			}
		};
	};

	var table_data_results_box = new AWS_Table_Data($('.results-box'), { delay: 2 });
		table_data_results_box.init();


	$('.results-box').on('aws-table-data-after-loading-data', function(){

		var table_data = $(this);

		$(this).find('.qm-result-view-all').click(function(e){
		
			var current_tr	=	$(this).parents('tr');
			
			table_data.find('.tr-view-all').remove();
			
			if(!current_tr.hasClass('show-all-view')){
				
				QM_Model().get({ 
					action: 'quizmaker_get_test_all_results', 
					tid: meta_boxes_test_script.post_id, 
					uid: $(this).data('uid')
				}).then(function(response){
					
					var wrap	=	$('<tr class="tr-view-all"><td colspan="8"><table cellpadding="0" cellspacing="0" class="widefat wp-list-table"><tbody></tbody></table></td></tr>');
			
					current_tr.after($(response.html));
				
					$('.results-box .show-all-view').removeClass('show-all-view');
				
					current_tr.addClass('show-all-view');
				
				});
				
			}else{
				
				current_tr.removeClass('show-all-view');
			}

			e.preventDefault();
		});
	});


	$('#qm-remove-test-results').click(function(e){
		
		var btn		=	$(this),
			loading	=	$('.spinner-results-action');
			
			btn.addClass('disabled');
			loading.css('visibility', 'visible');
			
			QM_Model().get({ 
				action: 'quizmaker_remove_test_results', 
				tid: meta_boxes_test_script.post_id
			}).then(function(response){

				$('.results-box').trigger('aws-table-data-no-data');
				
				btn.removeClass('disabled');
				loading.css('visibility', 'hidden');
			});
		
		e.preventDefault();
	});
	
	FixedQuestions().init();
	RankingData().init();
	AssignUsers().init();

	setTimeout(function(){
		
		QM_Verify().init();

	}, 500);
});