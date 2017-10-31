docute.init({
    nav: [
        {
        title: 'Home',
        path: '/'
        },
        {
        title: 'Controllers',
        type: 'dropdown',
        items: [
            {
            title: 'Traits',
            type: 'label'
            },
            {
            title: 'Api',
            path: '/controllers/traits/api'
            },
            {
            title: 'Attachable',
            path: '/controllers/traits/attachable'
            },
            {
            title: 'PassThrough',
            path: '/controllers/traits/pass-through'
            },
            {
            title: 'Pivotable',
            path: '/controllers/traits/pivotable'
            },
            {
            title: 'Referer',
            path: '/controllers/traits/referer'
            },
            {
            title: 'Respond',
            path: '/controllers/traits/respond'
            },
        ]
        },
        {
        title: 'Helpers',
        type: 'dropdown',
        items: [
            {
            title: 'Http',
            path: '/helpers/http'
            }
        ]
        }
    ]
});