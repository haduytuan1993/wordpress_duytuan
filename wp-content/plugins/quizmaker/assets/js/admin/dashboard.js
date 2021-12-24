jQuery( function( $ ) {
	
	var table_sessions = $('#dashboard-table-sessions');
	
	$('.stop_doing_session').click(function(e){
		e.preventDefault();
		
		var model	=	new QM_Model(),
			sid		=	parseInt($(this).data('id')),
			row		=	table_sessions.find( '.' + $(this).data('row') );
		
		model.get({ action: 'session_stop_doing', sid: sid }).then(function(response){
			
			row.remove();
			
		});
	});
	
});