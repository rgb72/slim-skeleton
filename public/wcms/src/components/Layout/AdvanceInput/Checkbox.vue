<template>
    <div class="advance-input">
        <div class="field">
            <div class="control">
                <div class="wrapper-input checkbox" v-if="!showAdvance">
                    <template v-for="(list, index) in lists">
                        <input :id="advanceId + index" type="checkbox" class="switch is-rounded is-outlined" v-model="localMain" :value="list[listValue]">
                        <label :for="advanceId + index">{{list[listName]}}</label>
                    </template>
                </div>
                <div class="wrapper-input checkbox advance" v-else>
                    <div class="field">
                        <label class="label">Desktop Site</label>
                        <div class="control is-fullwidth">
                            <template v-for="(list, index) in lists">
                                <input :id="advanceId + index + 'desktop'" type="checkbox" class="switch is-rounded is-outlined" v-model="localDesktop" :value="list[listValue]">
                                <label :for="advanceId + index + 'desktop'">{{list[listName]}}</label>
                            </template>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Mobile Site</label>
                        <div class="control is-fullwidth">
                            <template v-for="(list, index) in lists">
                                <input :id="advanceId + index + 'mobile'" type="checkbox" class="switch is-rounded is-outlined" v-model="localMobile" :value="list[listValue]">
                                <label :for="advanceId + index + 'mobile'">{{list[listName]}}</label>
                            </template>
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
