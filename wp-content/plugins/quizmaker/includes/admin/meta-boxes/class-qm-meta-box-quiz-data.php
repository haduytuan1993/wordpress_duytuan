<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Meta_Box_Quiz_Data {
	
	public static function output( $post ) {
		global $post;

?>

	<div id="quiz-composer" class="reset-vuetify">
		
		<v-app> 
      
      <v-tabs dark lazy>
        <v-tabs-bar>
          <v-tabs-item href="#composer"><?php _e('Composer', 'quizmaker'); ?></v-tabs-item>
          <v-tabs-item href="#settings"><?php _e('Settings', 'quizmaker'); ?></v-tabs-item>
          <v-tabs-item href="#marketing_app"><?php _e('Marketing', 'quizmaker'); ?></v-tabs-item>
          <v-tabs-slider color="yellow"></v-tabs-slider>
        </v-tabs-bar>

        <v-tabs-items>
          <v-tabs-content id="composer">

            <v-layout class="mt-3">

              <v-btn dark small primary class="white--text elevation-1" @click.stop="selectTypeQuestion"><v-icon left>add</v-icon>Add Question</v-btn>
            
              <v-btn dark small class="red white--text elevation-1" @click="removes"><v-icon left>delete</v-icon>Delete</v-btn>

              <v-btn small class="elevation-1" @click.stop="preview"><v-icon left>visibility</v-icon>Preview</v-btn>

            </v-layout>

            <v-toolbar dense class="elevation-1 mt-3">

               <v-toolbar-title>
                  List Questions
               </v-toolbar-title>
               <v-spacer></v-spacer>
                
               <v-toolbar-items>
                 
               </v-toolbar-items>
            </v-toolbar>

      			<v-data-table v-model="questionSelected"
            v-bind:headers="questionHeaders"
            v-bind:items="questionItems"
            select-all="true"
            v-bind:pagination.sync="pagination" hide-actions 
            selected-key="id"
            class="elevation-1 table-questions">

                <template slot="headerCell" scope="props">
                  <span>
                    {{ props.header.text }}
                  </span>
                </template>
                <template slot="items" scope="props">
                  <tr :id="props.item.id">
                    <td>
                      <v-checkbox
                        primary
                        hide-details
                        v-model="props.selected"
                      ></v-checkbox>
                    </td>
                    <td class="text-xs-left q-title"><a @click="editQuizItem(props.item.id)">{{ props.item.content }}</a></td>
                    <td class="text-xs-left">{{ props.item.type_name }}</td>
                    <td class="text-xs-left">{{ props.item.media }}</td>

                    <td class="text-xs-left pr-0 actions">
                          <v-btn fab small @click.stop="double(props.item.id)" class="green elevation-0 ml-0" slot="activator"><v-icon class="white--text">repeat</v-icon></v-btn>
                       
                          <v-btn fab small @click.stop="remove([props.item.id])" class="red elevation-0 ml-0 mr-0"><v-icon class="white--text">close</v-icon></v-btn>
                    </td>
                  </tr>
                </template>

            </v-data-table>

          </v-tabs-content>
          <v-tabs-content id="settings" class="quiz-settings">
            
            <v-card>
              <v-card-text>
                <v-layout row wrap>
                  <v-flex sm-6>
                    
                    <v-layout row wrap v-if="false">
                      <v-flex xs12>
                        <v-dialog persistent v-model="startDateDialog" lazy full-width>
                          <v-text-field
                            slot="activator"
                            label="<?php _e('Start Date', 'quizmaker'); ?>"
                            v-model="settings.start_date"
                            prepend-icon="event"
                            readonly
                          ></v-text-field>
                          <v-date-picker v-model="settings.start_date" scrollable actions>
                            <template scope="{ save, cancel }">
                              <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn flat color="primary" @click="cancel">Cancel</v-btn>
                                <v-btn flat color="primary" @click="save">OK</v-btn>
                              </v-card-actions>
                            </template>
                          </v-date-picker>
                        </v-dialog>

                      </v-flex>
                    </v-layout>

                    <v-layout row wrap v-if="false">
                     
                      <v-flex xs12>
                        <v-dialog persistent v-model="endDateDialog" lazy full-width>
                          <v-text-field
                            slot="activator"
                            label="<?php _e('End Date', 'quizmaker'); ?>"
                            v-model="settings.end_date"
                            prepend-icon="event" 
                            readonly
                          ></v-text-field>
                          <v-date-picker v-model="settings.end_date" scrollable actions>
                            <template scope="{ save, cancel }">
                              <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn flat color="primary" @click="cancel">Cancel</v-btn>
                                <v-btn flat color="primary" @click="save">OK</v-btn>
                              </v-card-actions>
                            </template>
                          </v-date-picker>
                        </v-dialog>

                      </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                      <v-flex xs12>

                        <v-switch v-model="settings.is_random" label="Question Random"></v-switch>

                      </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                      <v-flex xs12>

                        <v-switch v-model="settings.is_answer" label="Show Correct Answers"></v-switch>

                      </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                      <v-flex xs12>

                        <v-switch v-model="settings.is_share_for_view_result" label="Share for view result"></v-switch>

                      </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                      <v-flex xs12>
                        
                        <v-switch v-model="settings.is_share_for_plus_points" label="Share for plus point" hide-details></v-switch>
                        
                        <v-text-field v-show="settings.is_share_for_plus_points" v-model="settings.plus_points_when_share" label="Hint Text" single-line prepend-icon="info" class="mt-0 pt-0"></v-text-field>

                      </v-flex>
                    </v-layout>
                    
                   
                  </v-flex>
                  <v-flex sm6>
                    <v-layout row wrap>
                      <v-flex xs12>
                        <label class="setting-label">Cover Image</label>
                        <wpmedia :value.sync="settings.cover_image" size="medium" class="setting-image"></wpmedia>
                      </v-flex>
                    </v-layout>
                  </v-flex>
                </v-layout>
              </v-card-text>

              <v-card-actions>
                <v-btn small primary @click="updateSettings"><?php _e('Update Settings', 'quizmaker'); ?></v-btn>
              </v-card-actions>
            </v-card>
          </v-tabs-content>

          <v-tabs-content id="marketing_app" class="quiz-settings">
            
            <v-card>
              <v-card-text>
                
                <v-expansion-panel>

                  <v-expansion-panel-content v-if="marketing_mailchimp">
                    <div slot="header">Mailchimp</div>
                    <v-card>
                      <v-card-text class="grey lighten-3">
                        
                        <v-list two-line subheader>

                          <v-subheader><?php _e('Lists', 'quizmaker') ?></v-subheader>
                          
                          <v-list-tile avatar v-for="(list, i) in marketing_mailchimp.lists" :key="list.id">
                            <v-list-tile-action>
                              <v-checkbox v-model="marketing.mailchimp.lists" :value="list.id"></v-checkbox>
                            </v-list-tile-action>
                            <v-list-tile-content>
                              <v-list-tile-title>{{list.name}}</v-list-tile-title>
                              <v-list-tile-sub-title><?php _e('Members: ') ?>{{list.stats.member_count}}</v-list-tile-sub-title>
                            </v-list-tile-content>
                          </v-list-tile>
                        </v-list>

                      </v-card-text>
                    </v-card>
                  </v-expansion-panel-content>

                  <v-expansion-panel-content>
                    <div slot="header">GetResponse</div>
                    <v-card>
                      <v-card-text class="grey lighten-3">
                        
                        <v-list one-line subheader>

                          <v-subheader><?php _e('Lists', 'quizmaker') ?></v-subheader>
                          
                          <v-list-tile avatar v-for="(list2, i) in marketing_getresponse.lists" :key="list2.campaignId">
                            <v-list-tile-action>
                              <v-checkbox v-model="marketing.getresponse.lists" :value="list2.campaignId"></v-checkbox>
                            </v-list-tile-action>
                            <v-list-tile-content>
                              <v-list-tile-title>{{list2.name}}</v-list-tile-title>
                            </v-list-tile-content>
                          </v-list-tile>
                        </v-list>

                      </v-card-text>
                    </v-card>
                  </v-expansion-panel-content>

                </v-expansion-panel>


                
                  

              </v-card-text>
              <v-card-actions>

                <v-btn small primary @click="updateMarketing"><?php _e('Update Marketing', 'quizmaker'); ?></v-btn>

              </v-card-actions>
            </v-card>
          </v-tabs-content>

          <v-tabs-content id="form">
            
            <v-layout row wrap class="mt-3">
              

              <select2 class="smt-1 w-2" v-model="formData.type" :options="formTypes"></select2>

              <v-btn dark small primary class="white--text elevation-1" @click.stop="showDialogForm"><v-icon left>add</v-icon>Add</v-btn>
            
              <v-btn dark small class="red white--text elevation-1" @click=""><v-icon left>delete</v-icon>Delete</v-btn>

              <v-btn small class="elevation-1" @click.stop="showDialogForm"><v-icon left>settings</v-icon><?php _e('Settings', 'quizmaker'); ?></v-btn>

            </v-layout>
            
            <v-layout row wrap class="mt-3">
              <v-flex sm12>
                    <v-data-table
                v-bind:headers="formHeaders"
                v-bind:items="formItems" hide-actions 
                class="elevation-0 table-forms">

                    <template slot="headerCell" scope="props">
                      <span>
                        {{ props.header.text }}
                      </span>
                    </template>
                    <template slot="items" scope="props">
                      <tr :id="props.item.id">
                        <td class="text-xs-left q-title"><a @click="editQuizItem(props.item.id)">{{ props.item.title }}</a></td>

                        <td class="text-xs-left">{{ props.item.type }}</td>

                        <td class="text-xs-left pr-0 actions">
                              <v-btn fab small @click.stop="double(props.item.id)" class="green elevation-0 ml-0" slot="activator"><v-icon class="white--text">repeat</v-icon></v-btn>
                           
                              <v-btn fab small @click.stop="remove([props.item.id])" class="red elevation-0 ml-0 mr-0"><v-icon class="white--text">close</v-icon></v-btn>
                        </td>
                      </tr>
                    </template>

                </v-data-table>
              </v-flex>
              <v-flex sm12>
                
              </v-flex>
            </v-layout>
                
      
          </v-tabs-content>
        </v-tabs-items>
      </v-tabs>

      <v-dialog v-model="dialogChooseTypeQuestion" width="90%" :overlay="false">
        <v-card>
          <v-card-title class="headline">Select Question Type and Continue</v-card-title>
          <v-card-text>

            <v-layout row wrap>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0">Text</h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(1)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0">Image</h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(2)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
              <v-flex sm3>
                
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0">Scale</h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(3)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>

              </v-flex>
              <v-flex sm3>
                
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0"><?php _e('Sort', 'quizmaker'); ?></h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(4)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>

              </v-flex>
            </v-layout>

            <v-layout row wrap>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0"><?php _e('Guess Question', 'quizmaker'); ?></h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(5)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0"><?php _e('Keyword', 'quizmaker'); ?></h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(6)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0"><?php _e('Item Match', 'quizmaker'); ?></h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(7)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
              <v-flex sm3>
                <div class="pt-2 pl-2 pb-2 pr-2">
                  <v-card>
                    
                    <v-card-title primary-title>
                      <div>
                        <h3 class="headline mb-0"><?php _e('Group Match', 'quizmaker'); ?></h3>
                      </div>
                    </v-card-title>
                    <v-card-actions>
                      <v-btn block primary @click.stop="newQuizItem(8)">Select</v-btn>
                    </v-card-actions>
                  </v-card>
                </div>
              </v-flex>
            </v-layout>

          </v-card-text>
          
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green darken-1" flat @click.stop="dialogChooseTypeQuestion = false">Cancel</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogNewQuestion" fullscreen :overlay=false>
          
          <v-card>
            <v-toolbar dark class="primary">
              <v-btn icon @click.native="dialogNewQuestion = false" dark>
                <v-icon>close</v-icon>
              </v-btn>
              <v-toolbar-title>New Question</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-toolbar-items>
                
                <v-btn dark flat @click.stop="save">Save</v-btn>
              </v-toolbar-items>
            </v-toolbar>

            <template>
              
              <quiz-text-question v-bind:question="newQuestion" v-if="dialogNewQuestion && selectedTypeQuestion == 1" :order="questionItems.length"></quiz-text-question>

              <quiz-image-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 2"></quiz-image-question>

              <quiz-scale-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 3"></quiz-scale-question>

              <quiz-sort-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 4"></quiz-sort-question>
              
              <quiz-guess-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 5"></quiz-guess-question>

              <quiz-keyword-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 6"></quiz-keyword-question>

              <quiz-itemmatch-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 7"></quiz-itemmatch-question>

              <quiz-groupmatch-question v-bind:question="newQuestion" :order="questionItems.length" v-if="dialogNewQuestion && selectedTypeQuestion == 8"></quiz-groupmatch-question>
              
            </template>
            
          </v-card>

      </v-dialog>
      
      <v-dialog v-model="dialogEditQuestion" fullscreen :overlay=false>
          
          <v-card>
            <v-toolbar dark class="primary">
              <v-btn icon @click.stop="closeDialogEditQuestion" dark>
                <v-icon>close</v-icon>
              </v-btn>
              <v-toolbar-title>Edit Question</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-toolbar-items>
                
                <v-btn dark flat @click.stop="update">Update</v-btn>
              </v-toolbar-items>
            </v-toolbar>

            <template>
              
              <quiz-text-question v-if="dialogEditQuestion && selectedTypeQuestion == 1" :question="editQuestion" v-on:change="update"></quiz-text-question>
              
              <quiz-image-question v-if="dialogEditQuestion && selectedTypeQuestion == 2" v-bind:question="editQuestion"></quiz-image-question>

              <quiz-scale-question v-if="dialogEditQuestion && selectedTypeQuestion == 3" v-bind:question="editQuestion"></quiz-scale-question>
              
              <quiz-sort-question v-if="dialogEditQuestion && selectedTypeQuestion == 4" v-bind:question="editQuestion"></quiz-sort-question>

              <quiz-guess-question v-if="dialogEditQuestion && selectedTypeQuestion == 5" v-bind:question="editQuestion"></quiz-guess-question>

              <quiz-keyword-question v-if="dialogEditQuestion && selectedTypeQuestion == 6" v-bind:question="editQuestion"></quiz-keyword-question>

              <quiz-itemmatch-question v-if="dialogEditQuestion && selectedTypeQuestion == 7" v-bind:question="editQuestion"></quiz-itemmatch-question>

              <quiz-groupmatch-question v-if="dialogEditQuestion && selectedTypeQuestion == 8" v-bind:question="editQuestion"></quiz-groupmatch-question>
              
            </template>
            
          </v-card>

      </v-dialog>

      <v-dialog v-model="dialogPreview" width="90%">
        <v-card>
          <v-card-title class="headline">Preview</v-card-title>
          <v-card-text>
            
            <quiz-player v-if="dialogPreview" :sandbox="true" :id="<?php echo $post->ID; ?>"></quiz-player>

          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green darken-1" flat @click.stop="dialogPreview = false">Close</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogForm" scrollable width="50%">
        <v-card>
          <v-card-title class="headline">Form</v-card-title>
          <v-divider></v-divider>
          <v-card-text style="height: 300px;">
            
            <form-field :value="formData"></form-field>

          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn flat @click.stop="hideDialogForm">Cancel</v-btn>
            <v-btn color="green darken-1" flat @click.stop="saveForm">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      
      <v-snackbar :bottom="true" v-model="loading" :timeout="10000000000" color="primary">
        <v-progress-circular indeterminate color="white" class="snackbar-loading"></v-progress-circular>
      </v-snackbar>

      <v-snackbar :bottom="true" v-model="message.enable" :color="message.type">
        {{message.content}}
        <v-btn dark flat @click.native="message.enable = false">Close</v-btn>
      </v-snackbar>
			
		</v-app>
		
	</div>
	<div class="clear"></div>
			
<?php 
	}
	
	public static function save( $post_id, $post ) {
		
		
	}
	
}