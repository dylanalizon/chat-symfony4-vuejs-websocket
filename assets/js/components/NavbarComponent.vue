<template>
    <header class="barre-de-navigation">
        <div class="row z-depth-1">
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s6">
                        <router-link :to="{name: 'messagerie'}" :class="messagerieClass" exact>
                            <p><i class="material-icons">chat_bubble_outline</i></p>
                            Conversations
                        </router-link>
                    </li>
                    <li class="tab col s6">
                        <router-link :to="{name: 'utilisateurs-en-ligne'}">
                            <p><i class="material-icons">group</i></p>
                            Qui est en ligne ?
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
        <div class="deconnexion">
            <a href="/logout" ><i class="material-icons">exit_to_app</i></a>
        </div>
    </header>
</template>

<script>
    export default {
        name: 'Navbar',
        computed: {
            messagerieClass () {
                return this.$route.name === 'conversations' ? 'router-link-active' : ''
            }
        }
    }
</script>