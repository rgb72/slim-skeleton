<template>
    <div class="notification" :class="[{'is-hidden': !isVisible}, {'vanish': vanish}, status]">
        <button class="delete" @click="hide"></button>
        {{message}}
    </div>
</template>

<script>
export default {
    data () {
        return {
            vanish: false
        }
    },
    computed: {
        message () {
            return this.$store.getters['notification/message'] || ''
        },
        status () {
            return this.$store.getters['notification/status'] || ''
        },
        isVisible () {
            return this.$store.getters['notification/isVisible'] || false
        }
    },
    methods: {
        hide () {
            this.vanish = true
            setTimeout(() => {
                this.$store.dispatch('notification/hide')
                this.vanish = false
            }, 500)
        }
    },
    watch: {
        isVisible () {
            if(this.isVisible) {
                setTimeout(() => {
                    this.hide()
                }, 2000)
            }
        }
    }
}
</script>

<style lang="sass" scoped>
.notification
    position: fixed
    top: 0
    width: 100%
    z-index: 10
    transition: opacity .5s ease-in-out

    &.vanish
        opacity: 0
</style>