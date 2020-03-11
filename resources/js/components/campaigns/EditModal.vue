<template>
<modal
    title="Edit Campaign"
    submitText="Update Campaign"
    size="small"
    :show.sync="open"
    :submit-loading="btnLoading"
    @submit="updateCampaign"
    @close="$emit('update:open', false)">

    <form @submit.prevent="updateCampaign" class="mt-3" data-vv-scope="form_edit_campaign">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name"
                :class="[{ 'is-invalid': errors.has('form_edit_campaign.name') }, 'form-control']"
                v-validate="'required|max:40'"
                v-model="editCampaign.name">
            <div class="invalid-feedback" v-show="fields.$form_edit_campaign && fields.$form_edit_campaign.name && fields.$form_edit_campaign.name.dirty && errors.has('form_edit_campaign.name')">{{ errors.first('form_edit_campaign.name') }}</div>
        </div>
    </form>
</modal>
</template>


<script>
export default {
	name: 'edit-campaign-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        editCampaign: {
            type: Object,
            default: ''
        },
    },


    data() {
        return {
            btnLoading: false
        };
    },

    methods: {
        updateCampaign() {
            this.$validator.validateAll('form_edit_campaign').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.put('/campaigns/'+ this.editCampaign.id, this.editCampaign)
                .then(response => {

                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form_edit_campaign');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>