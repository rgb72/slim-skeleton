export default {
    namespaced: true,
    state: {
        status: '',
        message: '',
        visible: false
    },
    actions: {
        setMessage({commit}, message) {
            commit('setMessage', message)
        },
        setStatus({commit}, status) {
            switch (status) {
                case 'primary':
                    commit('setStatus', 'is-primary')
                    break

                case 'info':
                    commit('setStatus', 'is-info')
                    break

                case 'success':
                    commit('setStatus', 'is-success')
                    break

                case 'warn':
                case 'warning':
                    commit('setStatus', 'is-warning')
                    break

                case 'error':
                case 'danger':
                    commit('setStatus', 'is-danger')
                    break

                default:
                    commit('setStatus', '')
                    break
            }
        },
        show({commit}) {
            commit('setVisible', true)
        },
        hide({commit}) {
            commit('setVisible', false)
        }
    },
    mutations: {
        setMessage (state, message) {
            state.message = message
        },
        setStatus (state, status) {
            state.status = status
        },
        setVisible (state, visible) {
            state.visible = visible
        }
    },
    getters: {
        isVisible: state => state.visible,
        message: state => state.message,
        status: state => state.status
    }
}
