<template>

	<v-app>

      <v-navigation-drawer v-model="drawer" fixed light hide-overlay>
          <v-toolbar flat>

            <v-list class="pa-0">
              <v-list-tile avatar>
                <v-list-tile-avatar>
                  <v-btn fab dark small medium color="grey elevation-0"><v-icon color="white">person</v-icon></v-btn>
                </v-list-tile-avatar>
                <v-list-tile-content>
                  <v-list-tile-title class="body-2 grey--text">{{profile.email}}</v-list-tile-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>

          </v-toolbar>

          <v-divider></v-divider>

          <v-list dense class="pt-0">

          	<template v-for="item in menu">

          		<v-list-group v-if="item.items" v-bind:group="item.group">

          			<v-list-tile :to="item.href" slot="item" :title="item.title">

          				<v-list-tile-action>
          					<v-icon>{{ item.icon }}</v-icon>	
          				</v-list-tile-action>

          				<v-list-tile-content>

          					<v-list-tile-title>{{ $t(item.title) }}</v-list-tile-title>

          				</v-list-tile-content>

          			</v-list-tile>

          			<v-list-tile v-for="subItem in item.items" :key="subItem.href" :to="subItem.href">

          				<v-list-tile-action v-if="subItem.icon">
          					<v-icon>{{ subItem.icon }}</v-icon>	
          				</v-list-tile-action>

          				<v-list-tile-content>

          					<v-list-tile-title>{{ $t(subItem.title) }}</v-list-tile-title>

          				</v-list-tile-content>

          			</v-list-tile>

          		</v-list-group>

          		<v-subheader v-else-if="item.header">{{ item.header }}</v-subheader>

          		<v-divider v-else-if="item.divider"></v-divider>

          		<v-list-tile v-else :title="item.title" :to="item.href" :replace="false" ripple v-bind:disabled="item.disabled">

          			<v-list-tile-action><v-icon>{{ item.icon }}</v-icon></v-list-tile-action>
          			<v-list-tile-content>{{ $t(item.title) }}</v-list-tile-content>

          		</v-list-tile>

          	</template>

            </v-list>


      </v-navigation-drawer>

      <v-toolbar fixed dark class="primary">

        <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>
        <v-toolbar-title>{{$t(pageTitle)}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn small class="primary--text white mr-3" :href="home_url">Back to home</v-btn>
      </v-toolbar>

      <main>
        <div class="container fluid">
          <transition mode="out-in">
            <router-view class="view"></router-view>
          </transition>
        </div>
      </main>

  </v-app>

</template>
<script>

import { mapState } from 'vuex'

export default {
	data() {

		return {
      home_url: quizmaker.site_url,
			drawer: false,
      profile: {
        email: quizmaker.profile.email
      }
		}
	},
	computed: {
		...mapState(['menu', 'pageTitle'])
	},
	methods: {
		play: function() {

			console.log('Hello world');
		},
		fetchMenu () {
	      // fetch menu from server
	      // this.$http.get('menu').then(({data}) => this.$store.commit('setMenu', data))
	    }
	},
	created () {

	    this.fetchMenu()	
	}
}
</script>