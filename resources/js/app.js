/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
import FlashMessage from '@smartweb/vue-flash-message';

let bus = new Vue({});

Vue.use(VueRouter);
Vue.use(FlashMessage);
// If you want to use it in your vue components
Vue.prototype.translate=require('./VueTranslation/Translation').default.translate;
window.bus = new Vue({});


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/Profile.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('profile', require('./components/Profile.vue').default);
// Vue.component('community', require('./components/Community.vue').default);
// Vue.component('question-detail', require('./components/QuestionDetail.vue').default);
// Vue.component('home', require('./components/Home.vue').default);

Vue.component('pagination', require('laravel-vue-pagination'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import router from './routes';

const app = new Vue({
    el: '#app',
    router
});
