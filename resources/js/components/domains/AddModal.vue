<template>
<modal
    title="Add New Domain"
    submit-text="Add Domain"
    size="small"
    :show.sync="open"        
    :submit-loading="btnLoading"
    @submit="createDomain"
    @close="$emit('update:open', false)">

    <form data-vv-scope="form-add-domain" class="mt-3">
        
        <div class="form-group">
            <label>Domain Name</label>
            <input type="text" name="name" placeholder="example.com" 
                :class="['form-control', { 'is-invalid': errors.has('form-add-domain.name') }]"
                v-validate="{required: true, url: 'require_protocol'}"
                v-model="newDomain.name">
            <div class="invalid-feedback" v-show="errors.has('form-add-domain.name')">{{ errors.first('form-add-domain.name') }}</div>
        </div>

        <div class="alert alert-info">
            <span class="font-weight-semibold">Before adding your custom domain, ensure that it is pointing to our server by following these steps:</span>
            <ol class="mt-2">
                <li class="mb-1">On your domain registrar, create an A RECORD</li>
                <li class="mb-1">Point the a record to the following IP address <span class="font-weight-semibold">{{ $appSettings.serverIpAddress }}</span></li>
                <li>Once you are able to visit your domain and see our home page your domain is fully propagated and can now be used</li>
            </ol>
        </div>

    </form>

</modal>
</template>


<script>
export default {
	name: 'add-domain-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },
    },


    data() {
        return {
            btnLoading: false,

            newDomain: {},
        };
    },

    methods: {

        createDomain() {
            // Set validation custom message
            const dictionary = {
                en: {
                    attributes: {
                        name: 'domain name',
                    }
                }
            };
            
            this.$validator.localize(dictionary);

            this.$validator.validateAll('form-add-domain').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.post('domains', this.newDomain)
                .then(response => {

                	this.btnLoading = false;

                   	this.$emit('update:open', false);

                    this.$resetForm(this.newDomain);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                	this.btnLoading = false;
                	this.$backendErrors(error.response.data, 'form-add-domain');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>