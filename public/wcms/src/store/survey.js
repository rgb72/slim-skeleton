import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        list: []
    },
    actions: {
        getList ({ commit }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0
            Vue.http.get('survey', {
                params: {
                    ...query,
                    sorting: '-created_at',
                    offset,
                    limit
                }
            }).then(response => {
                commit('list', response.body)
            }).catch(response => {
                console.error(response)
            })
        },
        exportAll ({commit}, query = {}) {
            return new Promise((resolve, reject) => {
                query = Object.assign({}, query)
                Vue.http.get('survey/reports', {
                    params: {
                        ...query
                    }
                }).then(response => {
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        }
    }, // end Action

    mutations: {
        list (state, data) {
            state.list = data
        }
    },
    getters: {
        list: state => state.list
    }
}
