import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import Login from '@/components/Auth/Login'

import EmailRoute from '@/router/email'
import CustomTaggingRoute from '@/router/customTagging'

import WcmsUserRoleRoute from '@/router/wcmsUserRole'
import WcmsUserRoute from '@/router/wcmsUser'
import WcmsUserProfileRoute from '@/router/wcmsUserProfile'
import BookingRoute from '@/router/booking'
import SurveyRoute from '@/router/survey'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        ...EmailRoute,
        ...CustomTaggingRoute,
        ...WcmsUserRoleRoute,
        ...WcmsUserRoute,
        ...WcmsUserProfileRoute,
        ...BookingRoute,
        ...SurveyRoute,
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                authentication: false
            }
        }
    ]
})
