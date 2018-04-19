const Main = () => import('@/components/Tagging/Main')
const Info = () => import('@/components/Tagging/Info')

export default [
    {
        path: '/settings/custom-tagging',
        name: 'custom-tagging',
        component: Main
    },
    {
        path: '/settings/custom-tagging/create',
        name: 'custom-tagging.create',
        component: Info
    },
    {
        path: '/settings/custom-tagging/:id',
        name: 'custom-tagging.info',
        component: Info
    }
]
