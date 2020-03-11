<template>
<div class="has-sidebar has-navbar h-100">

    <navbar></navbar>

    <div id="page-wrapper">

        <sidebar></sidebar>

        <main id="page-content">
            <router-view></router-view>
        </main> 

    </div>

    <div v-if="!$user.emailVerified" class="alert border-0 text-gray-900 text-center mb-0 fixed-bottom rounded-0" role="alert" style="background: #ffc107;">
        Verify your email address, please check your email for a verification link. <a href="/email/verify" class="text-gray-900 font-size-lg" style="text-decoration: underline;">Didn't receive an email?</a>
    </div>

    <slideout-panel></slideout-panel>

</div>
</template>


<script>
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';

export default {
    name: 'layout-master',

    components: {
        Navbar,
        Sidebar
    },
}
</script>