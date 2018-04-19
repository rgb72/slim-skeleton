import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import auth from './auth'
import notification from './notification'
import wcmsModule from './wcmsModule'
import wcmsUser from './wcmsUser'
import wcmsUserRole from './wcmsUserRole'
import me from './me'
import email from './email'
import tagging from './tagging'
import booking from './booking'
import companyType from './companyType'
import survey from './survey'

export default new Vuex.Store({
    modules: {
        auth,
        notification,
        wcmsModule,
        wcmsUser,
        wcmsUserRole,
        me,
        email,
        tagging,
        booking,
        companyType,
        survey
    }
})
