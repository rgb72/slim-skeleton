import Vuex from 'vuex'
import DotProp from 'dot-prop'
import UploadFile from './uploadFile'

export default Vue => {
    Vue.use(Vuex)

    const store = new Vuex.Store({
        state: {
            isOpen: false,
            pathInfo: [],
            currentPath: '/',
            config: {},
            fileUploading: []
        },
        actions: {
            open ({ commit }, config) {
                commit('config', config)
                commit('open')
            },
            close ({ commit }) {
                commit('config', {})
                commit('close')
            },
            setCurrentPath ({ commit }, path = false) {
                if(path) commit('set_current_path', path)
            },
            get ({ commit, state }, path = false) {
                let url = 'file-managements' + (path || state.currentPath)
                Vue.http.get(url).then(response => {
                    let fileManagements = response.body
                    fileManagements.lists = fileManagements.lists.sort((a, b) => {
                        let nameA = a.name.toUpperCase()
                        let nameB = b.name.toUpperCase()

                        if (nameA < nameB) return -1
                        if (nameA > nameB) return 1
                        return 0
                    })

                    commit('path_info', fileManagements)
                })
            },
            upload ({ commit, dispatch, getters }, file) {
                let sliceSize = 1024 * 1024

                commit('file_uploading', {
                    name: file.name,
                    total_parts: Math.ceil(file.size/sliceSize),
                    current_part: 0
                })

                let sendChuckFile = (file, start, sliceSize, currentPart = 0) => {
                    let end = start + sliceSize
                    if(end > file.size) end = file.size

                    let sliced = file.mozSlice ? file.mozSlice.bind(file)(start, end) :
                                file.webkitSlice ? file.webkitSlice.bind(file)(start, end) :
                                file.slice.bind(file)(start, end)

                    ++currentPart

                    let formData = new FormData()

                    formData.append('multipart', true)
                    formData.append('filename', file.name)
                    formData.append('total_size', file.size)
                    formData.append('part_size', sliceSize)
                    formData.append('total_parts', Math.ceil(file.size/sliceSize))
                    formData.append('current_part', currentPart)
                    formData.append('start', start)
                    formData.append('end', end)
                    formData.append('file', sliced)

                    Vue.http.post(this.getters.uploadPath, formData).then(response => {
                        commit('file_part_uploaded', file.name)
                        if(end < file.size) {
                            start = end
                            sendChuckFile(file, start, sliceSize, currentPart)
                        } else {
                            dispatch('get')
                        }
                    }).catch(response => {
                        // alert(response)
                    })
                }

                sendChuckFile(file, 0, sliceSize)
            },
            newFolder ({ commit, state }, path) {
                return new Promise((resolve, reject) => {
                    let url = 'file-managements' + (path || state.currentPath) + '/new%20folder'
                    url = url.replace(/\/\//g, '/');
                    Vue.http.put(url).then(response => {
                        resolve(response)
                    }).catch(response => {
                        reject(response)
                    })
                })
            },
            rename ({ commit }, params) {
                return new Promise((resolve, reject) => {
                    let url = 'file-managements' + params.path
                    Vue.http.patch(url, {
                        name: params.name
                    }).then(response => {
                        resolve(response)
                    }).catch(response => {
                        reject(response)
                    })
                })
            },
            remove ({ commit, state }, path) {
                return new Promise((resolve, reject) => {
                    let url = 'file-managements' + path
                    Vue.http.delete(url).then(response => {
                        resolve(response)
                    }).catch(response => {
                        reject(response)
                    })
                })
            },
            setModelValue({ commit, state }, value) {
                return new Promise((resolve, reject) => {
                    if(!state.config.isMultiple) {
                        if(Array.isArray(value)) {
                            value = value[0]
                        }

                        DotProp.set(state.config.context, state.config.model, value)
                    } else {
                        let contextValue = DotProp.get(state.config.context, state.config.model)
                        if(!Array.isArray(value)) value = [value]
                        for (let i in value) {
                            contextValue.push(value[i])
                        }

                        DotProp.set(state.config.context, state.config.model, contextValue)
                    }

                    state.config.el.dispatchEvent(new Event('change'))
                    resolve()
                })
            }
        },
        mutations: {
            open (state) {
                state.isOpen = true
            },
            close (state) {
                state.isOpen = false
            },
            path_info (state, data) {
                state.pathInfo = data
            },
            config (state, config) {
                state.config = config
            },
            file_uploading (state, file) {
                state.fileUploading.push(file)
            },
            file_part_uploaded (state, name) {
                let index = state.fileUploading.findIndex(file => file.name === name)
                state.fileUploading[index].current_part += 1

                if(state.fileUploading[index].current_part === state.fileUploading[index].total_parts) {
                    state.fileUploading.splice(index, 1)
                }
            },
            set_current_path (state, path) {
                state.currentPath = path
            }
        },
        getters: {
            isOpen: state => state.isOpen,
            pathInfo: state => state.pathInfo,
            currentPath: state => state.currentPath,
            uploadPath: state => 'file-managements' + (state.currentPath || ''),
            fileUploading: state => state.fileUploading
        }
    })

    return store
}
