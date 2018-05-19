<template>
    <header>
        <section class="hero is-bold">
            <div class="hero-head">
                <nav class="navbar" role="navigation" aria-label="main navigation">
                    <div class="navbar-brand">
                        <router-link :to="{ name: 'home' }" class="navbar-item hero-brand">Oxygen4</router-link>
                        <a class="navbar-burger is-hidden-tablet" @click="toggleModule">
                            <span></span>
                            <span></span>
                            <span></span>
                        </a>
                    </div>
                </nav>
            </div>
        </section>
        <notification-view></notification-view>
    </header>
</template>

<script>
import NotificationView from '@/components/Layout/Notification'
export default {
    components: {
        NotificationView
    },
    computed: {
        showModule () {
            return this.$store.getters['wcmsModule/showModule']
        }
    },
    methods: {
        toggleModule () {
            this.$store.dispatch('wcmsModule/toggleModule')
        }
    }
}
</script>

<style lang="sass">
@import ../variables
.hero-brand
    color: $primary !important
    font-size: 2rem
</style>
