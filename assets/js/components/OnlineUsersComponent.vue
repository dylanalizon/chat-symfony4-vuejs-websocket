<template>
    <div class="online-users">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <h2>
                        Qui est en ligne ?
                    </h2>
                </div>
                <div class="collection utilisateurs" v-if="orderedUsers.length > 0">
                    <router-link :to="{name: 'conversations', params: {id: user.id}}" v-for="user in orderedUsers" :key="user.id" class="collection-item">
                        {{ user.username }}
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import orderBy from 'lodash/orderBy'

    export default {
        name: 'OnlineUsers',
        computed: {
            ...mapGetters(['onlineUsers']),
            orderedUsers: function () {
                return orderBy(this.onlineUsers, ['username'])
            }
        },
        mounted () {
            this.$store.commit("closeConversation")
        }
    }
</script>