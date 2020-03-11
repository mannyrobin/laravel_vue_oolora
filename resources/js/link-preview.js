// Load Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
window.axios.defaults.baseURL = document.head.querySelector('meta[name="app-url"]').content + '/api';


// Load Vuejs
window.Vue = require('vue');


// Import required modules
import AppKit from 'appkit';


Vue.use(AppKit);


// Register global components
Vue.component('cta-preview', require('./components/call-to-actions/CtaPreview.vue'));


// Initiate a Vue instance
const app = new Vue({
    el: '#app'
});