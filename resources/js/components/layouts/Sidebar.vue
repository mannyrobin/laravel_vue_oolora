<template>
<aside id="sidebar" class="sidebar-light bg-white border-right" :class="{show: activeClass}">
    
    <ul class="nav flex-column mt-2">
        <li class="nav-item-header">Main Menu</li>
        
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/dashboard">
                <i class="fal fa-home"></i>
                <span>Dashboard</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/campaigns">
                <i class="fal fa-layer-group"></i>
                <span>Campaigns</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/links">
                <i class="fal fa-link"></i>
                <span>Smart Links</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/analytics">
                <i class="fal fa-chart-line"></i>
                <span>Analytics</span>
            </router-link>
        </li>
    </ul>

    <ul class="nav flex-column mt-2">
        <li class="nav-item-header">Manage</li>
        
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/pixels">
                <i class="fal fa-location-arrow"></i>
                <span>Pixels</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/domains">
                <i class="fal fa-window"></i>
                <span>Domains</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/call-to-actions">
                <i class="fal fa-window-restore"></i>
                <span>Call to Actions</span>
            </router-link>
        </li>
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/custom-scripts">
                <i class="fal fa-laptop-code"></i>
                <span>Custom Scripts</span>
            </router-link>
        </li>
    </ul>

</aside>
</template>


<script>
export default {
    name: 'sidebar',

    props: {
        activeClass: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        closePanel() {
            this.$emit("closePanel", {});
        }
    }
}
</script>