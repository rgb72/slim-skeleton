<template>
    <div id="file-manager" :class="{'modal': modal, 'is-active': isOpen}">
        <div class="modal-background" v-if="modal"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">
                    File Manager
                </p>
                <button class="modal-close is-large" aria-label="close" @click="close()"></button>
            </header>
            <section class="modal-card-body">
                <nav class="breadcrumb" aria-label="breadcrumbs">
                    <ul>
                        <li v-for="breadcrumb in pathInfo.breadcrumbs">
                            <a @dblclick="get(breadcrumb.path)">
                                <template v-if="breadcrumb.name == ''">
                                    <span class="icon">
                                        <i class="fa fa-home" aria-hidden="true"></i>
                                    </span>
                                </template>
                                <template v-else>{{breadcrumb.name}}</template>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="content">
                    <div class="file-uploading" v-if="fileUploading">
                        <div class="columns is-mobile" v-for="item in fileUploading">
                            <div class="column is-2">{{ item.name }}</div>
                            <div class="column">
                                <progress class="progress is-info" :value="item.current_part" :max="item.total_parts"></progress>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-multiline is-mobile">
                        <div class="column is-one-third-desktop is-half-mobile" v-for="(item, index) in pathInfo.lists">
                            <div class="file is-boxed has-name"
                                :class="{'is-info': selectedIndex.includes(index)}"
                                @contextmenu.prevent="$refs.ctxMenu.open($event, {$event, item, index})"
                                @click="selectItem($event, index)"
                                @click.ctrl="multipleSelectItem($event, index)"
                                @click.meta="multipleSelectItem($event, index)"
                                @mouseenter="hoverIndex = index"
                                @mouseleave="hoverIndex = null"
                                >
                                <div class="file-label">
                                    <div class="file-cta" @dblclick="doubleClickHandler(item)">
                                        <span class="file-icon">
                                            <i class="fa" :class="getIcon(item.type, item.extension)"></i>
                                        </span>
                                    </div>
                                    <div class="file-name" v-if="!editable(index)"
                                        @dblclick="renameHandler($event, index)"
                                        :class="{'is-editing': editable(index)}"
                                        >{{item.name}}</div>
                                    <input v-else type="text" class="input" :value="item.name" @keyup.enter="rename($event, index)" @blur="rename($event, index)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <div class="item">
                    <button class="button is-success" @click="select" v-if="hasSelect">Select</button>
                    <button class="button" @click="close" v-if="hasCancel">Cancel</button>
                </div>
                <div class="item">
                    <input type="file" multiple class="is-hidden" ref="fileInput" @change="upload">
                    <button class="button" @click="newFile">
                        <span class="icon">
                            <i class="fa fa-file-o"></i>
                        </span>
                    </button>
                    <button class="button" @click="newFolder">
                        <span class="icon">
                            <i class="fa fa-folder-open-o"></i>
                        </span>
                    </button>
                </div>
            </footer>
        </div>

        <context-menu ref="ctxMenu" @ctx-open="onCtxOpen" @ctx-cancel="onCtxCancel">
            <li class="context-menu-select-item"><p class="is-size-6">{{selectedName}}</p></li>
            <li v-if="selectedIndex.length <= 1" @click="renameHandler(ctx.locals.$event, ctx.locals.index)">Rename</li>
            <li v-if="selectedIndex.length == 1" @click="clipboard">Copy to clipboard</li>
            <li @click="remove">Delete</li>
        </context-menu>
    </div>
</template>

<script>
import ContextMenu from 'vue-context-menu'

