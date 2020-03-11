<template>
<div class="admin has-sidebar has-navbar-sm has-navbar-content h-100">

    <div id="page-wrapper">

        <sidebar></sidebar>

        <main id="page-content">

            <navbar></navbar>

            <router-view></router-view>
            
        </main> 

    </div>
    
    <slideout-panel></slideout-panel>

</div>
</template>


<script>
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';

export default {
    name: 'layout',

    components: {
        Navbar,
        Sidebar
    },
}
</script>