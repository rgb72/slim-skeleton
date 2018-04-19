<template>
    <div class="action">
        <tooltip :label="viewLabel" placement="top">
            <a class="button is-info" v-if="hasView && isView" @click="viewCallback">
                <span class="icon">
                    <i class="fa fa-eye"></i>
                </span>
            </a>
        </tooltip>
        <tooltip :label="editLabel" placement="top">
            <a class="button is-warning" v-if="hasEdit && isEdit" @click="editCallback">
                <span class="icon">
                    <i class="fa fa-pencil"></i>
                </span>
            </a>
        </tooltip>
        <tooltip :label="deleteLabel" placement="top">
            <a class="button is-danger" v-if="hasDelete && isDelete" @click="remove">
                <span class="icon">
                    <i class="fa fa-times"></i>
                </span>
            </a>
        </tooltip>
        <template v-if="hasMove && isEdit">
            <!-- <tooltip label="move to first" placement="top">
                <a class="button" @click="move('first')">
                    <span class="icon">
                        <i class="fa fa-angle-double-up"></i>
                    </span>
                </a>
            </tooltip> -->
            <tooltip label="move up" placement="top">
                <a class="button is-success" @click="move('up')">
                    <span class="icon">
                        <i class="fa fa-angle-up"></i>
                    </span>
                </a>
            </tooltip>
            <tooltip label="move down" placement="top">
                <a class="button is-success" @click="move('down')">
                    <span class="icon">
                        <i class="fa fa-angle-down"></i>
                    </span>
                </a>
            </tooltip>
           <!--  <tooltip label="move to last" placement="top">
                <a class="button" @click="move('last')">
                    <span class="icon">
                        <i class="fa fa-angle-double-down"></i>
                    </span>
                </a>
            </tooltip> -->
        </template>
    </div>
</template>

<script>
import Tooltip from '@/libs/tooltip'
export default {
    components: {
        Tooltip
    },
    props: {
        label: {
            required: false,
            default: null
        },
        infoName: {
            type: String,
            required: false,
            default: null
        },
        infoId: {
            required: false,
            default: null
        },
        hasView: {
            type: Boolean,
            required: false,
            default: true
        },
        hasEdit: {
            type: Boolean,
            required: false,
            default: true
        },
        hasDelete: {
            type: Boolean,
            required: false,
            default: true
        },
        view: {
            type: Function,
            required: false
        },
        edit: {
            type: Function,
            required: false
        },
        remove: {
            type: Function,
            required: false
        },
        move: {
            type: Function,
            required: false
        }
    },
    computed: {
        module () {
            return this.$store.getters['me/module_info'] || {}
        },
        isView () {
            return this.module.action_view && !this.module.action_update
        },
        isEdit () {
            return this.module.action_update
        },
        isDelete () {
            return this.module.action_delete
        },
        hasMove () {
            return this.move
        },
        viewLabel () {
            return 'view ' + (this.label || this.infoId)
        },
        editLabel () {
            return 'edit ' + (this.label || this.infoId)
        },
        deleteLabel () {
            return 'delete ' + (this.label || this.infoId)
        }
    },
    methods: {
        viewCallback () {
            if(this.view === undefined) this.goToInfo()
            else this.view()
        },
        editCallback () {
            if(this.edit === undefined) this.goToInfo()
            else this.edit()
        },
        goToInfo () {
            if(this.infoName === null || this.infoId === null) return

            this.$router.push({
                name: this.infoName,
                params: {
                    id: this.infoId
                },
                query: {
                    redirect: this.$route.fullPath
                }
            })
        }
    }
}
</script>
