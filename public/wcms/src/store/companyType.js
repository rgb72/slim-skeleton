import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        all: [],
        list: [],
        info: []
    },
    actions: {
        getAll ({ commit }) {
            Vue.http.get('company-types', {
                params: {
                    options: {
                        pagination: false
                    }
                }
            }).then(response => {
                commit('all', response.body)
            }).catch(response => {
                console.error(response)
            })
        },
        getList ({ commit }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0
            Vue.http.get('company-types', {
                params: {
                    ...query,
                    offset,
                    limit
                }
            }).then(response => {
                commit('list', response.body)
            }).catch(response => {
                console.error(response)
            })
        },
        getInfo ({commit}, id) {
            return new Promise((resolve, reject) => {
                if(id === undefined) {
                    reject()
                    return
                }
                Vue.http.get('company-types/' + id)
                    .then(response => {
                        commit('info', response.body)
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        save ({ commit }, data) {
            let method = data.id ? 'put' : 'post'
            let url = 'company-types' + (data.id ? '/' + data.id : '')

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
        remove({commit}, id) {
             let url = 'company-types/' + id
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
        all (state, data) {
            state.all = data
        },
        list (state, data) {
            state.list = data
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
        all: state => state.all,
        list: state => state.list,
        info: (state, getter) => id => {
            return state.info.find(info => info.id === parseInt(id)) || {}
        }
    }
}
