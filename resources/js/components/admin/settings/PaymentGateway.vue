<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header pb-2">
            <h4 class="card-title">Payment Gateway Settings</h4>
        </div>

        <form @submit.prevent="updateSettings()" data-vv-scope="form-payment-gateway">

            <div class="card-body">

                <p>Manage and configure the application payment methods for subscription payments.</p>

                <tabs :pills="false" class="mt-4">
             
                    <tab-pane>
                        <span slot="title"><i class="fab fa-paypal"></i> PayPal</span>

                        <div class="custom-control custom-switch mb-2 mt-4">
                            <input type="checkbox" class="custom-control-input" id="enable-paypal" name="services.paypal.enable" :true-value="1" v-model="settingsData['services.paypal.enable']">
                            <label class="custom-control-label" for="enable-paypal">Enable PayPal</label>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="paypal-mood" name="services.paypal.settings.mode" :true-value="'sandbox'" v-model="settingsData['services.paypal.settings.mode']">
                            <label class="custom-control-label" for="paypal-mood">Enable Sandbox Mode</label>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-4">

                                <div class="form-group mb-0">
                                    <label>Webhook ID</label>
                                    <input type="text" class="form-control" name="services.paypal.webhook_id" v-model="settingsData['services.paypal.webhook_id']">
                                    <div class="form-text font-size-sm text-muted">Enter the ID for the webhook you have created</div>
                                </div>

                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-6 pr-md-4">

                                <div class="font-weight-semibold mb-2">Live API Credentials</div>

                                <div class="form-group mb-4">
                                    <label>Client ID</label>
                                    <input type="text" class="form-control" name="services.paypal.live_client_id" v-model="settingsData['services.paypal.live_client_id']">
                                </div>

                                <div class="form-group">
                                    <label>Secret</label>
                                    <input type="text" class="form-control" name="services.paypal.live_secret" v-model="settingsData['services.paypal.live_secret']">
                                </div>

                            </div>
                            <div class="col-md-6 pl-md-4">

                                <div class="font-weight-semibold mb-2">Sandbox API Credentials</div>

                                <div class="form-group mb-4">
                                    <label>Client ID</label>
                                    <input type="text" class="form-control" name="services.paypal.sandbox_client_id" v-model="settingsData['services.paypal.sandbox_client_id']">
                                </div>

                                <div class="form-group">
                                    <label>Secret</label>
                                    <input type="text" class="form-control" name="services.paypal.sandbox_secret" v-model="settingsData['services.paypal.sandbox_secret']">
                                </div>

                            </div>
                        </div>


                        <div class="mt-5 text-gray-600">
                            <span class="font-weight-semibold"><i class="far fa-question-circle"></i> NB:</span> if you need help to create or locate your PayPal API credentials <a href="https://www.paypal.com/us/smarthelp/article/How-do-I-create-REST-API-credentials-ts1949" target="_blank">Visit Here &rarr;</a>
                        </div>

                    </tab-pane>


                    <tab-pane>
                        <span slot="title"><i class="fab fa-cc-stripe"></i> Stripe</span>
                        
                        <div class="custom-control  custom-switch mt-4 mb-4">
                            <input type="checkbox" class="custom-control-input" id="enable-stripe" name="services.stripe.enable" :true-value="1" v-model="settingsData['services.stripe.enable']">
                            <label class="custom-control-label" for="enable-stripe">Enable Stripe</label>
                        </div>

                        <div class="form-group">
                            <label>Publishable Key</label>
                            <input type="text" class="form-control" name="services.stripe.key" v-model="settingsData['services.stripe.key']">
                            <div class="form-text font-size-sm text-muted">Enter your Stripe test publishable keys for test mode and live keys for live mode</div>
                        </div>

                        <div class="form-group mb-4">
                            <label>Secret Key</label>
                            <input type="text" class="form-control" name="services.stripe.secret" v-model="settingsData['services.stripe.secret']">
                            <div class="form-text font-size-sm text-muted">Enter your Stripe test secret keys for test mode and live keys for live mode</div>
                        </div>

                        <div class="mt-5 text-gray-600">
                            <span class="font-weight-semibold"><i class="far fa-question-circle"></i> NB:</span> If you need help locating your Stripe API keys <a href="https://support.stripe.com/questions/locate-api-keys" target="_blank">Visit Here &rarr;</a>
                        </div>

                    </tab-pane>

                </tabs>

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

            settingsData: {}
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
            this.$validator.validateAll('form-payment-gateway').then((success) => {

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
                    this.$alert.error(error.response.data.message);

                });

            });
        },

    }
}
</script>