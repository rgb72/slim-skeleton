<template>
    <div class="field is-horizontal">
        <div class="field-label is-normal">
            <label class="label">{{title}}</label>
        </div>
        <div class="field-body">
            <div class="field">
                <div class="control">
                    <div class="file" :class="{'has-name': value}">
                        <label class="file-label">
                            <input class="file-input" type="file" @change="storeFile" ref="value" :accept="accept">
                            <span class="file-cta">
                                <span class="file-icon">
                                    <i class="fa fa-upload"></i>
                                </span>
                                <span class="file-label">
                                    Choose a file…
                                </span>
                            </span>
                            <span class="file-name" v-if="value">
                                {{value.name}}
                            </span>
                            <button class="button" @click.prevent="resetFileInput($refs.value)" v-if="value">
                                <span class="icon">
                                    <i class="fa fa-times"></i>
                                </span>
                            </button>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        accept: {
            type: String,
            required: false,
            default: null
        },
        value: {
            required: true
        }
    },
    methods: {
        storeFile (event) {
            if (event.target.files.length) {
                this.$emit('input', event.target.files[0])
            }
        },
        resetFileInput (element) {
            element.type = 'text'
            element.type = 'file'
            this.$emit('input', null)
        }
    }
}
</script>
