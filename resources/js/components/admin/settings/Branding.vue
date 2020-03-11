<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header pb-2">
            <h4 class="card-title">Branding Settings</h4>
        </div>

        <div class="card-body">

            <p>Manage the application appearance assets such as logo and favicon.</p>

            <hr class="my-4">

            <div class="form-group row">
                <label class="col-form-label font-weight-semibold col-lg-3">Logo Dark</label>
                <div class="col-lg-9 mb-md-3">
                    
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="bg-light p-1 border text-center">
                                <img width="180" :src="settingsData['settings.logo_dark']" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-4">
                             <file-upload
                                action="/api/admin/settings/upload-image"
                                :data="{'settings_key': 'logo_dark'}"
                                :show-file-list="false"
                                :on-success="uploadSuccess"
                                :before-upload="uploadBefore"
                                :on-error="uploadError">

                                <button type="button" class="btn btn-primary btn-sm">Change Logo</button>
                            </file-upload>
                        </div>
                    </div>

                    <div class="form-text font-size-sm text-muted">Recommended size 50x200 png/jpg and less than 2MB</div>

                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label font-weight-semibold col-lg-3">Logo Light</label>
                <div class="col-lg-9 mb-md-3">
                    
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="bg-dark p-1 border text-center">
                                <img width="180" :src="settingsData['settings.logo_light']" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-4">
                             <file-upload
                                action="/api/admin/settings/upload-image"
                                :data="{'settings_key': 'logo_light'}"
                                :show-file-list="false"
                                :on-success="uploadSuccess"
                                :before-upload="uploadBefore"
                                :on-error="uploadError">

                                <button type="button" class="btn btn-primary btn-sm">Change Logo</button>
                            </file-upload>
                        </div>
                    </div>

                    <div class="form-text font-size-sm text-muted">Recommended size 50x200 png/jpg and less than 2MB</div>

                </div>
            </div>

            <div class="form-group row mb-0">
                <label class="col-form-label font-weight-semibold col-lg-3">Favicon</label>
                <div class="col-lg-9 mb-md-3">
                    
                    <div class="row align-items-center">
                        <div class="col-1">
                            <div class="bg-light p-1 border text-center">
                                <img width="16" :src="settingsData['settings.favicon']" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-4">
                             <file-upload
                                action="/api/admin/settings/upload-image"
                                :data="{'settings_key': 'favicon'}"
                                :show-file-list="false"
                                :on-success="uploadSuccess"
                                :before-upload="uploadBefore"
                                :on-error="uploadError">

                                <button type="button" class="btn btn-primary btn-sm">Change Favicon</button>
                            </file-upload>
                        </div>
                    </div>

                    <div class="form-text font-size-sm text-muted">Recommended size 16x16 png and less than 2MB</div>

                </div>
            </div>
        </div>

    </div>
    
</div>
</template>


<script>
export default {
    data() {
        return {
            settingsData: {}
        };
    },

    created () {
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

        uploadBefore(file) {
            const lessThanMS = file.size / 1024 / 1024 < 2;
            let supportedFormat = false;
            
            if ( file.type === 'image/jpeg' || file.type === 'image/png' )
                supportedFormat = true;

            if ( ! supportedFormat )
                this.$alert.error('Only jpg/png formats are supported');

            if ( ! lessThanMS )
                this.$alert.error('The file size can not exceed 2MB');

            return supportedFormat && lessThanMS;
        },

        uploadSuccess(result) {
            this.$alert.success(result.message);
            this.getSettings();
        },

        uploadError(err, file, fileList) {
            let response = JSON.parse(err.message);
            let errorMessage = response.errors ? this.$alert.error(response.errors.file[0]) : response.message;
            
            this.$alert.error(errorMessage);
        }

    }
}
</script>