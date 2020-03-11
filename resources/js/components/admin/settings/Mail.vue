<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header pb-2">
            <h4 class="card-title">Mail Settings</h4>
        </div>

        <form @submit.prevent="updateSettings()" data-vv-scope="form-mail">

            <div class="card-body">

                <p>Configure the applications mail settings which are used to send system emails.</p>

                <hr class="my-4">

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Mail Driver</label>
                    <div class="col-lg-9 mb-md-3" :class="{ 'border bg-light p-2': settingsData['mail.driver'] === 'smtp' || settingsData['mail.driver'] === 'mailgun' || settingsData['mail.driver'] === 'sparkpost' }">

                        <form-select name="mail.driver" v-model="settingsData['mail.driver']">
                            <select-option
                                v-for="item in mailDriver"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                                
                                {{ item.label }}

                            </select-option>
                        </form-select>

                        <template v-if="settingsData['mail.driver'] === 'smtp'">
                            <div class="row mt-3">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>SMTP Host Address</label>
                                        <input type="text" name="mail.host" class="form-control" v-model="settingsData['mail.host']">
                                    </div>
                                </div>                        
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>SMTP Host Port</label>
                                        <input type="text" name="mail.port" class="form-control" v-model="settingsData['mail.port']">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Username</label>
                                        <input type="text" class="form-control" name="mail.username" v-model="settingsData['mail.username']">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SMTP Password</label>
                                        <input type="text" class="form-control" name="mail.password" v-model="settingsData['mail.password']">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-if="settingsData['mail.driver'] === 'mailgun'">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Domain</label>
                                        <input type="text" name="services.mailgun.domain" class="form-control" v-model="settingsData['services.mailgun.domain']">
                                    </div>
                                </div>                        
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Secret</label>
                                        <input type="text" name="services.mailgun.secret" class="form-control" v-model="settingsData['services.mailgun.secret']">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-if="settingsData['mail.driver'] === 'sparkpost'">
                            <div class="form-group mt-3">
                                <label>Secret</label>
                                <input type="text" name="services.sparkpost.secret" class="form-control" v-model="settingsData['services.sparkpost.secret']">
                            </div>
                        </template>

                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Support Email</label>
                    <div class="col-lg-9 mb-md-3">
                        <input type="text" name="settings.support_email" class="form-control" v-model="settingsData['settings.support_email']">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Mail From Details</label>
                    <div class="col-lg-9 mb-md-3">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label>From Name</label>
                                <input type="text" class="form-control" name="mail.from.name" v-model="settingsData['mail.from.name']">
                            </div>

                            <div class="col-md-6">
                                <label>From Address</label>
                                <input type="text" class="form-control" name="mail.from.address" v-model="settingsData['mail.from.address']">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Test Mail</label>
                    <div class="col-lg-9">
                        <button-spinner :loading="btnLoadingTestMail" type="button" class="btn btn-sm btn-light" @click="sendTestMail">Send Test Mail</button-spinner>
                        <div class="form-text font-size-sm text-gray-600">Send a test email to <span class="font-weight-semibold text-gray-600">({{ $user.email }})</span> to check if the mail settings have been configured properly</div>
                    </div>
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

            btnLoadingTestMail: false,

			settingsData: {},
        	
        	mailDriver: [
        		{value: "smtp", label: "SMTP Server"},
                {value: "sendmail", label: "PHP SendMail"},
                {value: "mailgun", label: "Mailgun"},
                {value: "sparkpost", label: "SparkPost"},
		    ],

        };
    },

    created() {
    	this.getSettings();
    },

    methods: {
	    getSettings() {
			axios.get('admin/settings')
	        .then(response => {
	        	this.settingsData = response.data;
	        })
	        .catch(error => { });
    	},

        sendTestMail() {
            this.btnLoadingTestMail = true;

            axios.post('admin/settings/send-testmail')
            .then(response => {

                this.btnLoadingTestMail = false;
                this.$alert.success(response.data.message);

            })
            .catch(error => {

                this.btnLoadingTestMail = false;
                this.$alert.error(error.response.data.message);
            
            });
        },

        updateSettings() {
    		this.$validator.validateAll('form-mail').then((success) => {

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

                    this.$backendErrors(error.response.data, 'form-mail');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

	            });

            });
        },

    }
}
</script>