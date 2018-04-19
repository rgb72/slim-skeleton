<template>
    <section class="section">
        <div class="container">
            <div class="content columns">
                <div class="column oxygen is-4">
                    <h1 class="title is-2">Oxygen 4</h1>
                    <p class="subtitle">
                        Oxygen is a content management system developed and owned by RGB72 Co.,Ltd.<br>
                        The system is only provided for RGB72 clients and partners.<br>
                        Do not use without permission.
                    </p>
                </div>
                <div class="column is-8">
                    <div class="client">
                        <h3 class="subtitle">Content Management for</h3>
                        <h2 class="title is-3">Meiji-museum</h2>
                    </div>
                    <form @submit.prevent="login">
                        <div class="field">
                            <input type="text" class="input" placeholder="Username" v-model="credential.username">
                        </div>
                        <div class="field has-addons has-addons-right">
                            <div class="control is-expanded">
                                <input type="password" class="input" placeholder="Password" v-model="credential.password" v-show="!showPassword">
                                <input type="text" class="input" placeholder="Password" v-model="credential.password" v-show="showPassword">
                            </div>
                            <div class="control">
                                <a class="button is-primary" @click="showPassword = !showPassword">
                                    <template v-if="showPassword">Hide</template>
                                    <template v-else>Show</template>
                                </a>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control is-expanded">
                                <button class="button is-primary">Login</button>
                            </div>
                        </div>
                    </form>
                    <p class="help">
                        Don't have the password?<br>
                        Please contact your administration.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            credential: {
                username: '',
                password: ''
            },
            showPassword: false
        }
    },
    methods: {
        login() {
            this.$store.dispatch('auth/login', this.credential).then(() => {
                this.$router.push(this.$route.query.redirect || '/')
            }).catch(response => {
                this.$swal(response.message)
            })
        }
    }
}
</script>

<style lang="sass" scoped>
@import ../variables
.section
    height: 100%
    display: flex
    align-items: center
    justify-content: center
    background: url(./background.jpg) no-repeat center bottom/cover

    .container
        max-width: 40rem

        .oxygen
            .title
                color: $primary
            .subtitle
                font-size: 1rem
                margin-top: -1rem

        .client
            .title
                text-transform: uppercase
                font-weight: bold
</style>
