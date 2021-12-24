<div class="wrap">
	
	<h1><?php _e('Question Importing', 'quizmaker'); ?></h1>

	<div class="qm-import" id="importing-questions">
		
		<form enctype="multipart/form-data" method="post" v-show="status == 1">
			
			<p><?php _e('Hi there! Upload a CSV file containing questions to import the contents into your question bank. Choose a .csv file to upload, then click "Upload file and import".', 'quizmaker'); ?></p>
			
			<p><a href="http://quizmaker.awstheme.com/document/importing_questions.csv"><?php _e('Click here to download a sample', 'quizmaker'); ?></a></p>

			<p><?php _e('How to using options in csv file:', 'quizmaker'); ?></p>

			<p>
				<ol>
					<li>Type Question column: single or multiple</li>
					<li>Categories column: the id of question categories is separate by ',': 1,2,3</li>
					<li>Order Type column: the id of order type question
						<ul>
							<li>0 : None</li>
							<li>1 : A,B,C,D</li>
							<li>2 : 1,2,3,4</li>
							<li>3 : I,II,III,IV</li>
						</ul>
					</li>
					<li>Correct Answer column: the index of correct answer start from 0</li>
				</ol>
			</p>
			
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="upload">Choose a file from your computer:</label>
						</th>
						<td>
							<input type="file" id="upload" name="question_csv" size="25" v-on:change="send_data($event.target.name, $event.target.files)" accept=".csv">
							<small>Maximum size: 25 MB</small>
						</td>
					</tr>
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" class="button" v-on:click="start_import" value="Upload file and import">
			</p>
		</form>
		
		<div class="msg-importing" v-show="status == 2">Importing......</div>
		<div class="msg-success" v-show="status == 3">Success!</div>
		<div class="msg-error" v-show="status == 4">Error!</div>

		<table class="wp-list-table widefat fixed striped posts" v-if="items.length > 0">
			<thead>
				<tr>
					<td class="manage-column column-index">#</td>
					<td class="manage-column column-title column-primary"><?php _e('Title', 'quizmaker') ?></td>
					<td class="manage-column column-status"><?php _e('Status', 'quizmaker'); ?></td>
				</tr>
			</thead>

			<tbody>
				<template v-for="(item, index) in items">
					
					<tr class="iedit csv-item">
						<td class="index column-index">{{index+1}}</td>
						<td class="title column-title has-row-actions column-primary page-title"><a v-bind:href="item.url">{{item[2]}}</a></td>
						<td class="index column-status"><?php _e('Complete', 'quizmaker') ?></td>
					</tr>

				</template>
			</tbody>
		</table>

	</div>
</div>