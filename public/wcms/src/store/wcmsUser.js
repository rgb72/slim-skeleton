import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        list: [],
        info: [],
        userRoles: []
    },
    actions: {
        getList ({ commit, state }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0

            return new Promise((resolve, reject) => {
                Vue.http.get('wcms-users', {
                    params: {
                        ...query,
                        offset,
                        limit,
                    }
                }).then(response => {
                    commit('list', {
                        data: response.body,
                        params: query
                    })
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        getInfo ({ commit, getters }, id) {
            return new Promise((resolve, reject) => {
                if(id === undefined) {
                    reject({message: 'id is not found.'})
                    return
                }

                Vue.http.get('wcms-users/' + id)
                    .then(response => {
                        commit('info', response.body)
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        getUserRoles ({ commit, state }) {
            return new Promise((resolve, reject) => {
                Vue.http.get('wcms-user-roles', {
                    params: {
                        options: {
                            pagination: false
                        },
                        includes: 'permissions'
                    }
                }).then(response => {
                    commit('setUserRoles', response.body)
                    resolve(state.all)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        save ({ commit }, data) {
            let method = data.id ? 'put' : 'post'
            let url = 'wcms-users' + (data.id ? '/' + data.id : '')

            return new Promise((resolve, reject) => {
                Vue.http({
                    method,
                    url,
                    body: data
                }).then(response => {
                    commit('info', response.body)
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        unavaliable ({commit}, id) {
            let url = 'wcms-users/' + id + '/unavaliable'

            return new Promise((resolve, reject) => {
                Vue.http.patch(url).then(response => {
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        avaliable ({commit}, id) {
            let url = 'wcms-users/' + id + '/avaliable'

            return new Promise((resolve, reject) => {
                Vue.http.patch(url).then(response => {
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        remove ({commit}, id) {
            let url = 'wcms-users/' + id

            return new Promise((resolve, reject) => {
                Vue.http.delete(url).then(response => {
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        }
    },
    mutations: {
        setUserRoles (state, data) {
            state.userRoles = data
        },
        list (state, list) {
            state.list = list.data
        },
        info (state, data) {
            let added = false
            state.info.find((info, index) => {
                if(info.id === data.id) {
                    state.info[index] = data
                    added = true
                }
            })

            if(!added) state.info.push(data)
        }
    },
    getters: {
        list: state => state.list,
        info: (state, getter) => id => {
            return state.info.find(info => info.id === parseInt(id))
        },
        userRoles: state => state.userRoles,
        userRole: (state, getter) => id => {
            return state.userRoles.find(role => role.id === parseInt(id))
        }
    }
}
