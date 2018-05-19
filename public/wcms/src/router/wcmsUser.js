const Main = () => import('@/components/WcmsUser/Main')
const Info = () => import('@/components/WcmsUser/Info')

export default [
    {
        path: '/wcms-user',
        name: 'wcms-user',
        component: Main
    },
    {
        path: '/wcms-user/create',
        name: 'wcms-user.create',
        component: Info
    },
    {
        path: '/wcms-user/:id',
        name: 'wcms-user.info',
        component: Info
    }
]
