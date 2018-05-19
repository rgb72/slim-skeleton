import Vue from 'vue'

export default {
    namespaced: true,
    state: {
        params: {
            list: null
        },
        list: {},
        info: []
    },
    actions: {
        getList ({ commit, state }, query = {}) {
            let limit = 15
            let offset = query.page ? (query.page - 1) * limit : 0

            return new Promise((resolve, reject) => {
                Vue.http.get('emails', {
                    params: {
                        ...query,
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

                Vue.http.get('emails/' + id, {
                    params: {
                            includes: ['project_locations']
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
            let url = 'emails' + (data.id ? '/' + data.id : '')

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
        },
        unpublished ({commit}, data) {
            let url = 'emails/' + data.id + '/unpublished'

            return new Promise((resolve, reject) => {
                Vue.http.patch(url, {
                    ...data
                }).then(response => {
                    commit('resetList')
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        published ({commit}, data) {
            let url = 'emails/' + data.id + '/published'

            return new Promise((resolve, reject) => {
                Vue.http.patch(url, {
                    ...data
                }).then(response => {
                    commit('resetList')
                    resolve(response.body)
                }).catch(response => {
                    reject(response.body)
                })
            })
        },
        remove ({commit}, id) {
            let url = 'emails/' + id

            return new Promise((resolve, reject) => {
                Vue.http.delete(url).then(response => {
                    commit('resetList')
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
