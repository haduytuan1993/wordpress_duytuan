<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Meta_Box_Quiz_Analytics_Data {
	
	public static function output( $post ) {
		global $post;
?>

	<div id="quiz-analytics-metabox" class="reset-vuetify">
		
    <v-app>
      
      <v-data-table v-model="questionSelected"
            v-bind:headers="questionHeaders"
            v-bind:items="questionItems"
            v-bind:pagination.sync="pagination" hide-actions 
          
            class="elevation-1 table-analytics-questions">

                <template slot="headerCell" scope="props">
                  <span>
                    {{ props.header.text }}
                  </span>
                </template>
                <template slot="items" scope="props">
                  <tr :id="props.item.id">
                    <td class="text-xs-left q-title">{{ props.item.content }}</td>
                    <td class="text-xs-left">{{ props.item.corrects }}</td>
                    <td class="text-xs-left">{{ props.item.wrongs }}</td>
                  </tr>
                </template>

            </v-data-table>

    </v-app>
		
	</div>
	<div class="clear"></div>
			
<?php 
	}
	
	public static function save( $post_id, $post ) {
		
		
	}
	
}