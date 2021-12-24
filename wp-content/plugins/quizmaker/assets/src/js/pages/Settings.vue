<template>

	<div>
		<v-card>
			<v-card-text>
				<v-form>

					<v-text-field
					  v-model="first_name"
					  :label="$t('First Name')"
					  :counter="10"
					  :error-messages="errors.collect('first_name')"
					  v-validate="'required|max:10'"
					  data-vv-name="first_name"
					  required
					></v-text-field>

					<v-text-field
					  v-model="last_name"
					  :label="$t('Last Name')"
					  :counter="10"
					  :error-messages="errors.collect('last_name')"
					  v-validate="'required|max:10'"
					  data-vv-name="last_name"
					  required
					></v-text-field>

					<v-text-field
					  v-model="email"
					  label="Email"
					  :error-messages="errors.collect('email')"
					  v-validate="'required|email'"
					  data-vv-name="email"
					  required
					></v-text-field>

					<v-divider class="mt-3 mb-3"></v-divider>

					<v-text-field
					  v-model="username"
					  :label="$t('User Name')"
					  :counter="10"
					  :error-messages="errors.collect('username')"
					  v-validate="'required|max:10'"
					  data-vv-name="username"
					  required
					></v-text-field>

					<v-text-field
					  v-model="password_1"
					  :label="$t('New Password')"
					  :hint="$t('Leave blank to leave unchanged')" persistent-hint 
					></v-text-field>

					<v-text-field
					  v-model="password_2"
					  :label="$t('Confirm New Password')"
					></v-text-field>

					<v-btn @click="update">{{$t('Update')}}</v-btn>
				</v-form>

				<v-snackbar
			      :timeout="4000"
			      :color="snackbar_color"
			      v-model="snackbar_message"
			    >
			      {{ message_text }}
			    </v-snackbar>
			</v-card-text>
		</v-card>
	</div>

</template>
<script>

import Vue from 'vue';

export default {
	data () {
		return {
			snackbar_color: 'info',
			snackbar_message: false,
			message_text: '',
			loading: false,
			first_name: '',
			last_name: '',
			email: '',
			username: '',
			password_1: '',   
			password_2: ''   
		}
	},
	created: function(){

		var _self = this;

		Vue.http.get('?qm-ajax=account_resource&method=user_profile').then(response => {
			
			response.json().then(function( result ){

				_self.first_name = result.first_name;
				_self.last_name  = result.last_name;
				_self.email  	 = result.email;
				_self.username   = result.username;
			});

		}, response => {
			

		});
	},
	methods: {
		update: function(){

			var _self = this;

			this.message_text = 'Updating...';
			this.snackbar_color = 'info';
			this.snackbar_message = true;

			Vue.http.post('?qm-ajax=account_resource&method=update_user_profile', {
				account_first_name: this.first_name,
				account_last_name: this.last_name,
				account_email: this.email,
				account_username: this.username,
				password_1: this.password_1,
				password_2: this.password_2
			},{
				emulateJSON: true
			}).then(response => {
			
				response.json().then(function( result ){

					_self.message_text = 'Success';
					_self.snackbar_color = 'success';
				});

			}, response => {
					
				_self.snackbar_message = false;

			});

		}
	}
}
</script>