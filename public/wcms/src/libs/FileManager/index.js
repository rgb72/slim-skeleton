import Store from './store'
import FileManager from './FileManager'

export default {
    install (Vue) {
        Vue.component('FileManager', FileManager)

        Vue.directive('file-management', {
            bind (el, binding, vnode) {
                let context = vnode.context
                let model = vnode.data.directives.find(d => d.name === 'model').expression
                let isMultiple = vnode.data.attrs['is-multiple'] === true

                el.addEventListener('click', () => {
                    Vue.prototype.$fileManager.dispatch('open', {
                        el,
                        context,
                        model,
                        isMultiple
                    })
                })
            }
        })

        Vue.prototype.$fileManager = Store(Vue)
    }
}
