<template>
    <div class="advance-input">
        <div class="field">
            <div class="control">
                <div class="select" v-if="!showAdvance">
                    <select v-model="localMain">
                        <option :value="list[listValue]" v-for="list in lists">{{list[listName]}}</option>
                    </select>
                </div>
                <div class="wrapper-input advance" v-else>
                    <div class="field">
                        <label class="label">Desktop Site</label>
                        <div class="control is-fullwidth">
                            <div class="select">
                                <select v-model="localDesktop">
                                    <option :value="list[listValue]" v-for="list in lists">{{list[listName]}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Mobile Site</label>
                        <div class="control is-fullwidth">
                            <div class="select">
                                <select v-model="localMobile">
                                    <option :value="list[listValue]" v-for="list in lists">{{list[listName]}}</option>
                                </select>
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
export default {
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
        },
        lists: {
            type: Array,
            required: true
        },
        listName: {
            type: String,
            default: 'name',
            required: true
        },
        listValue: {
            type: String,
            default: 'value',
            required: true
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
    updated () {
        if(this.advanceId === null) {
            this.showAdvance = this.localDesktop !== this.localMobile
            this.advanceId = 'advance_' + Math.floor(Math.random() * (1000 - 0 + 1))
        }
    }
}
</script>
