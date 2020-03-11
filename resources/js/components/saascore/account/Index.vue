<template>
<div class="page-account bg-gray h-100 p-3">
	
    <h4 class="mb-2">Account Settings</h4>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <tabs v-if="!showPlaceholder" :pills="false" :fill="false">
         
                <tab-pane class="p-3">
                    <span slot="title" class="px-5">General</span>
                    <general :user-data="userData"></general>
                </tab-pane>


                <tab-pane class="p-3">
                    <span slot="title" class="px-5">Security</span>
                    <security :user-data="userData"></security>
                </tab-pane>

            </tabs>

            <div v-if="showPlaceholder" class="row p-4">
                <div class="col-md-6">
                    <placeholder class="mb-5" heading :heading-image="false"></placeholder>
                    <placeholder class="mb-5" heading :heading-image="false"></placeholder>
                    <placeholder class="mb-5" heading :heading-image="false"></placeholder>
                    <placeholder class="mb-5" heading :heading-image="false"></placeholder>
                    <placeholder heading :heading-image="false"></placeholder>
                </div>
                <div class="col-md-6">

                   <div class="row align-items-center justify-content-center ml-md-5">
                        <div class="col-4">
                            <placeholder center image image-circle image-width="130px" image-height="130px"></placeholder>
                        </div>
                        <div class="col-8">
                            <placeholder class="w-75" heading :heading-image="false"></placeholder>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
</template>


<script>
import General from './General.vue';
import Security from './Security.vue';

export default {
    components: {
        General,
        Security
    },

    data() {
        return {
            showPlaceholder: false,

            userData: {}
        };
    },

    created() {
        this.getUser();
    },

    methods: {
        getUser() {
            this.showPlaceholder = true;

            axios.get('user')
            .then(response => {
                this.showPlaceholder = false;
                this.userData = response.data;
            })
            .catch(error => { }); 
        }
    }
}
</script>