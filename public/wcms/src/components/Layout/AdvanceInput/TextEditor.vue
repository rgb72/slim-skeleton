<template>
    <div class="advance-input">
        <div class="field">
            <div class="control">
                <template v-if="!showAdvance"​>
                    <quill-editor v-model="localMain" :id="'main_' + Math.floor(Math.random() * (1000 - 0 + 1))"></quill-editor>
                    <div class="quill-code" v-highlightjs="localMain">
                        <code class="html" contenteditable @blur="updateMain"></code>
                    </div>
                </template>
                <div class="wrapper-input advance" v-else>
                    <div class="field">
                        <label class="label">Desktop Site</label>
                        <div class="control is-fullwidth">
                            <quill-editor v-model="localDesktop" :id="'desktop_' + Math.floor(Math.random() * (1000 - 0 + 1))"​></quill-editor>
                            <div class="quill-code" v-highlightjs="localDesktop">
                                <code class="html" contenteditable @blur="updateDesktop"></code>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Mobile Site</label>
                        <div class="control is-fullwidth">
                            <quill-editor v-model="localMobile" :id="'mobile_' + Math.floor(Math.random() * (1000 - 0 + 1))"​></quill-editor>
                            <div class="quill-code" v-highlightjs="localMobile">
                                <code class="html" contenteditable @blur="updateMobile"></code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="field is-narrow" v-if="hasAdvance">
            <div class="control">
                <input :id="advanceId" type="checkbox" class="switch" v-model="showAdvance">
                <label :for="advanceId">Advance</label>
            </div>
        </div>
    </div>
</template>

<script>
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

export default {
    components: {
        quillEditor
    },
    props: {
        main: {
            required: true
        },
        desktop: {
            default: undefined,
            required: false
        },
        mobile: {
            default: undefined,
            required: false
        }
    },
    data () {
        return {
            showAdvance: false,
            advanceId: null
        }
    },
    computed: {
        localMain: {
            get () {
                return this.main
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:desktop', value)
                this.$emit('update:mobile', value)
            }
        },
        localDesktop: {
            get () {
                return this.desktop
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:desktop', value)
            }
        },
        localMobile: {
            get () {
                return this.mobile
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:mobile', value)
            }
        },
        hasAdvance () {
            return this.desktop !== undefined && this.mobile !== undefined
        }
    },
    methods: {
        updateMain (event) {
            this.localMain = event.target.innerText
        },
        updateDesktop (event) {
            this.localDesktop = event.target.innerText
        },
        updateMobile (event) {
            this.localMobile = event.target.innerText
        }
    },
    updated () {
        console.log(this.desktop, this.mobile, this.main)
        if(this.advanceId === null) {
            this.showAdvance = this.localDesktop !== this.localMobile
            this.advanceId = 'advance_' + Math.floor(Math.random() * (1000 - 0 + 1))
        }
    }
}
</script>
