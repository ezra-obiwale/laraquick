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
            title: 'Crud',
            path: '/controllers/traits/crud'
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
            {
            title: 'Web',
            path: '/controllers/traits/web'
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


window.ga= window.ga || function(){
    (ga.q = ga.q || [] ).push(arguments)
};
ga.l =+ new Date;
ga('create', 'UA-75126891-4', 'auto');
ga('send', 'pageview');
docute.router.afterEach(function (to) {
  ga('set', 'page', to.fullPath);
  ga('send', 'pageview');
});
