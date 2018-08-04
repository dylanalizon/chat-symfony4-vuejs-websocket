<template>
    <div class="collection card">
        <router-link :to="{name: 'conversation', params: {id: conversation.id}}" class="collection-item " v-for="conversation in conversations" :key="conversation.id">
            {{ conversation.username }}
            <span class="new badge" data-badge-caption="" v-if="conversation.unread">{{ conversation.unread }}</span>
        </router-link>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: 'ListeConversations',
        computed: {
            ...mapGetters(['conversations'])
        },
        mounted () {
            this.$store.dispatch('loadConversations')
        }
    }
</script>