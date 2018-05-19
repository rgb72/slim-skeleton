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
            <form @submit.prevent="save" autocomplete="off">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="username" class="label">Username</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="text" class="input" id="username" v-model="data.username" :readonly="content.username !== undefined">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="password" class="label">Password</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="password" class="input" id="password" v-model="data.password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label for="avaliable" class="label">Avaliable</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" id="avaliable" v-model="data.avaliable" :true-value="1" :false-value="0">
                            </div>
                        </div>
                    </div>
                </div>

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
                        <label for="email" class="label">Email</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="email" class="input" id="email" v-model="data.email">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field is-horizontal" v-if="userRoles.length">
                    <div class="field-label is-normal">
                        <label for="role_id" class="label">User Role</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select id="role_id" v-model="data.role_id">
                                        <option :value="undefined" disabled hidden>Select Role</option>
                                        <option v-for="role in userRoles" :value="role.id">
                                            {{role.name}}
                                        </option>
                                    </select>
                                </div>
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
                                    <th>Module</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>Export</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="item in modules">
                                    <tr v-if="!item.child || item.child.length === 0">
                                        <th>{{item.title}}</th>
                                        <td class="has-text-centered">
                                            <span class="icon" v-if="item.action_view">
                                                <i class="fa" :class="userRolePermission(item.id).action_view ? 'fa-check' : 'fa-times'"></i>
                                            </span>
                                            <template v-else>&nbsp;</template>
                                        </td>
                                        <td class="has-text-centered">
                                            <span class="icon" v-if="item.action_create">
                                                <i class="fa" :class="userRolePermission(item.id).action_create ? 'fa-check' : 'fa-times'"></i>
                                            </span>
                                            <template v-else>&nbsp;</template>
                                        </td>
                                        <td class="has-text-centered">
                                            <span class="icon" v-if="item.action_update">
                                                <i class="fa" :class="userRolePermission(item.id).action_update ? 'fa-check' : 'fa-times'"></i>
                                            </span>
                                            <template v-else>&nbsp;</template>
                                        </td>
                                        <td class="has-text-centered">
                                            <span class="icon" v-if="item.action_delete">
                                                <i class="fa" :class="userRolePermission(item.id).action_delete ? 'fa-check' : 'fa-times'"></i>
                                            </span>
                                            <template v-else>&nbsp;</template>
                                        </td>
                                        <td class="has-text-centered">
                                            <span class="icon" v-if="item.action_export">
                                                <i class="fa" :class="userRolePermission(item.id).action_export ? 'fa-check' : 'fa-times'"></i>
                                            </span>
                                            <template v-else>&nbsp;</template>
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
                                                <span class="icon" v-if="child.action_view">
                                                    <i class="fa" :class="userRolePermission(child.id).action_view ? 'fa-check' : 'fa-times'"></i>
                                                </span>
                                                <template v-else>&nbsp;</template>
                                            </td>
                                            <td class="has-text-centered">
                                                <span class="icon" v-if="child.action_create">
                                                    <i class="fa" :class="userRolePermission(child.id).action_create ? 'fa-check' : 'fa-times'"></i>
                                                </span>
                                                <template v-else>&nbsp;</template>
                                            </td>
                                            <td class="has-text-centered">
                                                <span class="icon" v-if="child.action_update">
                                                    <i class="fa" :class="userRolePermission(child.id).action_update ? 'fa-check' : 'fa-times'"></i>
                                                </span>
                                                <template v-else>&nbsp;</template>
                                            </td>
                                            <td class="has-text-centered">
                                                <span class="icon" v-if="child.action_delete">
                                                    <i class="fa" :class="userRolePermission(child.id).action_delete ? 'fa-check' : 'fa-times'"></i>
                                                </span>
                                                <template v-else>&nbsp;</template>
                                            </td>
                                            <td class="has-text-centered">
                                                <span class="icon" v-if="child.action_export">
                                                    <i class="fa" :class="userRolePermission(child.id).action_export ? 'fa-check' : 'fa-times'"></i>
                                                </span>
                                                <template v-else>&nbsp;</template>
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
                id: null,
                username: null,
                password: null,
                name: null,
                email: null,
                role_id: null
            }
        }
    },
    computed: {
        content () {
            return this.$store.getters['wcmsUser/info'](this.$route.params.id) || {}
        },
        userRoles () {
            return this.$store.getters['wcmsUser/userRoles'] || []
        },
        userRole () {
            return this.$store.getters['wcmsUser/userRole'](this.data.role_id) || {}
        },
        userRolePermissions () {
            return this.userRole.permissions || []
        },
        breadcrumbs () {
            return this.content.id ? ['User Management', this.content.id, 'Edit'] : ['User Management', 'Create']
        },
        modules () {
            return this.$store.getters['wcmsModule/modules'].filter(i => i.parent_id == null)
        }
    },
    methods: {
        getInfo () {
            this.$store.dispatch('wcmsUser/getInfo', this.$route.params.id).then(response => {
                this.data = {
                    id: response.id,
                    username: response.username,
                    name: response.name,
                    email: response.email,
                    role_id: response.role_id,
                    avaliable: response.avaliable
                }
            })
        },
        save () {
            this.$store.dispatch('wcmsUser/save', this.data).then(response => {
                this.$router.push({name: 'wcms-user'})
            }).catch(response => {
                this.$swal(response.message)
            })
        },
        userRolePermission (id) {
            let permissions = this.userRole.permissions || []
            return permissions.find(i => i.id == id) || {}
        }
    },
    mounted () {
        if(this.$route.params.id) this.getInfo()
        this.$store.dispatch('wcmsUser/getUserRoles')
        this.$store.dispatch('wcmsModule/getModules')
    }
}
</script>

<style lang="sass" scoped>
.is-child
    th
        padding-left: 2.5rem
</style>
