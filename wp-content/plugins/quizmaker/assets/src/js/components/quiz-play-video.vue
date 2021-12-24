<template>
	<div class="quiz-play-item quiz-play-video">
		<v-container fluid>
			<v-layout row wrap play-content>
				<v-flex sm12 class="content">
					
					<template v-for="(item, i) in videos">
						
						<div v-html="item.code" :key="index" v-if="i == index"></div>

					</template>
					
					<v-btn @click="next" round class="play-item-btn-continue-1">Continue</v-btn>
				</v-flex>
			</v-layout>
		</v-container>
	</div>
</template>
<script>

export default {
	props:{
		videos: {
			type: [Array]
		}
	},
	data: function(){

		return {
			loading: false,
			index: 0
		};
	},
	mounted: function(){

		if( this.videos.length > 0 ){
			jQuery(this.$el).fitVids();
		}
	},
	watch: {
		videos: function(){

			// jQuery(this.$el).fitVids();
		}
	},
	methods: {
		next: function(){

			if( this.index < this.videos.length - 1 ) {

				var _self = this;
				
				this.index++;

				setTimeout(function(){
					jQuery(_self.$el).fitVids();
				});
				
			}else{

				this.finish();
			}
		},
		finish: function(){

			this.$emit('finish');
		}
	}
}
</script>