<template>
<div class="container-fluid slideout-panel p-0 h-100 bg-light">
    <div class="row no-gutters h-100">

        <div class="col-md-4 h-100" style="z-index: 1100;">

			<div class="panel-wrap has-header has-footer-lg border-right">

			    <div class="panel-header px-3 bg-white border-bottom d-flex justify-content-between align-items-center">
			    	<h6 class="mb-0">Create Call to Action</h6>
			        <button type="button" class="close" @click="$router.go(-1)">&times;</button>
			    </div>

			    <div class="panel-body py-3 px-4 bg-white">

			 		<placeholder v-if="showPlaceholder" v-for="n in 5" :key="n" class="mt-3 mb-5" heading :heading-image="false"></placeholder>

                    <form v-if="!showPlaceholder" data-vv-scope="form_save_cta">
                        
                        <div class="form-group mb-4">
                            <label>Name</label>
                            <input type="text" name="name"
                            	placeholder="My Awesome CTA" 
                                :class="[{ 'is-invalid': errors.has('form_save_cta.name') }, 'form-control']"
                                v-validate="'required|max:40'"
                                v-model="ctaData.name">

                            <div class="invalid-feedback" v-show="errors.has('form_save_cta.name')">{{ errors.first('form_save_cta.name') }}</div>
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <form-select name="type"
                            	placeholder="Please Select"
                            	v-model="ctaData.type"
                            	v-validate="'required'" 
                            	:class="[{ 'is-invalid': errors.has('form_save_cta.type') }]">
                                <select-option 
                                    v-for="item in ctaTypesOption" 
                                    :key="item.value" 
                                    :label="item.label" 
                                    :value="item.value">

                                    {{ item.label }}

                                </select-option>
                            </form-select>
                            <span class="form-text text-danger" v-show="errors.has('form_save_cta.type')">{{ errors.first('form_save_cta.type') }}</span>
                        </div>

                        <hr class="my-5">

                        <div class="font-weight-semibold font-size-lg mt-4">Message Customization</div>
                        <p class="text-gray-600 mb-3">Customize your call to action message for maximum conversion</p>


                        <div class="form-group mb-4">
                            <label>Message Headline</label>
                            <input type="text" name="message_headline" class="form-control" v-model="ctaData.meta.message.headline">
						</div>

                        <div v-if="ctaData.type != 'Banner'" class="form-group mb-4">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="5" v-model="ctaData.meta.message.body"></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label>Button Text</label>
                            <input type="text" name="button_text" class="form-control" v-model="ctaData.meta.message.button_text">	
                            <div class="form-text font-size-sm text-muted">Leave empty to disable the CTA button</div>
						</div>

                        <div class="form-group mb-4">
                            <label>Button URL</label>
                            <input type="text" name="button_text" class="form-control" v-model="ctaData.meta.message.button_url">
						</div>


                        <hr class="my-5">

                        <div class="font-weight-semibold font-size-lg mt-4">Design Customization</div>
                        <p class="text-gray-600 mb-3">Customize the look and feel of your call to action</p>


                        <div class="d-flex align-content-between align-items-center">
            
                            <file-upload
                                action="/api/cta/upload-image"
                                :data="{'current_file': ctaData.meta.image}"
                                :show-file-list="false"
                                :on-success="uploadSuccess"
                                :before-upload="uploadBefore"
                                :on-error="uploadError">

                                <button type="button" class="btn btn-light btn-sm">Add a display image</button>
                            </file-upload>

                            <button type="button" class="btn btn-link text-danger btn-sm" @click="removeImage">Remove Image</button>
                        
                        </div>

                        <div class="form-text font-size-sm text-muted mb-3">Recommended size 50x200 png/jpg and less than 2MB</div>


                        <div class="row">
                        	<div class="col">

		                        <div class="form-group mb-4">
		                            <label>Background Color</label>
		                            <input type="color" name="background_color" class="form-control border-0 p-0" v-model="ctaData.meta.style_background.background">
								</div>

							</div>
							<div class="col">

		                        <div class="form-group mb-4">
		                            <label>Text Color</label>
		                            <input type="color" name="text_color" class="form-control border-0 p-0" v-model="ctaData.meta.style_text.color">
								</div>

							</div>
						</div>

                        <div class="row">
                        	<div class="col">

		                        <div class="form-group mb-4">
		                            <label>Button Background Color</label>
		                            <input type="color" name="button_background_color" class="form-control border-0 p-0" v-model="ctaData.meta.style_button.background">
								</div>

							</div>
							<div class="col">

		                        <div class="form-group mb-4">
		                            <label>Button Text Color</label>
		                            <input type="color" name="button_text_color" class="form-control border-0 p-0" v-model="ctaData.meta.style_button.color">
								</div>

							</div>
						</div>

                        <div class="row">
                        	<div class="col" v-if="ctaData.type != 'Popup'">

		                        <div class="form-group">
		                            <label>Position</label>
		                            <form-select name="position"
		                            	v-model="ctaData.meta.position">
		                                <select-option 
		                                    v-for="item in positionOption" 
		                                    :key="item.value" 
		                                    :label="item.label" 
		                                    :value="item.value">

		                                    {{ item.label }}

		                                </select-option>
		                            </form-select>
		                        </div>

							</div>
							<div class="col">

		                        <div class="form-group mb-4">
		                            <label>Appear Time (seconds)</label>
		                            <input type="number" name="button_text_color" class="form-control" v-model="ctaData.meta.appear_time">
								</div>

							</div>
						</div>

                    </form>

			    </div>

			    <div class="panel-footer">
			    	<div class="footer-lg bg-white border-top d-flex justify-content-center align-items-center">
				    	<button-spinner :loading="btnLoadingSave" class="btn-primary" @click="updateCta">Save Call to Action</button-spinner>
				    </div>
			    </div>

			</div>

        </div>
        <div class="col-md-8 h-100">

        	<cta-preview :generic-demo="ctaData" :demo-image="demoImage"></cta-preview>

        </div>

    </div>

