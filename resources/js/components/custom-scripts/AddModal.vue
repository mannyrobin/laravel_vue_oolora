<template>
<modal
    title="Add Custom Script"
    submit-text="Add New Script"
    size="small"
    :show.sync="open"        
    :submit-loading="btnLoading"
    @submit="createScript"
    @close="$emit('update:open', false)">

    <form data-vv-scope="form-add-script" class="mt-3">
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" placeholder="Name Your Script" 
                :class="['form-control', { 'is-invalid': errors.has('form-add-script.name') }]"
                v-validate="'required|max:40'"
                v-model="newScript.name">
            <div class="invalid-feedback" v-show="errors.has('form-add-script.name')">{{ errors.first('form-add-script.name') }}</div>
        </div>

        <div class="form-group">
            <label>Code</label>
            <textarea rows="5" name="code" placeholder="<script> etc... </script>" 
                :class="['form-control', { 'is-invalid': errors.has('form-add-script.code') }]"
                v-validate="'required'"
                v-model="newScript.code"></textarea>
            <div class="invalid-feedback" v-show="errors.has('form-add-script.code')">{{ errors.first('form-add-script.code') }}</div>
        </div>

    </form>

</modal>
</template>


<script>
export default {
	name: 'add-script-modal',

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

            newScript: {},
        };
    },

    methods: {
        createScript() {
            this.$validator.validateAll('form-add-script').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.post('custom-scripts', this.newScript)
                .then(response => {

                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$resetForm(this.newScript);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-add-script');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>