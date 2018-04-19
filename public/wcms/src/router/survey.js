const Main = () => import('@/components/Survey/Main')
// const Info = () => import('@/components/Survey/Info')

export default [
    {
        path: '/survey',
        name: 'survey',
        component: Main
    }
    // ,
    // {
    //     path: '/survey/:id',
    //     name: 'survey.info',
    //     component: Info
    // },
    // {
    //     path: '/survey/create',
    //     name: 'survey.create',
    //     component: Info
    // },
]
