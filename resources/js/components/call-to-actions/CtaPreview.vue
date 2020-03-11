<template>
<div>

	<template v-if="ctaData.type === 'Popup'">
	   <modal id="popup"
	        :conten-style="ctaData.meta.style_background"
	        :position-center="true"
	        :close-backdrop="false"
	        :hide-footer="true"
	        :show.sync="showCta"
	        @close="close">

	        <div class="text-center pb-4">

	        	<img v-if="ctaData.meta.image" :src="ctaData.meta.image" class="img-fluid" alt="">

				<h2 class="mb-1" :style="ctaData.meta.style_text">{{ ctaData.meta.message.headline }}</h2>
				<p class="lead" :style="ctaData.meta.style_text">{{ ctaData.meta.message.body }}</p>
				<a @click="recordConversion" v-if="ctaData.meta.message.button_text" :href="ctaData.meta.message.button_url" class="btn btn-primary btn-lg mt-2" :style="ctaData.meta.style_button">{{ ctaData.meta.message.button_text }}</a>
			
			</div>

	    </modal>
	</template>


    <template v-if="ctaData.type === 'Scroll Box'">
    	<transition name="fade">
			<div v-if="showCta" id="scroll-box" class="shadow text-center px-3 py-5 m-3 rounded break-text position-absolute" :class="ctaData.meta.position" :style="ctaData.meta.style_background">
				
				<button class="close" :style="ctaData.meta.style_text" @click="close">&times;</button>

				<img v-if="ctaData.meta.image" :src="ctaData.meta.image" class="img-fluid" alt="">

				<h2 class="mb-1" :style="ctaData.meta.style_text">{{ ctaData.meta.message.headline }}</h2>
				<p class="lead" :style="ctaData.meta.style_text">{{ ctaData.meta.message.body }}</p>
				<a @click="recordConversion" v-if="ctaData.meta.message.button_text" :href="ctaData.meta.message.button_url" class="btn btn-primary btn-lg mt-2" :style="ctaData.meta.style_button">{{ ctaData.meta.message.button_text }}</a>

			</div>
		</transition>
	</template>


	<template v-if="ctaData.type === 'Banner'">
		<transition name="fade">
			<div v-if="showCta" id="banner" class="p-3 break-text w-100 position-absolute" :class="ctaData.meta.position" :style="ctaData.meta.style_background">
				
				<button class="close" :style="ctaData.meta.style_text" @click="close">&times;</button>

				<div class="d-flex align-items-center justify-content-center">
					<h3 class="mb-0 mr-4" :style="ctaData.meta.style_text">{{ ctaData.meta.message.headline }}</h3>
					<a @click="recordConversion" v-if="ctaData.meta.message.button_text" :href="ctaData.meta.message.button_url" class="btn btn-primary btn-lg" :style="ctaData.meta.style_button">{{ ctaData.meta.message.button_text }}</a>
				</div>

			</div>
		</transition>
	</template>


</div>
</template>


<script>
export default {
	name: 'cta-preview',

	props: ['ctaId', 'linkId', 'linkUrl', 'genericDemo'],

	data() {
        return {
            showCta: false,

            ctaData: {}
        }
    },

	watch: {
		genericDemo: {
            handler: function(newValue) {
                this.ctaData = this.genericDemo;
				this.showCta = true;
            },
            deep: true
        },

		// watch for when the CTA ID changes and fetch the new data
		ctaId(val) {
			this.showCta = false;
			this.getCta();
		}
	},

	created() {
		this.getCta();

		// Use the data pass on in generic mood
		// else use the ID to get the data
		if ( this.genericDemo ) {
			this.ctaData = this.genericDemo;
			this.showCta = true;
		}
	},

    methods: {
    	getCta() {

    		// Don't fetch the data if it's in generic mood
    		if ( this.genericDemo )
    			return;

            axios.get('cta/preview/' + this.ctaId)
            .then(response => {
                this.ctaData = response.data;

				self = this;
				setTimeout(function() {
					self.showCta = true;
				}, response.data.meta.appear_time+'000');

            })
            .catch(error => { });
    	},

    	recordConversion() {

    		// Do nothing if the link ID props isn't set
    		if ( ! this.linkId )
    			return;

            axios.put('cta/record-conversion/'+ this.linkId)
            .then(response => {

                console.log(response);

            })
            .catch(error => {
                console.log(error.response);
            });
    	},

    	close() {
    		this.showCta = false;

    		// redirect to the target website
    		window.location = this.linkUrl
    	}

    }
}
</script>

<style scoped>
	#banner .close {
		position: absolute; 
		right: 15px; 
		top: 10px;
	}

	.banner-top {
		top: 0;
		-webkit-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
		box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
	}

	.banner-bottom {
		bottom: 0;
		-webkit-box-shadow: 0 0 10px rgba(0,0,0,.15); 
		box-shadow: 0 0 10px rgba(0,0,0,.15); 
	}

	#scroll-box {
		max-width: 400px; 
		width: 400px;
	}

	#scroll-box .close {
		position: absolute; 
		right: 15px; 
		top: 15px;
	}

	.scroll-box-bottom-left {
		bottom: 0;
		left: 0;
	}

	.scroll-box-bottom-right {
		bottom: 0;
		right: 0;
	}

	.scroll-box-top-left {
		top: 0;
		left: 0;
	}

	.scroll-box-top-right {
		top: 0;
		right: 0;
	}
</style>