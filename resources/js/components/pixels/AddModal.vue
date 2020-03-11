<template>
<modal
    title="Add Pixel"
    submit-text="Add New Pixel"
    size="small"
    :show.sync="open"        
    :submit-loading="btnLoading"
    @submit="createPixel"
    @close="$emit('update:open', false)">

    <form data-vv-scope="form-add-pixel" class="mt-3">
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" placeholder="Name your pixel" 
                :class="['form-control', { 'is-invalid': errors.has('form-add-pixel.name') }]"
                v-validate="'required|max:40'"
                v-model="newPixel.name">
            <div class="invalid-feedback" v-show="errors.has('form-add-pixel.name')">{{ errors.first('form-add-pixel.name') }}</div>
        </div>

        <div class="row">

            <div class="col-sm-6">

                <div class="form-group">
                    <label>Platform</label>
                    <form-select name="platform" v-model="newPixel.platform" v-validate="'required'" :class="[{ 'is-invalid': errors.has('form-add-pixel.platform') }]">
                        <select-option
                            v-for="item in platforms"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                            
                            <span><img :src="$appSettings.appUrl + '/assets/images/brands/' + item.value + '.png'" class="rounded-circle mr-2" width="20" height="20" alt=""></span> {{ item.label }}

                        </select-option>
                    </form-select>    
                    <span class="form-text text-danger" v-show="errors.has('form-add-pixel.platform')">{{ errors.first('form-add-pixel.platform') }}</span>
                </div>

            </div>

            <div class="col-sm-6">

                <label>Pixel ID</label>
                <input type="text" name="pixel_code" placeholder="eg: 102238493312" 
                    :class="[{ 'is-invalid': errors.has('form-add-pixel.pixel_code') }, 'form-control']"
                    v-validate="'required'"
                    v-model="newPixel.pixel_code">
                <span class="form-text text-danger" v-show="errors.has('form-add-pixel.pixel_code')">{{ errors.first('form-add-pixel.pixel_code') }}</span>

            </div>

        </div>

    </form>

</modal>
</template>


<script>
export default {
	name: 'add-pixel-modal',

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

            newPixel: {},

            platforms: [
                {
                    value: 'facebook',
                    label: 'Facebook',
                }, 
                {
                    value: 'twitter',
                    label: 'Twitter',
                },
                {                
                    value: 'linkedin',
                    label: 'Linkedin',
                }, 
                {                
                    value: 'pinterest',
                    label: 'Pinterest',
                }, 
                {                
                    value: 'quora',
                    label: 'Quora',
                }, 
                {                
                    value: 'bing',
                    label: 'Bing',
                }, 
                {                
                    value: 'google-adwords',
                    label: 'Google Adwords',
                }, 
                {                
                    value: 'google-tag-manager',
                    label: 'Google Tag Manager',
                }
            ]
        };
    },

    methods: {
        createPixel() {
            // Set validation custom message
            const dictionary = {
                en: {
                    attributes: {
                        pixel_code: 'Pixel ID',
                        code: 'Pixel ID',
                    }
                }
            };
            
            this.$validator.localize(dictionary);

            this.$validator.validateAll('form-add-pixel').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.post('pixels', this.newPixel)
                .then(response => {

                	this.btnLoading = false;
                   	this.$emit('update:open', false);

                    this.$resetForm(this.newPixel);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                	this.btnLoading = false;
                	this.$backendErrors(error.response.data, 'form-add-pixel');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>