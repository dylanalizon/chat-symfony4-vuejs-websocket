/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../css/app.scss')

import Vue from 'vue'
import Messagerie from './components/MessagerieComponent'
import Application from './components/ApplicationComponent'
import OnlineUsers from './components/OnlineUsersComponent'
import store from './store/store'
import VueRouter from 'vue-router'


Vue.use(VueRouter)

const routes = [
    {path: '/', component: Messagerie, name: 'messagerie'},
    {path: '/utilisateurs-en-ligne', component: OnlineUsers, name: 'utilisateurs-en-ligne'},
    {path: '/conversations/:id', component: Messagerie, name: 'conversations'}
]

const router = new VueRouter({
    mode: 'history',
    routes
})

new Vue({
    el: '#app',
    template: '<Application/>',
    components: { Application },
    store,
    router
});