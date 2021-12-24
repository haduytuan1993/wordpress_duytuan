jQuery( function( $ ) {

	$(document).ready(function() {

		setTimeout(function(){

			QM_Verify().init();

		}, 2000);
		
		var app = new Vue({
			el: '#importing-questions',
			data: {
				status: 1,
				items: [],
				post_data: false
			},
			methods: {
				send_data: function( fieldName, fileList ){

					// handle file changes
			        const formData = new FormData();

			        if (!fileList.length) return;
			        
			        formData.append(fieldName, fileList[0], fileList[0].name);

			        formData.append('action', 'quizmaker_importing_questions');
			        formData.append('security', importing_questions.security);

			        this.post_data = formData;

				},
				start_import: function( event ){

					event.preventDefault();

					var _self = this;

			        this.status = 2;

					axios.post( quizmaker.ajax_url, this.post_data ).then(function(response){

						if( typeof response.data != 'undefined' && response.data.length > 0 ){

							_self.items = response.data;

							_self.status = 3;

						}else{

							_self.status = 4;
						}
						
					})
					.catch(function(response){
						
					});
				}
			}
		});

	});

});