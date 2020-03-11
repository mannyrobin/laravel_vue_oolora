<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header pb-2">
            <h4 class="card-title">Custom Code Settings</h4>
        </div>

        <form @submit.prevent="updateSettings()" data-vv-scope="form-advance">

            <div class="card-body">

                <p>You can add your own custom styling to override the application default as well as tracking/JavaScript code that will be placed in the header before the closing &lt;/head&gt; tag.</p>

                <hr class="my-4">

                <div class="form-group mb-5">
                    <label>Analytics Code</label>
                    <textarea class="form-control" name="settings.analytics_code" v-model="settingsData['settings.analytics_code']" rows="8"></textarea>
                    <div class="form-text font-size-sm text-muted">Ensure that you wrap your code in the relevant script tags.</div>
                </div>

                <div class="form-group">
                    <label>Custom CSS</label>
                    <textarea class="form-control" name="settings.custom_css" v-model="settingsData['settings.custom_css']" rows="8"></textarea>
                    <div class="form-text font-size-sm text-muted">Do not add the style tag, your code will be wrapped in it automatically.</div>
                </div>

            </div>

            <div class="card-footer border-top text-center">
                <button-spinner :loading="btnLoadingSettings" class="btn btn-primary" @click.prevent="updateSettings()">Save Settings</button-spinner>
            </div>

        </form>

    </div>

</div>
</template>


<script>
export default {
	data() {
        return {
        	btnLoadingSettings: false,

			settingsData: {},
        };
    },

    created () {
    	this.getSettings();
    },

    methods: {
	    getSettings () {
			axios.get('admin/settings')
	        .then(response => {
	        	this.settingsData = response.data;
	        })
	        .catch(error => { });
    	},

        updateSettings () {
    		this.$validator.validateAll('form-advance').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoadingSettings = true;

	    		axios.post('admin/settings', this.settingsData)
	            .then(response => {

	            	this.btnLoadingSettings = false;
                    this.$alert.success(response.data.message);

	            })
	            .catch(error => {

	            	this.btnLoadingSettings = false;

                    this.$backendErrors(error.response.data, 'form-advance');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

	            });

            });
        },

    }
}
</script>