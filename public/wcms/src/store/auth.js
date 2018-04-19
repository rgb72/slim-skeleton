import Vue from 'vue'
import jwtDecode from 'jwt-decode'
import Moment from 'moment'

export default {
    namespaced: true,
    state: {
        token: localStorage.token || null,
        payload: {},
        setHeader: false
    },
    actions: {
        login ({ commit, dispatch }, credential) {
            return new Promise((resolve, reject) => {
                return Vue.http.post('auth/login', credential).then(response => {
                    if(response.body.token && response.body.token.length) {
                        commit('token', response.body.token)
                        dispatch('setAuth')
                        dispatch('decodeToken').then(() => {
                            resolve(response.body)
                        }).catch(() =>{
                            reject({'message': 'Something wrongs. Please try again'})
                        })
                    } else {
                        reject({'message': 'Something wrongs. Please try again'})
                    }
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        setAuth ({ commit, state }) {
            Vue.http.headers.common['Authorization'] = 'Bearer ' + state.token
            commit('header', true)
        },
        unsetAuth ({ commit }) {
            Vue.http.headers.common['Authorization'] = null
            commit('header', false)
        },
        refreshToken ({ commit, state, dispatch }) {
            return new Promise((resolve, reject) => {
                Vue.http.get('auth/token').then(response => {
                    commit('token', response.body.token)
                    dispatch('setAuth')
                    resolve(response.body.token)
                }).catch(response => {
                    dispatch('logout')
                    reject(response.body)
                })
            })
        },
        checkToken ({ commit, state, dispatch }) {
            return new Promise((resolve, reject) => {
                dispatch('decodeToken').then(payload => {
                    let now = Moment().unix()
                    if(now < payload.nbf || now > payload.exp) {
                        dispatch('logout')
                        reject()
                    } else {
                        resolve()
                    }
                }).catch(() => {
                    dispatch('logout')
                    reject()
                })
            })
        },
        decodeToken({ commit, state, dispatch }) {
            return new Promise((resolve, reject) => {
                if(state.token === null) {
                    reject()
                    return
                }

                try {
                    let payload = jwtDecode(state.token)
                    commit('payload', payload)
                    resolve(payload)
                } catch(e) {
                    reject()
                }
            })
        },
        deleteKey({ commit }, key) {
            Vue.http.delete('auth/' + key)
        },
        logout({ commit, state, dispatch }) {
            if(state.payload.key) dispatch('deleteKey', state.payload.key)
            dispatch('unsetAuth')
            localStorage.removeItem('token')
            commit('token', null)
            commit('payload', {})
        }
    },
    mutations: {
        token (state, token) {
            if(token !== null) localStorage.setItem('token', token)
            state.token = token
        },
        payload (state, payload) {
            state.payload = payload
        },
        header (state, status) {
            state.setHeader = status
        }
    },
    getters: {
        isLogin: state => state.token !== null,
        token: state => state.token,
        user: state => state.payload.user,
        role: state => state.payload.role,
        permission: state => state.payload.permission,
        isSetHeader: state => state.setHeader
    }
}
