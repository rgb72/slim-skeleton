<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="['Custom Tagging']"></title-view>
                </div>
            </div>
            <div class="level-right" v-if="module.action_create">
                <router-link :to="{name: 'customTagging.create'}" class="button is-primary">Create</router-link>
            </div>
        </nav>

        <div class="content has-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Tags</th>
                        <th>Pattern</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in content.data">
                        <td>{{item.name}}</td>
                        <td>{{item.codes.length}}</td>
                        <td>{{item.patterns.length}}</td>
                        <td>
                            <action-view info-name="customTagging.info" :info-id="item.id" :label="item.code" :remove="remove(item.id)"></action-view>
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
            return this.$store.getters['tagging/list']
        },
        module () {
            return this.$store.getters['me/module_info'] || {}
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('tagging/getList', this.$route.query)
        },
        remove (id) {
            return () => {
                this.$swal({
                    title: 'Remove ' + id + ' ?',
                    showCancelButton: true
                }).then(result => {
                    this.$store.dispatch('tagging/remove', id).then(response => {
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
