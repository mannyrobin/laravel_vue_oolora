<template>
<modal
    title="Create Campaign"
    submitText="Save Campaign"
    size="small"
    :show.sync="open"
    :submit-loading="btnLoading"
    @submit="createCampaign"
    @close="$emit('update:open', false)">

    <form @submit.prevent="createCampaign" class="mt-3" data-vv-scope="form_create_campaign">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name"
                :class="[{ 'is-invalid': errors.has('form_create_campaign.name') }, 'form-control']"
                v-validate="'required|max:40'"
                v-model="newCampaign.name">
            <div class="invalid-feedback" v-show="errors.has('form_create_campaign.name')">{{ errors.first('form_create_campaign.name') }}</div>
        </div>
    </form>
</modal>
</template>


<script>
export default {
	name: 'add-campaign-modal',

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

            newCampaign: {},
        };
    },

    methods: {
        createCampaign() {
            this.$validator.validateAll('form_create_campaign').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.post('campaigns', this.newCampaign)
                .then(response => {

                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$resetForm(this.newCampaign);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {
                    
                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form_create_campaign');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>