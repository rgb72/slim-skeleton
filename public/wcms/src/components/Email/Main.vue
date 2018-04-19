<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="['Email']"></title-view>
                </div>
            </div>
            <div class="level-right" v-if="module.action_create">
                <router-link :to="{name: 'email.create'}" class="button is-primary">Create</router-link>
            </div>
        </nav>

        <div class="content has-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in content.data">
                        <td>{{item.name}}</td>
                        <td>{{item.email}}</td>
                        <td>
                            <action-view info-name="email.info" :info-id="item.id" :label="item.id" :remove="remove(item.id)"></action-view>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </section>
</template>

<script>
export default {
    computed: {
        content () {
            return this.$store.getters['email/list']
        },
        module () {
            return this.$store.getters['me/module_info'] || {}
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('email/getList', this.$route.query)
        },
        remove (id) {
            return () => {
                this.$swal({
                    title: 'Remove ' + id + ' ?',
                    showCancelButton: true
                }).then(result => {
                    this.$store.dispatch('email/remove', id).then(response => {
                        this.$swal('Removed')
                        this.fetchData()
                    })
                    .catch(response => {
                        this.$swal({
                            type: 'error',
                            title: response.message
                        })
                    })
                })
            }
        }
    },
    mounted () {
        this.fetchData()
    },
    watch: {
        '$route' (to, from) {
            this.fetchData()
        }
    }
}
</script>
