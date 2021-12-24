jQuery( function( $ ) {

	var UserGroups	=	function(){
		
		return {
			init: function(){

				this.table_data = new AWS_Table_Data($('.qm-list-user-groups'), { delay: 1 });
				
				this.events();

				this.table_data.init();
			},
			events: function(){
				var _self	=	this;
				
				$('#add-user-to-group').click(function(e){
					
					var user_ids = $('#input-user-groups').val();
					
					if( user_ids ){

						$.when(_self.table_data.action( 'add', { data: { ids: user_ids } } )).then(function(){
							
							_self.table_data.refresh();
							$('#input-user-groups').val('').trigger('change');
						});
						
					}
					
					e.preventDefault();
				});
				
				$('#remove-user-from-group').click(function(e){
					
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

	UserGroups().init();

});