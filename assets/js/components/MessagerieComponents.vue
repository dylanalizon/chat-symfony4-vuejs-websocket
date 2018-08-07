<template>
    <div class="container">
        <div id="conversations">
            <div class="row">
                <div class="col m3">
                    <ListeConversations></ListeConversations>
                </div>
                <div class="col m9 conversation">
                    <router-view></router-view>
                </div>
            </div>
        </div>
        <div id="qui-est-en-ligne">
        </div>
    </div>
</template>

<script>
    import ListeConversations from './ListeConversationsComponents'

    export default {
        name: 'Messagerie',
        mounted () {
          this.$store.commit('setUser', dataLayout.userId)
        },
        components: { ListeConversations }
    }
</script>