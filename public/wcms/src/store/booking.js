import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        list: [],
        info: {}
    },
    actions: {
        getList ({ commit }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0
            Vue.http.get('bookings', {
                params: {
                    includes: ['companyType', 'scheduleTemp', 'schedule'],
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
                Vue.http.get('bookings/' + id, {
                        params: {
                            includes: ['ticket', 'companyType', 'address', 'transportations.transportation', 'attachments', 'logs', 'scheduleTemp', 'schedule']
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
        updateStatus ({commit}, params) {
            return new Promise((resolve, reject) => {
                if(params.id === undefined) {
                    reject()
                    return
                }

                Vue.http.patch('bookings/' + params.id , {
                        status: params.status,
                        comment: params.comment
                    })
                    .then(response => {
                        commit('info', response.body)
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        notify ({commit}, id) {
            return new Promise((resolve, reject) => {
                if(id === undefined) {
                    reject()
                    return
                }

                Vue.http.post('bookings/' + id + '/notify')
                    .then(response => {
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        ticket ({commit}, id) {
            return new Promise((resolve, reject) => {
                if(id === undefined) {
                    reject()
                    return
                }

                Vue.http.post('bookings/' + id + '/ticket')
                    .then(response => {
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        save ({ commit }, data) {
            let method = data.id ? 'put' : 'post'
            let url = 'bookings' + (data.id ? '/' + data.id : '')

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
             let url = 'bookings/' + id
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
            state.info = data
        }
    },
    getters: {
        list: state => state.list,
        info: (state, getter) => id => {
            return state.info.id === parseInt(id) ? state.info : {}
        }
    }
}
