import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        list: [],
        info: []
    },
    actions: {
        getList ({ commit }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0
            Vue.http.get('wcms-user-roles', {
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

                Vue.http.get('wcms-user-roles/' + id,{
                        params: {
                            includes: ['permissions']
                        }
                    })
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
            let url = 'wcms-user-roles' + (data.id ? '/' + data.id : '')

            return new Promise((resolve, reject) => {
                Vue.http({
                    method,
                    url,
                    body: data
                }).then(response => {
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        remove({commit}, id) {
             let url = 'wcms-user-roles/' + id
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
        list: state => state.list,
        info: (state, getter) => id => {
            return state.info.find(info => info.id === parseInt(id)) || {}
        }
    }
}
