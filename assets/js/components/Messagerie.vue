<template>
    <div class="container">
        <div id="conversations">
            <div class="row">
                <div class="col m3">
                    <ListeConversations></ListeConversations>
                </div>
            </div>
        </div>
        <div id="qui-est-en-ligne">
        </div>
    </div>
</template>

<script>
    import ListeConversations from './ListeConversations'

    export default {
        name: 'Messagerie',
        components: { ListeConversations }
    }
</script>