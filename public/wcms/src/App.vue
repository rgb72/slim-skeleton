<template>
    <main id="app" :class="{'hero is-fullheight': !isLogin}">
        <nprogress-container></nprogress-container>
        <header-view v-if="isLogin"></header-view>
        <div class="columns is-marginless">
            <div class="is-2 column" v-if="isLogin">
                <module-view></module-view>
            </div>
            <div class="column" :class="{'is-paddingless is-fullheight': !isLogin, 'is-10': isLogin}">
                <router-view></router-view>
                <file-manager></file-manager>
            </div>
        </div>
    </main>
</template>

<script>
import Vue from 'vue'

import Moment from 'moment'
Vue.prototype.Moment = Moment

import NprogressContainer from 'vue-nprogress/src/NprogressContainer'

import { Tabs, TabPane } from 'vue-bulma-tabs'
const HeaderView = () => import('@/components/Layout/Header')
const ModuleView = () => import('@/components/Layout/Module')
const TitleView = () => import('@/components/Layout/Title.vue')
const PaginationView = () => import('@/components/Layout/Pagination.vue')
const ActionView = () => import('@/components/Layout/Action.vue')
const AdvanceInput = () => import('@/components/Layout/AdvanceInput')
const MetaView = () => import('@/components/Layout/Meta.vue')
const FileUpload = () => import('@/components/Layout/FileUpload')
const FileUploadPreview = () => import('@/components/Layout/FileUpload/Preview')
const Datepicker = () => import('@/components/Layout/Datepicker')

const ListView = () => import('@/components/Layout/List.vue')

Vue.component('Tabs', Tabs)
Vue.component('TabPane', TabPane)
Vue.component('TitleView', TitleView)
Vue.component('PaginationView', PaginationView)
Vue.component('ActionView', ActionView)
Vue.component('AdvanceInput', AdvanceInput)
Vue.component('MetaView', MetaView)
Vue.component('FileUpload', FileUpload)
Vue.component('FileUploadPreview', FileUploadPreview)
Vue.component('Datepicker', Datepicker)
Vue.component('ListView', ListView)

import store from './store'

import VueQuillEditor from 'vue-quill-editor'
import { quillRedefine } from 'vue-quill-editor-upload'

const quillOptions = quillRedefine({
    theme: 'snow',
    toolOptions: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [ 'bold', 'italic', 'underline', 'strike' ],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'super' }, { 'script': 'sub' }],
        [ 'blockquote', 'code-block' ],
        [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
        [ 'direction', { 'align': [] }],
        [ 'link', 'image', 'video', 'formula' ],
        [ 'clean' ]
    ],
    uploadConfig: {
        action: '/wcms/api/uploads',
        name: 'file',
        accept: 'image/*',
        header (xhr, formData) {
            xhr.setRequestHeader('Authorization', `Bearer ${store.getters['auth/token']}`)
        },
        res (response) {
            return response.url
        }
    }
})
Vue.use(VueQuillEditor, quillOptions)

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'

import katex from 'katex'
window.katex = katex

import FileManager from '@/libs/FileManager'
Vue.use(FileManager)

export default {
    components: {
        NprogressContainer,
        HeaderView,
        ModuleView
    },
    name: 'app',
    computed: {
        isLogin () {
            return this.$store.getters['auth/isLogin']
        }
    }
}
</script>

<style lang="sass">
@import ./components/variables
@import ./components/utilities
@import ~bulma
@import ~bulma-switch/switch

html
    height: 100%

body
    background-color: #f2f2f2
    min-height: 100%
    overflow: scroll

    #app
        height: 100%

        > .columns
            height: 100%

.has-table
    +mobile
        overflow: scroll

.search
    .search-item
        padding: 0 1rem
        text-align: left
        align-item: center

        .search-label
            color: $grey-dark
            display: block
            font-weight: 700
            font-size: 0.9rem

.toggle-tag
    .tag
        margin-right: .4em
        margin-bottom: .4em
        &:last-child
            margin-right: 0

.multi-checkbox
    padding-top: 0.375em
    .checkbox
        margin-right: 0.5rem
        &:last-child
            margin-right: 0

.drop-area
  display: flex
  height: 10rem
  margin-bottom: 1rem
  border: 0.3rem dashed #F5F5F5
  .drop-area-text
    display: flex
    text-align: center
    align-items: center
    justify-content: center
    width: 100%

.level
    padding: .5rem 1rem
    background-color: white
    &.head
        box-shadow: 0px 0px 10px 1px rgba(10, 10, 10, 0.1)

    + .content
        background-color: white
        padding: 1rem
        box-shadow: 0px 0px 10px 1px rgba(10, 10, 10, 0.1)

.vue-bulma-tabs
    .tabs
        ul
            margin: 0
        .tab-list
            li
                + li
                    margin-top: 0
    .tab-content
        margin: .75rem 2.25rem !important
        +mobile
            margin: .25rem 1rem !important

+mobile
    .section
        padding: 1rem .75rem

.ql-container
    max-height: 75vh
    overflow: scroll

.ql-editor
    // put editor style
</style>


<style lang="sass" scoped>
.column.is-fullheight
    height: 100vh

.nprogress-custom-parent
    position: fixed
    width: 100%
    height: 5px
    top: 0
    left: 0
    z-index: 10
</style>
