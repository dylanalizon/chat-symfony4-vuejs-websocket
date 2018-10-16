<template>
    <div class="card">
        <div class="card-content">
            <div class="card-title">
                <h2>
                    {{ toName }}
                </h2>
            </div>
            <div class="messages">
                <Message :message="message" :user="user" v-for="message in messages" :key="message.id"></Message>
            </div>
            <div class="envoyer-message">
                <form action="">
                <div class="row">
                    <div class="input-field col s12" :class="classError">
                        <textarea name="content" class="materialize-textarea" placeholder="Ecrivez votre message..." v-model="content" @keypress.enter="sendMessage" ></textarea>
                        <span class="erreur" v-if="erreur">Votre message ne doit pas être vide</span>
                    </div>
                </div>
                </form>
            </div>
            <div class="conversation-loading" v-if="loading">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import Message from './MessageComponent'
    import {mapGetters} from 'vuex'

    export default {
        name: 'Messages',
        data () {
          return {
              content: '',
              erreur : '',
              loading: false
          }
        },
        computed: {
            ...mapGetters(['user']),
            messages: function () {
                return this.$store.getters.messages(this.$route.params.id)
            },
            lastMessage: function () {
                return this.messages[this.messages.length - 1]
            },
            conversation () {
                return this.$store.getters.conversation(this.$route.params.id)
            },
            toName () {
                return this.conversation.username
            },
            classError () {
                return this.erreur ? 'envoyer-message-erreur' : ''
            },
            countAll () {
                return this.conversation.count
            }
        },
        mounted () {
            this.loadMessages()
            this.$listeMessages = this.$el.querySelector('.messages')
            document.addEventListener('visibilitychange', this.onVisible)
            this.scrollToBottom()
        },
        destroyed () {
            document.removeEventListener('visibilitychange', this.onVisible)
        },
        watch: {
            '$route.params.id': function () {
                this.loadMessages()
            },
            lastMessage: function () {
                this.scrollToBottom()
            }
        },
        methods: {
            async loadMessages () {
                this.loading = true
                await this.$store.dispatch('loadMessages', this.$route.params.id)
                if (this.messages.length < this.countAll) {
                    this.$listeMessages.addEventListener('scroll', this.onScroll)
                }
                this.loading = false
            },
            async onScroll () {
                if (this.$listeMessages.scrollTop === 0){
                    this.loading = true
                    this.$listeMessages.removeEventListener('scroll', this.onScroll)
                    let previousHeight = this.$listeMessages.scrollHeight
                    await this.$store.dispatch('loadPreviousMessages', this.$route.params.id)
                    this.$nextTick(() => {
                        this.$listeMessages.scrollTop = this.$listeMessages.scrollHeight - previousHeight
                    })
                    if (this.messages.length < this.countAll) {
                        this.$listeMessages.addEventListener('scroll', this.onScroll)
                    }
                    this.loading = false
                }
            },
            scrollToBottom () {
                this.$nextTick(() => {
                    this.$listeMessages.scrollTop = this.$listeMessages.scrollHeight
                })
            },
            async sendMessage (e) {
                if(e.shiftKey === false){
                    this.loading = true
                    this.erreur = ''
                    e.preventDefault()
                    try{
                        await this.$store.dispatch('sendMessage', {
                            content: this.content,
                            userId: this.$route.params.id
                        })
                        this.content = ''
                    }catch (e) {
                        this.erreur = e.message
                    }
                }
                this.loading = false
            },
            onVisible () {
                if (document.hidden === false) {
                    this.$store.dispatch('loadMessages', this.$route.params.id)
                }
            }
        },
        components: { Message }
    }
</script>