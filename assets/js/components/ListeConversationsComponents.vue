<template>
    <div class="collection card">
        <router-link :to="{name: 'conversation', params: {id: conversation.id}}" class="collection-item" active-class="active" v-for="conversation in orderedConversations" :key="conversation.id">
            {{ conversation.username }}
            <span class="new badge" data-badge-caption="" v-if="conversation.unread">{{ conversation.unread }}</span>
        </router-link>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import orderBy from 'lodash/orderBy'

    export default {
        name: 'ListeConversations',
        computed: {
            ...mapGetters(['conversations']),
            orderedConversations: function () {
                return orderBy(this.conversations, ['username'])
            }
        },
        mounted () {
            this.$store.dispatch('loadConversations')
        }
    }
</script>