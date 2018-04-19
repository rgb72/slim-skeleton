<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="['User Management']"></title-view>
                </div>
            </div>
            <div class="level-right" v-if="module.action_create">
                <router-link :to="{name: 'wcms-user.create'}" class="button is-primary">Create</router-link>
            </div>
        </nav>

        <div class="content has-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in content.data">
                        <td>{{item.username}}</td>
                        <td>{{item.name}}</td>
                        <td>
                            <a class="tag is-success" v-if="item.avaliable" @click="unavaliable(item.id)">Avaliable</a>
                            <a class="tag is-danger" v-else @click="avaliable(item.id)">Unavaliable</a>
                        </td>
                        <td>
                            <action-view info-name="wcms-user.info" :info-id="item.id" :remove="remove(item.id)"></action-view>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <pagination-view :pagination="content.pagination"></pagination-view>
    </section>
</template>

<script>
export default {
    computed: {
        content () {
            return this.$store.getters['wcmsUser/list']
        },
        module () {
            return this.$store.getters['me/module_info'] || {}
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('wcmsUser/getList', this.$route.query)
        },
        unavaliable (id) {
            this.$store.dispatch('wcmsUser/unavaliable', id).then(response => {
                this.fetchData()
            }).catch(response => {
                this.$swal({
                    type: 'error',
                    title: response.message
                })
            })
        },
        avaliable (id) {
            this.$store.dispatch('wcmsUser/avaliable', id).then(response => {
                this.fetchData()
            }).catch(response => {
                this.$swal({
                    type: 'error',
                    title: response.message
                })
            })
        },
        remove (id) {
            return () => {
                this.$swal({
                    title: 'Remove ' + id + ' ?',
                    showCancelButton: true
                }).then(result => {
                    this.$store.dispatch('wcmsUser/remove', id).then(response => {
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
