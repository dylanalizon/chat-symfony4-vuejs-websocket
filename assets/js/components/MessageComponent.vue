<template>
    <div class="row message">
        <div class="col m10" :class="cls">
            <p>
                <span class="auteur">{{ name }}</span>
                <span class="message-date">{{ ago }}</span>
            </p>
            <p class="content">
                {{ message.content }}
            </p>
        </div>
    </div>
<!--
{{ message.content|nl2br }}
-->
</template>

<script>
    import moment from 'moment'
    moment.locale('fr');

    export default {
        name: 'Message',
        props: {
            message: Object,
            user: Number
        },
        computed: {
            isMe () {
                return this.message.from_user.id === this.user
            },
            cls () {
                let cls = [];
                if(this.isMe){
                    cls.push('offset-m2 right-align')
                }
                return cls;
            },
            name () {
                return this.isMe ? 'Moi' : this.message.from_user.username
            },
            ago () {
                return moment(this.message.created_at).fromNow()
            }
        }
    }
</script>