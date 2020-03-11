<template>
    <modal
        title="Edit Custom Script"
        submit-text="Update Script"
        size="small"
        :show.sync="open"        
        :submit-loading="btnLoading"
        @submit="updateScript"
        @close="$emit('update:open', false)">

        <form data-vv-scope="form-edit-script" class="mt-3">
            
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name"
                    :class="['form-control', { 'is-invalid': errors.has('form-edit-script.name') }]"
                    v-validate="'required|max:40'"
                    v-model="editScript.name">
                <div class="invalid-feedback" v-show="errors.has('form-edit-script.name')">{{ errors.first('form-edit-script.name') }}</div>
            </div>

            <div class="form-group">
                <label>Code</label>
                <textarea rows="5" name="code"
                    :class="['form-control', { 'is-invalid': errors.has('form-add-script.code') }]"
                    v-validate="'required'"
                    v-model="editScript.code"></textarea>
                <div class="invalid-feedback" v-show="errors.has('form-edit-script.code')">{{ errors.first('form-add-script.code') }}</div>
            </div>

            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="disable_script" name="disable_script" :true-value="true" v-model="editScript.disabled">
                <label class="custom-control-label" for="disable_script">Disable Script</label>
            </div>

        </form>

    </modal>
</template>


<script>
export default {
	name: 'edit-script-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        editScript: {
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
        updateScript() {
            this.$validator.validateAll('form-edit-script').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.put('custom-scripts/'+ this.editScript.id, this.editScript)
                .then(response => {
                    
                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');
                    
                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-edit-script');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>