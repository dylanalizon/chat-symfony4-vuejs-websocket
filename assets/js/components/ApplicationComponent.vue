<template>
    <div>
        <Navbar></Navbar>
        <main>
            <div class="container">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script>
    import Navbar from './NavbarComponent'
    export default {
        name: 'Application',
        mounted () {
            this.$store.commit('setUser', dataLayout.userId)
            this.$store.dispatch('loadConversations')
        },
        components: {Navbar}
    }
</script>