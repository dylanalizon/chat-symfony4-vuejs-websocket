import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const fetchApi = async function(url, options = {}){
    let response = await fetch(url, options)
    if(response.ok){
        return response.json()
    }else{
        throw await response.json()
    }
};

export default new Vuex.Store({
    strict: true,
    state: {
        user: null,
        conversations: {}
    },
    getters: {
        user: function (state){
            return state.user
        },
        conversations: function (state) {
            return state.conversations
        },
        conversation: function (state) {
            return function (id) {
                return state.conversations[id] || {}
            }
        },
        messages: function (state) {
            return function (id) {
                let conversation = state.conversations[id]
                if (conversation && conversation.messages) {
                    return conversation.messages
                }else{
                    return []
                }
            }
        }
    },
    mutations: {
        setUser: function (state, userId) {
          state.user = userId
        },
        markAsRead: function (state, conversationId) {
            state.conversations[conversationId].unread = 0
        },
        addConversations: function (state, {conversations}) {
            conversations.forEach(function(c){
                let conversation = state.conversations[c.id] || {messages: [], count: 0}
                conversation = {...conversation, ...c}
                state.conversations = {...state.conversations, ...{[c.id]: conversation}}
            })
        },
        addMessage: function (state, {message, conversationId}) {
            state.conversations[conversationId].count++
            state.conversations[conversationId].messages.push(message)
        },
        addMessages: function (state, {messages, conversationId, count}) {
            let conversation = state.conversations[conversationId] || {}
            conversation.messages = messages
            conversation.count = count
            conversation.loaded = true
            state.conversations = {...state.conversations, ...{[conversationId]: conversation}}
        },
        prependMessages: function (state, {messages, conversationId}) {
            let conversation = state.conversations[conversationId] || {}
            conversation.messages = [...messages, ...conversation.messages]
            state.conversations = {...state.conversations, ...{[conversationId]: conversation}}
        }
    },
    actions: {
        loadConversations: async function (context) {
            let response = await fetchApi('/api/conversations')
            context.commit('addConversations', {conversations: response.conversations})
        },
        loadMessages: async function (context, conversationId){
            if (!context.getters.conversation(conversationId).loaded) {
                let response = await fetchApi('/api/conversations/' + conversationId)
                context.commit('addMessages', { messages : response.messages, conversationId: conversationId, count : response.count })
                context.commit('markAsRead', conversationId)
            }
        },
        sendMessage: async function (context, {content, userId}){
            let response = await fetchApi('/api/conversations/' + userId, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    content: content,
                    token: dataLayout.token
                })
            })
            context.commit('addMessage', { message: response.message, conversationId: userId })
        },
        loadPreviousMessages: async function (context, conversationId) {
            let message = context.getters.messages(conversationId)[0]
            if (message) {
                let url = '/api/conversations/' + conversationId + '?before=' + message.created_at
                let response = await fetchApi(url)
                context.commit('prependMessages', {conversationId: conversationId, messages: response.messages })
            }
        }
    }
});