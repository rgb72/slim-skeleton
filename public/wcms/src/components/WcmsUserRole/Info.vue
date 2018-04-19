<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="breadcrumbs"></title-view>
                </div>
            </div>
            <div class="level-right">
                <a class="button" @click="$router.go(-1)">Back</a>
            </div>
        </nav>

        <div class="content">
            <form @submit.prevent="save">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="text" class="input" id="name" v-model="data.name">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="permission" class="label">Permission</label>
                    </div>
                    <div class="field-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th rowspan="2">Module</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>Export</th>
                                </tr>
                                <tr>
                                    <th><input type="checkbox"></th>
                                    <th><input type="checkbox"></th>
                                    <th><input type="checkbox"></th>
                                    <th><input type="checkbox"></th>
                                    <th><input type="checkbox"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="item in modules" v-if="modules && data.permissions.length">
                                    <tr v-if="!item.child || item.child.length === 0">
                                        <th>{{item.title}}</th>
                                        <td class="has-text-centered">
                                            <input type="checkbox" v-if="item.action_view" v-model="data.permissions[item.id].action_view">
                                        </td>
                                        <td class="has-text-centered">
                                            <input type="checkbox" v-if="item.action_create" v-model="data.permissions[item.id].action_create">
                                        </td>
                                        <td class="has-text-centered">
                                            <input type="checkbox" v-if="item.action_update" v-model="data.permissions[item.id].action_update">
                                        </td>
                                        <td class="has-text-centered">
                                            <input type="checkbox" v-if="item.action_delete" v-model="data.permissions[item.id].action_delete">
                                        </td>
                                        <td class="has-text-centered">
                                            <input type="checkbox" v-if="item.action_export" v-model="data.permissions[item.id].action_export">
                                        </td>
                                    </tr>
                                    <template v-else>
                                        <tr class="is-parent">
                                            <th>{{item.title}}</th>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr v-for="child in item.child" class="is-child">
                                            <th>{{child.title}}</th>
                                            <td class="has-text-centered">
                                                <input type="checkbox" v-if="child.action_view" v-model="data.permissions[child.id].action_view">
                                            </td>
                                            <td class="has-text-centered">
                                                <input type="checkbox" v-if="child.action_create" v-model="data.permissions[child.id].action_create">
                                            </td>
                                            <td class="has-text-centered">
                                                <input type="checkbox" v-if="child.action_update" v-model="data.permissions[child.id].action_update">
                                            </td>
                                            <td class="has-text-centered">
                                                <input type="checkbox" v-if="child.action_delete" v-model="data.permissions[child.id].action_delete">
                                            </td>
                                            <td class="has-text-centered">
                                                <input type="checkbox" v-if="child.action_export" v-model="data.permissions[child.id].action_export">
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
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
                name: null,
                permissions: []
            }
        }
    },
    computed: {
        modules () {
            return this.$store.getters['wcmsModule/modules'].filter(i => i.parent_id == null)
        },
        breadcrumbs () {
            return this.data.id ? ['User Role', this.data.id, 'Edit'] : ['User Role', 'Create']
        }
    },
    methods: {
        getInfo () {
            this.$store.dispatch('wcmsUserRole/getInfo', this.$route.params.id).then(response => {
                this.data.id = response.id
                this.data.name = response.name

                response.permissions.forEach(item => {
                    this.data.permissions[item.module_id] = item
                })
            })
        },
        save () {
            this.$store.dispatch('wcmsUserRole/save', this.data).then(response => {
                this.$router.push({name: 'wcms-user-role'})
            }).catch(response => {
                this.$swal(response.body.message)
            })
        }
    },
    mounted () {
        this.$store.dispatch('wcmsModule/getModules').then(response => {
            response.forEach(item => {
                this.data.permissions[item.id] = {
                    module_id: item.id,
                    action_view: false,
                    action_create: false,
                    action_update: false,
                    action_delete: false,
                    action_export: false
                }
            })

            if(this.$route.params.id) this.getInfo()
        })
    }
}
</script>

<style lang="sass" scoped>
@import ../variables
.table
    thead
        th
            text-align: center
            vertical-align: middle
    tbody
        tr
            background-color: $white-ter
        .is-parent
            background-color: $white-bis
        .is-child
            background-color: $white
            th
                padding-left: 2rem
            + :not(.is-child)
                border-top-width: 2px
</style>