export default {
    props: {
        modal: {
            required: false,
            default: true
        },
        open: {
            required: false,
            default: false
        },
        hasSelect: {
            required: false,
            default: true
        },
        hasCancel: {
            required: false,
            default: true
        }
    },
    components: {
        ContextMenu
    },
    data () {
        return {
            hoverIndex: null,
            selectedIndex: [],
            editing_index : null,
            renaming: false,
            ctx: {
                locals: {}
            }
        }
    },
    computed: {
        isOpen () {
            return this.$fileManager.getters.isOpen || this.open
        },
        pathInfo () {
            return this.$fileManager.getters.pathInfo
        },
        uploadPath () {
            return this.$fileManager.getters.uploadPath
        },
        fileUploading () {
            return this.$fileManager.getters.fileUploading
        },
        selectedName () {
            if(this.selectedIndex.length === 1) return this.pathInfo.lists[this.selectedIndex[0]].name
            else 'Select ' + this.selectedIndex.length + ' items.'
        }
    },
    methods: {
        get (path = null) {
            this.selectedIndex = []
            this.$fileManager.dispatch('get', path)
            this.$fileManager.dispatch('setCurrentPath', path)
        },
        doubleClickHandler (item) {
            if(item.type === 'dir') {
                this.get(item.path)
            } else {
                this.$fileManager.dispatch('setModelValue', item.url).then(response => {
                    this.$fileManager.dispatch('close')
                })
            }
        },
        select () {
            let files = this.pathInfo.lists.filter((v, i) => this.selectedIndex.includes(i)).map(v => v.url)
            this.$fileManager.dispatch('setModelValue', files).then(response => {
                this.$fileManager.dispatch('close')
            }).catch(response => {
                alert('error')
            })
        },
        onCtxOpen (locals) {
            if(!this.selectedIndex.includes(locals.index)) this.selectedIndex = [locals.index]
            this.ctx.locals = locals
        },
        onCtxCancel () {
            this.ctx.locals = {}
        },
        renameHandler (e, index) {
            this.editing_index = index

            let range = document.createRange()
            let selection = window.getSelection()
            range.setStart(e.target, 1)
            range.collapse(true)
            selection.removeAllRanges()
            selection.addRange(range)
        },
        rename (e, index) {
            if(this.renaming) return false

            this.editing_index = null
            let name = e.target.value

            let path = this.pathInfo.lists[index].path

            this.renaming = true
            this.$fileManager.dispatch('rename', {path, name}).then(() => {
                this.get()
                this.renaming = false
            }).catch(() => {
                this.renaming = false
            })

            return e.which != 13
        },
        editable (index) {
            return index === this.editing_index
        },
        close () {
            this.$fileManager.dispatch('close')
        },
        getIcon (type, extension) {
            if(type === 'dir') return 'fa-folder-o'

            extension = extension.toLowerCase()

            switch(extension) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-file-image-o'
                    break

                case 'xls':
                case 'xlsx':
                case 'csv':
                    return 'fa-file-excel-o'
                    break

                case 'doc':
                case 'docx':
                case 'odt':
                    return 'fa-file-word-o'
                    break

                case 'ppt':
                case 'pptx':
                    return 'fa-file-powerpoint-o'
                    break

                case 'pdf':
                    return 'fa-file-pdf-o'
                    break

                case 'zip':
                case 'rar':
                case '7z':
                case 'tar':
                case 'gz':
                case 'z':
                    return 'fa-file-archive-o'
                    break

                case 'txt':
                case 'rtf':
                case 'tex':
                    return 'fa-file-text-o'
                    break

                default:
                    return 'fa-file'
                    break
            }
        },
        isImage (extension) {
            extension = extension.toLowerCase()
            return ['jpg', 'jpeg' ,'png', 'gif'].includes(extension)
        },
        selectItem (e, index) {
            if(index !== this.editing_index) this.editing_index = null
            if(!e.ctrlKey && !e.metaKey) this.selectedIndex = [index]
        },
        multipleSelectItem (e, index) {
            if(this.editing_index) return
            this.selectedIndex.push(index)
        },
        newFile () {
            this.$refs.fileInput.click()
        },
        upload (el) {
            let files = el.target.files

            if(!files.length) return

            Array.from(Array(files.length).keys()).map(i => {
                this.$fileManager.dispatch('upload', files[i])
            })
        },
        newFolder () {
            this.$fileManager.dispatch('newFolder').then(() => {
                this.get()
            })
        },
        clipboard () {
            let index = this.selectedIndex[0]
            let url = this.pathInfo.lists[index].url

            let input = document.createElement('input')
            input.style.position = 'fixed'
            input.style.top = 0
            input.style.left = 0
            input.style.backgroud = 'transparent'
            input.value = url
            document.body.appendChild(input)

            input.select()
            console.log(document.execCommand('copy'))
            document.body.removeChild(input)
        },
        remove () {
            let count = 0
            this.selectedIndex.forEach(index => {
                let path = this.pathInfo.lists[index].path
                this.$fileManager.dispatch('remove', path).then(response => {
                    ++count
                    if(count === this.selectedIndex.length) {
                        this.selectedIndex = []
                        this.get()
                    }
                }).catch(response => {
                    this.$swal(response.body)
                    ++count
                    if(count === this.selectedIndex.length) {
                        this.selectedIndex = []
                        this.get()
                    }
                })
            })
        }
    },
    mounted () {
        if(this.isOpen) this.get()
    },
    watch: {
        isOpen (to, from) {
            if(to) this.get()
        }
    }
}
</script>

<style lang="sass" scoped>
@import ~bulma/sass/utilities/_all

.breadcrumb
    background-color: $light

.ctx-menu
    li
        padding-left: 1em
        &:hover
            background-color: $light
    .context-menu-select-item
        border-bottom: 1px solid $grey
        font-weight: bold
        background-color: $light

.is-editing
    background-color: $light
    text-overflow: unset

.file
    .file-label,
    .file-cta,
    .file-name
        width: 100%
        max-width: unset

.modal-card-foot
    justify-content: space-between
    +mobile
        flex-direction: column-reverse
        .item
            + .item
                margin-bottom: 1rem
</style>