</div>
</template>


<script>
export default {
    data() {
        return {
            demoImage: '',

        	showPlaceholder: false,

        	btnLoadingSave: false,

        	ctaData: {
        		type: '',

        		meta: {
        			position: 'scroll-box-bottom-right',

                    image: this.$appSettings.appUrl + '/storage/cta-graphics/default.png',

        			appear_time: 3,

        			message: {
        				headline: 'Your headline goes here',
        				body: 'Your message goes here',
        				button_text: 'Click Me Now',
        				button_url: 'http://example.com'
        			},

        			style_background: {
        				background: '#ffffff',
        			},

        			style_text: {
        				color: '#606F7B',
        			},

        			style_button: {
        				background: '#FF7F45',
        				color: '#ffffff'
        			}
        		}
        	},

        	ctaTypesOption: [
                {value: 'Scroll Box', label: 'Scroll Box'},
                {value: 'Popup', label: 'Popup'}, 
                {value: 'Banner', label: 'Banner'}, 
        	],

        	positionOption: [
                {value: 'scroll-box-bottom-right', label: 'Bottom Right'},
                {value: 'scroll-box-bottom-left', label: 'Bottom Left'},
                {value: 'scroll-box-top-right', label: 'Top Right'},
                {value: 'scroll-box-top-left', label: 'Top Left'},
        	]

        };
    },

	watch: {
		'ctaData.type'(val) {
			// Watch for when the CTA type changes
			// and set the position options
	    	if ( val === 'Scroll Box' ) {
	    		this.ctaData.meta.position = 'scroll-box-bottom-right',

	    		this.positionOption = [
	                {value: 'scroll-box-bottom-right', label: 'Bottom Right'},
	                {value: 'scroll-box-bottom-left', label: 'Bottom Left'},
	                {value: 'scroll-box-top-right', label: 'Top Right'},
                	{value: 'scroll-box-top-left', label: 'Top Left'},
	        	];
	    	} 

	    	if( val === 'Banner' ) {
	    		this.ctaData.meta.position = 'banner-bottom',

	    		this.positionOption = [
	                {value: 'banner-top', label: 'Top'},
	                {value: 'banner-bottom', label: 'Bottom'},
	        	];
	    	}
		}
	},

	created() {
		// If we have CTA Id fetch the data for it
		if ( this.$route.params.ctaId )
			this.getCta();
	},

    methods: {
    	getCta() {
            axios.get('cta/' + this.$route.params.ctaId)
            .then(response => {
                this.ctaData = response.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
    	},

    	updateCta() {
            this.$validator.validateAll('form_save_cta').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoadingSave = true;

                axios.post('cta', this.ctaData)
                .then(response => {

                    this.btnLoadingSave = false;
                	this.$alert.success(response.data.message);

                    // Redirect to cta page
                    this.$router.push('/call-to-actions');
                })
                .catch(error => {

                    this.btnLoadingSave = false;
                    this.$backendErrors(error.response.data, 'form_save_cta');

                    // Show error message if there is any
                    if ( error.response.data.message )
                		this.$alert.error(error.response.data.message);

                });

            });
    	},

        uploadBefore(file) {
            const lessThanMS = file.size / 1024 / 1024 < 2;
            let supportedFormat = false;
            
            if ( file.type === 'image/jpeg' || file.type === 'image/png' )
                supportedFormat = true;

            if ( ! supportedFormat )
                this.$alert.error('Only jpg/png formats are supported');

            if ( ! lessThanMS )
                this.$alert.error('The file size can not exceed 2MB');

            return supportedFormat && lessThanMS;
        },

        uploadSuccess(result) {
            this.$alert.success(result.message);

            this.ctaData.meta.image = result.image_url;
        },

        uploadError(err, file, fileList) {
            let response = JSON.parse(err.message);
            let errorMessage = response.errors ? this.$alert.error(response.errors.file[0]) : response.message;
            
            this.$alert.error(errorMessage);
        },

        removeImage() {
            axios.post('cta/remove-image', { current_file: this.ctaData.meta.image})
            .then(response => {
                this.ctaData.meta.image = '';
                this.$alert.success(response.data.message);
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        }
    }
}
</script>


<style scoped>
	>>> #popup .modal-dialog {
		left: 18% !important;
	}
</style>