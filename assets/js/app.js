/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../css/app.scss')

import Vue from 'vue'
import Messagerie from './components/MessagerieComponents'
import Messages from './components/MessagesComponents'
import store from './store/store'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
    {path: '/'},
    {path: '/conversations/:id', component: Messages, name: 'conversation'}
]

const router = new VueRouter({
    mode: 'history',
    routes
})

new Vue({
    el: '#app',
    template: '<Messagerie/>',
    components: { Messagerie },
    store,
    router
});