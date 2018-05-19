<template>
    <div class="advance-input">
        <div class="field">
            <div class="control">
                <pre v-highlightjs="localMain" v-if="!showAdvance">
                    <code :class="type" contenteditable @blur="updateMain"></code>
                </pre>
                <div class="wrapper-input advance" v-else>
                    <div class="field">
                        <label class="label">Desktop Site</label>
                        <div class="control is-fullwidth">
                            <pre v-highlightjs="localDesktop">
                                <code :class="type" contenteditable @blur="updateDesktop"></code>
                            </pre>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Mobile Site</label>
                        <div class="control is-fullwidth">
                            <pre v-highlightjs="localMobile">
                                <code :class="type" contenteditable @blur="updateMobile"></code>
                            </pre>
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
export default {
    props: {
        type: {
            required: true,
            default: 'html'
        },
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
        if(this.advanceId === null) {
            this.showAdvance = this.localDesktop !== this.localMobile
            this.advanceId = 'advance_' + Math.floor(Math.random() * (1000 - 0 + 1))
        }
    }
}
</script>

<style lang="sass">
@import ~highlight.js/styles/dracula
</style>
