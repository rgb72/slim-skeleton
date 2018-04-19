<template>
    <aside class="menu" :class="{'is-active': showModule}">
        <ul class="menu-list">
            <li>
                <router-link :to="{ name: 'home' }" active-class="is-active" exact>Welcome</router-link>
            </li>
            <li v-for="item in modules">
                <a v-if="item.child.length" @click="toggleMenu" :class="{'is-active': isChildActive(item.name)}">
                    {{item.title}}
                    <span class="icon">
                        <i class="fa fa-caret-down"></i>
                    </span>
                </a>
                <router-link v-else :to="{ name: item.name }" active-class="is-active">{{item.title}}</router-link>
                <ul v-if="item.child.length">
                    <li v-for="child in item.child">
                        <router-link :to="{ name: child.name }" active-class="is-active">{{child.title}}</router-link>
                    </li>
                </ul>
            </li>
            <li>
                <router-link :to="{ name: 'wcms-user-profile'}" active-class="is-active">User Profile</router-link>
            </li>
            <li>
                <a @click="logout">Logout</a>
            </li>
        </ul>
    </aside>
</template>

<script>
export default {
    methods: {
        isChildActive (name) {
            let path = this.$route.matched[0].path.split('/').filter(item => item)
            return path[0] === name
        },
        toggleMenu (event) {
            event.target.classList.toggle('is-active')
        },
        logout () {
            this.$store.dispatch('auth/logout')
            this.$router.push({path: '/login'})
        },
        getRealModuleName (name) {
            return name.lastIndexOf('.') === -1 ? name : name.substr(0, name.lastIndexOf('.'))
        }
    },
    computed: {
        modules () {
            return this.$store.getters['me/modules']
        },
        showModule () {
            return this.$store.getters['wcmsModule/showModule']
        }
    },
    mounted () {
        this.$store.dispatch('me/getModules')
        if(this.$route.name) this.$store.dispatch('me/getModuleInfo', this.getRealModuleName(this.$route.name))
    },
    watch: {
        '$route' (to, from) {
            this.$store.dispatch('me/getModuleInfo', this.getRealModuleName(to.name))
        }
    }
}
</script>

<style lang="sass">
@import ../variables
.menu
    +mobile
        position: absolute
        left: 0
        right: 0
        top: 4rem
        background-color: $white-ter
        z-index: 10
        box-shadow: 0px 0px 10px 1px rgba(10, 10, 10, 0.1)
        &:not(.is-active)
            display: none

.menu-list
    a
        &:not(.is-active)
            + ul
                display: none
        &.is-active
            .fa
                transform: rotate(180deg)

        .icon
            float: right
</style>
