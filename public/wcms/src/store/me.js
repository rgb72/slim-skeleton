import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        me: null,
        modules: [],
        module_info: [],
        currentModule: null
    },
    actions: {
        get ({ commit, state }) {
            return new Promise((resolve, reject) => {
                Vue.http.get('me').then(response => {
                    commit('setMe', response.body)
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        save ({ commit }, data) {
            let url = 'me'

            return new Promise((resolve, reject) => {
                Vue.http({
                    method: 'put',
                    url,
                    body: data
                }).then(response => {
                    commit('resetMe')
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        getModules({ commit, state }) {
            return new Promise((resolve, reject) => {
                Vue.http.get('me/modules').then(response => {
                    commit('setModules', response.body)
                    resolve(state.modules)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        getModuleInfo({ commit, getters }, name) {
            return new Promise((resolve, reject) => {
                if(name === undefined) {
                    reject({message: 'name is not found.'})
                    return
                }

                commit('setCurrentModule', name)

                Vue.http.get('me/modules/' + name).then(response => {
                    commit('setModuleInfo', response.body)
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        }
    },
    mutations: {
        setMe (state, data) {
            state.me = data
        },
        resetMe ({state, commit}) {
            state.me = null
            commit('get')
        },
        setModules (state, modules) {
            state.modules = modules
        },
        setModuleInfo (state, module) {
            let added = false

            if(typeof(module) !== 'object' || module === null) return

            state.module_info.find((info, index) => {
                if(info.name === module.name) {
                    state.module_info[index] = module
                    added = true
                }
            })

            if(!added) state.module_info.push(module)
        },
        setCurrentModule (state, name) {
            state.currentModule = name
        }
    },
    getters: {
        me: state => state.me,
        modules: state => state.modules,
        module_info: state => state.module_info.find(item => item.name === state.currentModule)
    }
}
