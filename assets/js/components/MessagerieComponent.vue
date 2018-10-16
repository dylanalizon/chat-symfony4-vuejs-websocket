<template>
    <div class="row">
        <div class="col m3">
            <ListeConversations></ListeConversations>
        </div>
        <div class="col m9 conversation">
            <Messages v-if="this.$route.params.id"></Messages>
        </div>
    </div>
</template>

<script>
    import ListeConversations from './ListeConversationsComponents'
    import Messages from './MessagesComponent'

    export default {
        name: 'Messagerie',
        components: { ListeConversations, Messages }
    }
</script>