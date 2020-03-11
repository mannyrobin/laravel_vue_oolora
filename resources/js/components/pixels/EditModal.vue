<template>
<modal
    title="Edit Pixel"
    submit-text="Update Pixel"
    size="small"
    :show.sync="open"        
    :submit-loading="btnLoading"
    @submit="updatePixel"
    @close="$emit('update:open', false)">

    <form data-vv-scope="form-edit-pixel" class="mt-3">
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name"
                :class="['form-control', { 'is-invalid': errors.has('form-edit-pixel.name') }]"
                v-validate="'required|max:40'"
                v-model="editPixel.name">
            <div class="invalid-feedback" v-show="errors.has('form-edit-pixel.name')">{{ errors.first('form-edit-pixel.name') }}</div>
        </div>

        <div class="row">

            <div class="col-sm-6">

                <div class="form-group">
                    <label>Platform</label>
                    <form-select name="platform" v-model="editPixel.platform" v-validate="'required'" :class="[{ 'is-invalid': errors.has('form-edit-pixel.platform') }]">
                        <select-option
                            v-for="item in platforms"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                            
                            <span><img :src="$appSettings.appUrl + '/assets/images/brands/' + item.value + '.png'" class="rounded-circle mr-2" width="20" height="20" alt=""></span> {{ item.label }}

                        </select-option>
                    </form-select>    
                    <span class="form-text text-danger" v-show="errors.has('form-edit-pixel.platform')">{{ errors.first('form-edit-pixel.platform') }}</span>
                </div>

            </div>

            <div class="col-sm-6">

                <label>Pixel ID</label>
                <input type="text" name="code"
                    :class="[{ 'is-invalid': errors.has('form-edit-pixel.code') }, 'form-control']"
                    v-validate="'required'"
                    v-model="editPixel.code">
                <span class="form-text text-danger" v-show="errors.has('form-edit-pixel.code')">{{ errors.first('form-edit-pixel.code') }}</span>

            </div>

        </div>

        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="disable_pixel" name="disable_pixel" :true-value="true" v-model="editPixel.disabled">
            <label class="custom-control-label" for="disable_pixel">Disable Pixel</label>
        </div>

    </form>

</modal>
</template>


<script>
export default {
    name: 'edit-pixel-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        editPixel: {
            type: Object,
            default: ''
        },
    },


    data() {
        return {
            btnLoading: false,

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

    created() {
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
    },

    methods: {
        updatePixel() {
            this.$validator.validateAll('form-edit-pixel').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.put('pixels/'+ this.editPixel.id, this.editPixel)
                .then(response => {
                    
                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');
                    
                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-edit-pixel');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>