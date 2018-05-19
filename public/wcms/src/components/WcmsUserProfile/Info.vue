<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="['User Profile']"></title-view>
                </div>
            </div>
        </nav>

        <div class="content">
            <form @submit.pervent="save">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="username" class="label">Username</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input type="text" class="input" id="username" v-model="content.username" readonly>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="password" class="label">Password</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input type="password" class="input" id="password" v-model="data.password">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input type="text" class="input" id="name" v-model="data.name">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="email" class="label">E-mail</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input type="email" class="input" id="email" v-model="data.email">
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control is-expanded">
                        <button class="button is-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
export default {
    data () {
        return {
            data: {
                password: null,
                name: null,
                email: null
            }
        }
    },
    computed: {
        content () {
            return this.$store.getters['me/me'] || {}
        }
    },
    methods: {
        save () {
            this.$store.dispatch('me/save', this.data).then(response => {
                this.$swal('Updated', null, 'success')
            })
        }
    },
    mounted () {
        this.$store.dispatch('me/get').then(response => {
            this.data.name = response.name
            this.data.email = response.email
        })
    }
}
</script>
