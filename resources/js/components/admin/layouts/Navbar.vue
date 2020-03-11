<template>
<nav id="main-navbar" class="navbar navbar-sm navbar-expand navbar-light bg-white border-bottom fixed-top">
        
    <span class="form-inline navbar-icons d-lg-none">
        <a class="nav-link" href="#" @click.prevent="showSidebarPanel"> 
            <i class="fal fa-bars"></i>
        </a>
    </span>


    <div class="navbar-nav ml-auto">

        <span class="form-inline navbar-icons mr-4">
            <a class="nav-link" href="#" @click.prevent="showNotificationPanel">
                <i class="fal fa-bell"></i>
                <span v-if="appUser.unreadNotifications > 0" class="badge badge-pill badge-danger animate-pulse">{{ appUser.unreadNotifications }}</span>
            </a>
        </span>

        <ul class="navbar-nav">

            <dropdown tag="li" class="nav-item" menu-right>
                <a href="javascript:void(0)" slot="heading" class="nav-link dropdown-toggle">
                    <img class="avatar rounded-circle mr-2" :src="appUser.avatar" alt="*"> <span class="d-none d-lg-inline">{{ appUser.name }}</span>
                </a>
                
                <router-link class="dropdown-item" to="/account">
                    <i class="fal fa-user"></i> Account
                </router-link>

                <a class="dropdown-item" :href="$appSettings.appUrl + '/dashboard'">
                    <i class="fal fa-user-secret"></i> Exit Admin
                </a>

                <a class="dropdown-item" :href="$appSettings.appUrl + '/logout'">
                    <i class="fal fa-sign-out-alt"></i> Logout
                </a>
            </dropdown>

        </ul>

    </div>

</nav>
</template>


<script>
import Notifications from '../../layouts/Notifications.vue';
import Sidebar from './Sidebar.vue';

export default {
    name: 'navbar',

    components: {
        Notifications,
        Sidebar
    },

    data() {
        return {
            appUser: window.appUser,
        };
    },
    

    methods: {
        showNotificationPanel() {
            this.$showPanel({
                component: Notifications
            });
        },

        showSidebarPanel() {
            this.$showPanel({
                component: Sidebar,
                openOn: 'left',
                cssClass: "sidebar-panel",
                width: 220,
                props: {
                    activeClass : true
                }
            });
        }
    }
}
</script>