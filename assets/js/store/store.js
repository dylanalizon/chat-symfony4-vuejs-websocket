import Vue from 'vue'
import Vuex from 'vuex'
import Favico from 'favico.js'

Vue.use(Vuex);

const favicon = new Favico({
    animation: 'none'
})
const title = document.title

const updateTitle = function (conversations) {
    let unread = Object.values(conversations).reduce((acc, conversation) => parseInt(conversation.unread) + acc, 0)
    if(unread === 0){
        document.title = title
        favicon.reset()
    }else{
        document.title = `(${unread}) ${title}`
        favicon.badge(unread)
    }
}

const fetchApi = async function(url, options = {}){
    let response = await fetch(url, options)
    if(response.ok){
        return response.json()
    }else{
        throw await response.json()
    }
};

const ws =  WS.connect("ws://127.0.0.1:8080");

var sendMessageWs;

ws.on("socket/connect", function(session){
    session.subscribe("app/chat/" + store.getters.user, function(uri, payload){
        if(payload.message){
            store.dispatch('messageReceived', payload.message)
        }
    })
    session.subscribe("app/users", function(uri, payload){
        if (payload.online_users) {
            store.commit('updateOnlineUsers', payload.online_users)
        }
    })
    sendMessageWs = function(message){
        session.publish("app/chat/" + message.to_user.id, message);
    }
})

ws.on("socket/disconnect", function(error){
    console.log("Disconnected for " + error.reason + " with code " + error.code);
})

const store = new Vuex.Store({
    strict: true,
    state: {
        user: null,
        conversations: {},
        openedConversation: [],
        onlineUsers: []
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
        },
        onlineUsers: function (state) {
            return state.onlineUsers
        }
    },
    mutations: {
        setUser: function (state, userId) {
            state.user = userId
        },
        markConversationAsRead: function (state, conversationId) {
            state.conversations[conversationId].unread = 0
        },
        markMessageAsRead: function (state, {message, readAt}) {
            let conversation = state.conversations[message.from_user.id]
            if (conversation && conversation.messages) {
                let msg = conversation.messages.find(m => m.id === message.id)
                if (msg) {
                    msg.read_at = readAt
                }
            }
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
        },
        openConversation: function (state, conversationId) {
            state.openedConversation = [conversationId]
        },
        closeConversation: function (state) {
            state.openedConversation = []
        },
        incrementUnread: function (state, conversationId) {
            let conversation = state.conversations[conversationId]
            conversation.unread++
        },
        updateOnlineUsers: function (state, users) {
            state.onlineUsers = users.filter( u => u.id !== state.user )
        }
    },
    actions: {
        messageReceived: function(context, message){
            context.commit('addMessage', { message: message, conversationId: message.from_user.id })
            if(!context.state.openedConversation.includes(message.from_user.id) || document.hidden){
                context.commit('incrementUnread', message.from_user.id)
                updateTitle(context.state.conversations)
            }else{
                context.dispatch('markAsRead', message)
            }
        },
        loadConversations: async function (context) {
            let response = await fetchApi('/api/conversations')
            context.commit('addConversations', {conversations: response.conversations})
        },
        loadMessages: async function (context, conversationId){
            context.commit('openConversation', parseInt(conversationId))
            if (!context.getters.conversation(conversationId).loaded) {
                let response = await fetchApi('/api/conversations/' + conversationId)
                context.commit('addMessages', { messages : response.messages, conversationId: conversationId, count : response.count })
            }
            context.getters.messages(conversationId).forEach(message => {
                if (message.read_at === null && message.to_user.id === context.state.user) {
                    context.dispatch('markAsRead', message)
                }
            })
            context.commit('markConversationAsRead', conversationId)
            updateTitle(context.state.conversations)
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
            sendMessageWs(response.message, userId)
        },
        loadPreviousMessages: async function (context, conversationId) {
            let message = context.getters.messages(conversationId)[0]
            if (message) {
                let url = '/api/conversations/' + conversationId + '?before=' + message.created_at
                let response = await fetchApi(url)
                context.commit('prependMessages', {conversationId: conversationId, messages: response.messages })
            }
        },
        markAsRead: async function (context, message) {
            let response = await fetchApi('/api/messages/' + message.id, {method: 'POST'})
            context.commit('markMessageAsRead', { message: message, readAt: response.read_at})
        }
    }
})

export default store;