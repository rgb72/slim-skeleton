<template>
    <div class="field is-horizontal" v-if="previewImage || defaultImage">
        <div class="field-label is-normal"></div>
        <div class="field-body">
            <div class="field">
                <img :src="previewImage || defaultImage" class="preview-img">
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'file',
        'defaultImage'
    ],
    data () {
        return {
            previewImage: null
        }
    },
    watch: {
        file (to, from) {
            if (to) {
                let reader = new FileReader()
                reader.onload = e => {
                    this.previewImage = e.target.result
                }

                reader.readAsDataURL(to)
            } else {
                this.previewImage = null
            }
        }
    }
}
</script>

<style lang="sass">
.preview-img
    width: auto
    height: auto
    max-width: 20rem
    border: 1px solid #dbdbdb
</style>
