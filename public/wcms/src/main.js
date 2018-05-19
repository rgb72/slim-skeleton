import Vue from 'vue'
import VueResource from 'vue-resource'
import VueSweetAlert from 'vue-sweetalert'
import { sync } from 'vuex-router-sync'
import NProgress from 'vue-nprogress'
import VueConfig from 'vue-config'
import VueLodash from 'vue-lodash'
import lodash from 'lodash'

import App from './App'
import router from './router'
import store from './store'

Vue.use(VueResource)
Vue.use(VueSweetAlert)
Vue.use(NProgress)

Vue.http.options.root = '/wcms/api'
Vue.config.productionTip = false

Vue.use(VueConfig, {
    uploadUrl: Vue.http.options.root + '/uploads'
})

Vue.use(VueLodash, lodash)

sync(store, router)

const nprogress = new NProgress({ parent: '.nprogress-container' })

router.beforeEach((to, from, next) => {
    let goToLogin = redirect => {
        next({
            path: '/login',
            query: {
                redirect
            }
        })
    }

    store.dispatch('auth/setAuth')

    if(store.getters['auth/token'])
        store.dispatch('auth/refreshToken')

    if(to.matched.some(record => record.meta.authentication !== false)) {
        store.dispatch('me/get').then().catch(() => {
            store.dispatch('auth/logout')
            goToLogin(to.fullPath)
        })
    }

    store.dispatch('auth/checkToken').then(() => {
        if(to.matched.some(record => record.meta.authentication !== false && store.getters['auth/isLogin'] !== true)) {
            goToLogin(to.fullPath)
        } else {
            next()
        }
    }).catch(() => {
        store.dispatch('auth/logout')
        if(to.matched.some(record => record.meta.authentication !== false))
            goToLogin(to.fullPath)
        else
            next()
    })
})

const vm = new Vue({
    el: '#app',
    router,
    store,
    nprogress,
    created() {
        if(this.$store.getters['auth/isLogin'] !== true) return
        this.$store.dispatch('auth/setAuth')
    },
    render: h => h(App)
})
