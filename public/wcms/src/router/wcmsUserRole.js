const Main = () => import('@/components/WcmsUserRole/Main')
const Info = () => import('@/components/WcmsUserRole/Info')

export default [
    {
        path: '/wcms-user-role',
        name: 'wcms-user-role',
        component: Main
    },
    {
        path: '/wcms-user-role/create',
        name: 'wcms-user-role.create',
        component: Info
    },
    {
        path: '/wcms-user-role/:id',
        name: 'wcms-user-role.info',
        component: Info
    }
]
