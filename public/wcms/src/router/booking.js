const Main = () => import('@/components/Booking/Main')
const Info = () => import('@/components/Booking/Info')

export default [
    {
        path: '/booking',
        name: 'booking',
        component: Main
    },
    {
        path: '/booking/:id',
        name: 'booking.info',
        component: Info
    },
    {
        path: '/booking/create',
        name: 'booking.create',
        component: Info
    },
]
