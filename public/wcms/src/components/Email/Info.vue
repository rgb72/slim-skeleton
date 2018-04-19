<template>
    <section class="section">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <title-view :breadcrumbs="breadcrumbs"></title-view>
                </div>
            </div>
            <div class="level-right">
                <router-link :to="{name: 'email'}" class="button">Back</router-link>
            </div>
        </nav>

        <div class="content">
            <form @submit.prevent="save">
                <tabs>
                    <tab-pane label="General">
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
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Email</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input type="text" class="input" v-model="data.email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tab-pane>

                    <tab-pane label="Project Location">
                        <section v-for="group in projectGroups" class="section project-location">
                            <h3 class="title">{{group.name}}</h3>
                            <BulmaAccordion accordion animation="spin">
                                <BulmaAccordionItem v-for="brand in group.brands" :key="brand.id">
                                    <p slot="title">{{brand.name}} ({{ProjectLocationTotal(brand.locations)}})</p>
                                    <div slot="content">
                                        <div class="columns is-multiline">
                                            <div class="column is-one-third" v-for="location in brand.locations">
                                                <label class="checkbox">
                                                    <input type="checkbox" :value="location.id" v-model="data.project_locations"> {{location.name}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </BulmaAccordionItem>
                            </BulmaAccordion>
                        </section>
                    </tab-pane>
                </tabs>


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
import { BulmaAccordion, BulmaAccordionItem } from 'vue-bulma-accordion'

export default {
    components: {
        BulmaAccordion,
        BulmaAccordionItem
    },
    data () {
        return {
            data: {
                id: null,
                name: null,
                email: null,
                project_locations: []
            },
            modules: []
        }
    },
    computed: {
        content () {
            return this.$store.getters['email/info'](this.$route.params.id) || {}
        },
        projectGroups () {
            return this.$store.getters['projectGroup/all']
        },
        breadcrumbs () {
            return this.content.id ? ['Setting', 'Email', this.content.id, 'Edit'] : ['Setting', 'Email', 'Edit']
        }
    },
    methods: {
        fetchData () {
            this.$store.dispatch('email/getInfo', this.$route.params.id).then(response => {
                this.data.id = response.id
                this.data.name = response.name
                this.data.email = response.email
                this.data.project_locations = response.project_locations.map(i => i.id)
            })
        },
        getProjectGroup () {
            this.$store.dispatch('projectGroup/getAll', {
                includes: ['brands.locations']
            })
        },
        save () {
            this.$store.dispatch('email/save', this.data).then(response => {
                this.$router.push(this.$route.query.redirect || {name: 'email'})
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
        },
        ProjectLocationTotal(locations) {
            return locations.filter(i => this.data.project_locations.includes(i.id)).length
        }
    },
    mounted () {
        this.fetchData()
        this.getProjectGroup()
    }
}
</script>

<style lang="sass">
.accordion
    .card-header
        .card-header-title
            margin-bottom: 0
</style>

<style lang="sass" scoped>
.section.project-location
    padding: 1.5rem 0
</style>
