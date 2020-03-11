<template>
<aside id="sidebar" class="sidebar-dark bg-dark" :class="{show: activeClass}">

    <div class="sidebar-brand">
        <a :href="$appSettings.appUrl + '/dashboard'">
            <img class="logo" :src="$appSettings.logoLight" :alt="$appSettings.appName">
        </a>
    </div>


    <ul class="nav flex-column mt-3">
        <li class="nav-item-header">Overview</li>
        
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin"><i class="fal fa-home"></i> <span>Dashboard</span></router-link>
        </li>
    </ul>

    <ul class="nav flex-column mt-3">
        <li class="nav-item-header">Membership</li>
        
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/membership/users"><i class="fal fa-users"></i> Users</router-link>
        </li>

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/membership/payments"><i class="fal fa-money-check-alt"></i> Payments</router-link>
        </li>

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/membership/plans"><i class="fal fa-shopping-basket"></i> Subscription Plans</router-link>
        </li>

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/membership/vouchers"><i class="fal fa-credit-card-front"></i> Vouchers</router-link>
        </li>
    </ul>

    <ul class="nav flex-column mt-3">
        <li class="nav-item-header">Settings</li>
        
        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/settings/general"><i class="fal fa-sliders-h"></i> General</router-link>
        </li>

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/settings/branding"><i class="fal fa-pencil-paintbrush"></i> Branding</router-link>
        </li>

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/settings/mail"><i class="fal fa-envelope"></i> Mail</router-link>
        </li>
       
       <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/settings/payment-gateway"><i class="fal fa-usd-square"></i> Payment Gateway</router-link>
        </li>  

        <li class="nav-item" @click="closePanel">
            <router-link class="nav-link" to="/admin/settings/custom-code"><i class="fal fa-cogs"></i> Custom Code</router-link>
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