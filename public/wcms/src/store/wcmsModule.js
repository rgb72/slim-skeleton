import Vue from 'vue'
import jwtDecode from 'jwt-decode'
import Moment from 'moment'

export default {
    namespaced: true,
    state: {
        modules: [],
        showModule: false
    },
    actions: {
        getModules({ commit, state }) {
            return new Promise((resolve, reject) => {
                Vue.http.get('wcms-modules', {
                    params: {
                        includes: ['child'],
                        sorting: 'ordering',
                        options: {
                            pagination: false
                        }
                    }
                }).then(response => {
                    commit('modules', response.body)
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        toggleModule({ commit }) {
            commit('toggleModule')
        },
        setCurrentModule({ commit }, name) {
            commit('setCurrentModule', name)
        }
    },
    mutations: {
        showModule (state) {
            state.showModule = true
        },
        hideModule (state) {
            state.showModule = false
        },
        toggleModule (state) {
            state.showModule = !state.showModule
        },
        modules (state, modules) {
            state.modules = modules
        }
    },
    getters: {
        showModule: state => state.showModule,
        modules: state => state.modules
    }
}
