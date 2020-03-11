// Load Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
window.axios.defaults.baseURL = document.head.querySelector('meta[name="app-url"]').content + '/api';



// Intercept all HTTP request for errors
axios.interceptors.response.use(null, (error) => {

	// unauthorized redirect
	if ( error.response.status == 401 )
    	window.location.href = "/login";

    // 3xx Redirection
	if ( error.response.status == 307 )
    	window.location.href = error.response.data.location;


    return Promise.reject(error);
});


// Load Vuejs
window.Vue = require('vue');


// Import required modules
import AppKit from 'appkit';
import VueRouter from 'vue-router';
import VeeValidate from 'vee-validate';
import VueClipboard from 'vue-clipboard2'


Vue.use(AppKit);
Vue.use(VueRouter);
Vue.use(VeeValidate);
Vue.use(VueClipboard)


// Register global components
Vue.component('cta-preview', require('./components/call-to-actions/CtaPreview.vue'));


// Import all routes
import SaaSCoreRoutes from './components/saascore/routes';
import AdminRoutes from './components/admin/routes';
import AppRoutes from './routes';

// Merge all the routes
const routes = AppRoutes.concat(SaaSCoreRoutes, AdminRoutes);

// Load Vue router
const router = new VueRouter({
	mode: 'history',
	linkExactActiveClass: "active",
	routes
});


// Before every route is loaded
router.beforeEach((to, from, next) => {

    // Check for new notifications
    if ( window.appUser ) {
        axios.get('notifications/count')
        .then(response => {
            window.appUser.unreadNotifications = response.data;
        }).catch(error => { });
    }


    // Route requires subscription feature usage check
    if (to.matched.some(record => record.meta.featureCheck)) {

        // Let all admin through
        if ( Vue.prototype.$can('access admin') ) {
            window.subscriptionUsage = {
                consumed: '',
                canuse: '',
                value: ''
            }

            next();

            return;
        }


        // Fetch information on the current feature usage
        axios.get('subscription/usage?feature=' + to.meta.featureCheck)
        .then(response => {

            // If the user doesn't have this feature ability
            // on their current plan redirect them to the upgrade error page
            if ( response.data.value == '0' && response.data.canuse == 0 )
                next({ path: '/dashboard/feature-upgrade' });


            // Else continue normal and set usage data
            window.subscriptionUsage = response.data;
            next();

        })
        .catch(error => {
            Vue.prototype.$alert.error('Unable to complete your request, please refresh and try again.');
        });
    }
    else {
        next();
    }

})


// Initiate a Vue instance
const app = new Vue({
    el: '#app',
    router
});
