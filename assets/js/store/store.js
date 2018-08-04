import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const get = async function(url){
    let response = await fetch(url)
    if(response.ok){
        return response.json()
    }
};

export default new Vuex.Store({
    strict: true,
    state: {
        conversations: {}
    },
    getters: {
        conversations: function (state) {
            return state.conversations
        }
    },
    mutations: {
        addConversation: function (state, {conversations}) {
            state.conversations = conversations
        }  
    },
    actions: {
        loadConversations: async function (context) {
            let response = await get('/api/conversations')
            context.commit('addConversation', {conversations: response.conversations})
        }
    }
});