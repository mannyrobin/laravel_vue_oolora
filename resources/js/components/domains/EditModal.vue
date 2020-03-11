<template>
<modal
    title="Edit Domain"
    submit-text="Update Domain"
    size="small"
    :show.sync="open"        
    :submit-loading="btnLoading"
    @submit="updateDomain"
    @close="$emit('update:open', false)">

    <form data-vv-scope="form-edit-domain" class="mt-3">
        
        <div class="form-group">
            <label>Domain Name</label>
            <input type="text" name="name" placeholder="example" 
                :class="['form-control', { 'is-invalid': errors.has('form-edit-domain.name') }]"
                v-validate="{required: true, url: 'require_protocol'}"
                v-model="editDomain.name">
            <div class="invalid-feedback" v-show="errors.has('form-edit-domain.name')">{{ errors.first('form-edit-domain.name') }}</div>
        </div>

    </form>

</modal>
</template>


<script>
export default {
    name: 'edit-domain-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        editDomain: {
            type: Object,
            default: ''
        },
    },


    data() {
        return {
            btnLoading: false,
        };
    },

    methods: {

        updateDomain() {
            // Set validation custom message
            const dictionary = {
                en: {
                    attributes: {
                        name: 'domain name',
                    }
                }
            };
            
            this.$validator.localize(dictionary);

            this.$validator.validateAll('form-edit-domain').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.put('domains/'+ this.editDomain.id, this.editDomain)
                .then(response => {

                    this.btnLoading = false;

                    this.$emit('update:open', false);

                    this.$resetForm(this.editDomain);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-edit-domain');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>