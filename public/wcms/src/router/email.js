const Main = () => import('@/components/Email/Main')
const Info = () => import('@/components/Email/Info')

export default [
    {
        path: '/email',
        name: 'email',
        component: Main
    },
    {
        path: '/email/create',
        name: 'email.create',
        component: Info
    },
    {
        path: '/email/:id',
        name: 'email.info',
        component: Info
    }
]
