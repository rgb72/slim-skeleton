<template>
    <list-view
        :routes="routes"
        :breadcrumbs="breadcrumbs"
        :content="content.data"
        :contentName="contentName"
        :title="title"
        :pagination="contentPagination"
        @edit="edit"
        @remove="remove"
    />
</template>

<script>
import Moment from 'moment'

export default {
    data () {
        return {
            routes: {
                create: 'wcms-user-role.create',
                info: 'wcms-user-role.info'
            },
            breadcrumbs: ['User Role'],
            title: ['Name'],
            contentName: ['name'],
        }
    },
    computed: {
        content () {
            return this.$store.getters['wcmsUserRole/list'] || {}
        },
        contentPagination () {
            return {
                current_page: this.content.current_page,
                from: this.content.from,
                last_page: this.content.last_page,
                per_page: this.content.per_page,
                to: this.content.to,
                total: this.content.total
            }
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('wcmsUserRole/getList', this.$route.query)
        },
        edit (id) {
            this.$router.push({
                name: this.routes.info,
                params: {
                    id
                },
                query: {
                    redirect: this.$route.fullPath
                }
            })
        },
        remove (id) {
            this.$swal({
                title: 'Remove ' + id + ' ?',
                showCancelButton: true
            }).then(result => {
                this.$store.dispatch('wcmsUserRole/remove', id).then(response => {
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
