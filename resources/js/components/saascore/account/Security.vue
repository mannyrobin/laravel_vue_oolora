<template>
<div>
	
    <div class="row">
        <div class="col-lg-6">

            <form @submit.prevent="changePassword" data-vv-scope="form-password">

                <div class="font-weight-semibold font-size-lg">Change Password</div>
                <p class="text-gray-600">Keep your account secure by using a strong password</p>


                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password"
                        :class="['form-control', { 'is-invalid': errors.has('form-password.current_password') }]"
                        v-validate="'required'"
                        v-model="passwordData.current_password">
                    <div class="invalid-feedback" v-show="errors.has('form-password.current_password')">{{ errors.first('form-password.current_password') }}</div>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password"
                        :class="['form-control', { 'is-invalid': errors.has('form-password.password') }]"
                        v-validate="'required|min:6|confirmed:password_confirmation'"
                        v-model="passwordData.password">
                    <div class="invalid-feedback" v-show="errors.has('form-password.password')">{{ errors.first('form-password.password') }}</div>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                        ref="password_confirmation"
                        :class="['form-control', { 'is-invalid': errors.has('form-password.password_confirmation') }]"
                        v-validate="'required'"
                        v-model="passwordData.password_confirmation">
                    <div class="invalid-feedback" v-show="errors.has('form-password.password_confirmation')">{{ errors.first('form-password.password_confirmation') }}</div>
                </div>

                <div class="text-center">
                    <button-spinner :loading="btnLoadingPassword" class="btn btn-primary" @click.prevent="changePassword()">Change Password</button-spinner>
                </div>
                
            </form>

        </div>
    </div>

    <hr class="my-5">

    <div class="font-weight-semibold font-size-lg">Close Account</div>
    <p class="text-gray-600">Deleting your account is permanent</p>

    <button-spinner :loading="btnLoadingDelete" type="button" class="btn-outline-danger btn-sm border-transparent" @click="deleteAccount()">Delete Account</button-spinner>

</div>
</template>


<script>
export default {    
    props: ['userData'],

    data () {
        return {
            btnLoadingPassword: false,
            btnLoadingDelete: false,
            passwordData: {},
        };
    },

    created () {
        // Set validation custom message
        const dictionary = {
            en: {
                attributes: {
                    current_password: 'current password',
                    password_confirmation: 'password confirmation',
                }
            }
        };
        
        this.$validator.localize(dictionary);
    },

    methods: {
        changePassword () {
            this.$validator.validateAll('form-password').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoadingPassword = true;

                axios.put('user/password', this.passwordData)
                .then(response => {
                    this.btnLoadingPassword = false;
                    this.$alert.success(response.data.message);

                    // Logout the user
                    window.location.href = "/logout";
                })
                .catch(error => {
                    this.btnLoadingPassword = false;
                    this.$backendErrors(error.response.data, 'form-password');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);
                });

            });
        },

        deleteAccount () {
            this.$confirm('<p>You are about to permanently delete your account, this cannot be undone.</p> <p>You can always cancel any active subscription, instead of deleting your account.</p>', 'Account Deletion', {
                confirmButtonText: 'Confirm Deletion',
                cancelButtonText: "Don't Delete",
                dangerouslyUseHTMLString: true,
            }).then(() => {

                this.btnLoadingDelete = true;

                axios.delete('user/'+ this.userData.id)
                .then(response => {
                    this.btnLoadingDelete = false;
                    this.$alert.success(response.data.message);

                    // Redirect
                    window.location.href = "/";
                }).catch(error => {
                    this.btnLoadingDelete = false;
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        }
    }
}
</script>