<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="breadcrumbs"></title-view>
                </div>
            </div>
            <div class="level-right">
                <router-link :to="{name: 'customTagging'}" class="button">Back</router-link>
            </div>
        </nav>

        <div class="content">
            <form @submit.prevent="save">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Name</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input type="text" class="input" v-model="data.name">
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="title">Tags</h3>
                <template v-for="(code, index) in data.codes">
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">#{{index+1}}</label>
                        </div>
                        <div class="field-body is-justify-content-end">
                            <a class="delete" @click="removeCode(index)"></a>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Code</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <textarea class="textarea" v-model="code.code"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Position</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <div class="select">
                                        <select v-model="code.position">
                                            <option value="head">Head</option>
                                            <option value="body_start">Body Top</option>
                                            <option value="body_end">Body End</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </template>
                <div class="field">
                    <div class="control has-text-centered is-expanded">
                        <a class="button" @click="addCode()">
                            <span class="icon">
                                <i class="fa fa-plus"></i>
                            </span>
                            <span>Add Tag</span>
                        </a>
                    </div>
                </div>
                <hr>
                <h3 class="title">Pattern</h3>
                <template v-for="(pattern, index) in data.patterns">
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">#{{index+1}}</label>
                        </div>
                        <div class="field-body is-justify-content-end">
                            <a class="delete" @click="removePattern(index)"></a>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Include</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input type="text" class="input" v-model="pattern.include_pattern">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Exclude</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input type="text" class="input" v-model="pattern.exclude_pattern">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </template>
                <div class="field">
                    <div class="control has-text-centered is-expanded">
                        <a class="button" @click="addPattern()">
                            <span class="icon">
                                <i class="fa fa-plus"></i>
                            </span>
                            <span>Add Pattern</span>
                        </a>
                    </div>
                </div>
                <hr>

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
                name: null,
                codes: [],
                patterns: []
            },
            modules: []
        }
    },
    computed: {
        content () {
            return this.$store.getters['tagging/info'](this.$route.params.id) || {}
        },
        breadcrumbs () {
            return this.content.id ? ['Setting', 'Custom Tagging', this.content.id, 'Edit'] : ['Setting', 'Custom Tagging', 'Edit']
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('tagging/getInfo', this.$route.params.id).then(response => {
                this.data.id = response.id
                this.data.name = response.name
                this.data.codes = response.codes
                this.data.patterns = response.patterns
            })
        },
        save () {
            this.$store.dispatch('tagging/save', this.data).then(response => {
                this.$router.push(this.$route.query.redirect || {name: 'customTagging'})
            }).catch(response => {
                this.$swal(response.message)
            })
        },
        addCode () {
            this.data.codes.push({
                code: null,
                position: null
            })
        },
        removeCode(index) {
            this.$swal({
                title: 'Are you sure?',
                showCancelButton: true
            }).then(isConfirm => {
                if(isConfirm) {
                    this.data.codes.splice(index, 1)
                }
            })
        },
        addPattern () {
            this.data.patterns.push({
                include_pattern: null,
                exclude_pattern: null
            })
        },
        removePattern(index) {
            this.$swal({
                title: 'Are you sure?',
                showCancelButton: true
            }).then(isConfirm => {
                if(isConfirm) {
                    this.data.patterns.splice(index, 1)
                }
            })
        }
    },
    mounted () {
        this.fetchData()
    }
}
</script>
