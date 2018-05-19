<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="breadcrumbs" />
                </div>
                <slot name="filter"></slot>
            </div>
            <div class="level-right" v-if="module.action_create">
                <router-link :to="{name: routes.create}" class="button is-primary">Create</router-link>
            </div>
        </nav>

        <div class="content has-table">
            <table class="table">
                <thead>
                    <slot name="contentName">
                        <tr>
                            <th v-for="item in title">{{item}}</th>
                            <th v-if="withActions">Action</th>
                        </tr>
                    </slot>
                </thead>
                <tbody>
                    <slot name="content">
                        <tr v-for="item in content">
                            <td v-for="n in contentName">
                                <template v-if="typeof n === 'string'">
                                    {{item[n]}}
                                </template>
                                <template v-else-if="typeof n === 'object'">
                                    <template v-if="n.type === Boolean">
                                        <span class="tag" :class="{'is-success': item[n.name], 'is-danger': !item[n.name]}">
                                            {{item[n.name] ? 'active' : 'inactive'}}
                                        </span>
                                    </template>
                                    <template v-else>
                                        {{n.parser(item[n.name])}}
                                    </template>
                                </template>
                            </td>
                            <td v-if="withActions">
                                <action-view
                                    :info-name="routes.info"
                                    :info-id="item[keyName]"
                                    @view="$emit('view', item[keyName])"
                                    @edit="$emit('edit', item[keyName])"
                                    @remove="$emit('remove', item[keyName])"
                                    @moveUp="$emit('moveUp', item[keyName])"
                                    @moveDown="$emit('moveDown', item[keyName])"
                                    @moveTop="$emit('moveTop', item[keyName])"
                                    @moveLast="$emit('moveLast', item[keyName])"
                                    />
                            </td>
                        </tr>
                    </slot>
                </tbody>
            </table>
        </div>

        <pagination-view v-if="pagination" :pagination="pagination" />
    </section>
</template>

<script>
export default {
    props: {
        routes: {
            type: Object,
            required: true
        },
        breadcrumbs: {
            type: Array,
            required: true
        },
        content: {
            required: false,
            default: () => {
                return []
            }
        },
        contentName: {
            type: Array,
            required: false,
            default: () => {
                return []
            }
        },
        title: {
            type: Array,
            required: true
        },
        pagination: {
            required: false
        },
        withActions: {
            type: Boolean,
            default: true,
            required: false
        },
        keyName: {
            type: String,
            default: 'id',
            required: false
        }
    },
    computed: {
        module () {
            return this.$store.getters['me/module_info'] || {}
        }
    }
}
</script>
