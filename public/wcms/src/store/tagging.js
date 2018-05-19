import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        list: {},
        info: []
    },
    actions: {
        getList ({ commit, state }, query = {}) {
            let includes = ['codes', 'patterns']
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0

            return new Promise((resolve, reject) => {
                Vue.http.get('tagging', {
                    params: {
                        ...query,
                        includes: ['codes', 'patterns'],
                        offset,
                        limit
                    }
                }).then(response => {
                    commit('setList', {
                        data: response.body,
                        params: query
                    })
                    resolve(state.list)
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

                let response = getters['info'](id)

                if(response) {
                    resolve(response)
                    return
                }

                Vue.http.get('tagging/' + id, {
                        params: {
                            includes: ['codes', 'patterns']
                        }
                    })
                    .then(response => {
                        commit('setInfo', response.body)
                        resolve(response.body)
                    }).catch(response => {
                        reject(response.body)
                    })
            })
        },
        save ({ commit }, data) {
            let method = data.id ? 'put' : 'post'
            let url = 'tagging' + (data.id ? '/' + data.id : '')

            return new Promise((resolve, reject) => {
                Vue.http({
                    method,
                    url,
                    body: data
                }).then(response => {
                    commit('resetList')
                    if(response.body.id || data.id) commit('resetInfo', response.body.id || data.id)

                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        }
    },
    mutations: {
        setList (state, list) {
            state.list = list.data
            state.params.list = list.params
        },
        resetList (state) {
            state.params.list = null
        },
        setInfo (state, data) {
            let added = false
            state.info.find((info, index) => {
                if(info.id === data.id) {
                    state.info[index] = data
                    added = true
                }
            })

            if(!added) state.info.push(data)
        },
        resetInfo (state, id) {
            state.info = state.info.filter(i => i.id != id)
        }
    },
    getters: {
        list: state => state.list,
        info: (state, getter) => id => {
            return state.info.find(info => info.id === parseInt(id))
        }
    }
}
