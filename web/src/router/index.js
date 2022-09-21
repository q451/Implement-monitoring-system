import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        // {
        //     path: '/',
        //     redirect: '/dashboard'
        // },
        {
            path: '/dashboard',
            component: () => import(/* webpackChunkName: "home" */ '../components/common/Home.vue'),
            meta: { title: '自述文件' },
            children: [
                {
                    path: '/dashboard',
                    component: () => import(/* webpackChunkName: "dashboard" */ '../components/page/Dashboard.vue'),
                    meta: { title: '系统首页' }
                },
                {
                    path: '/table',
                    component: () => import(/* webpackChunkName: "table" */ '../components/page/BaseTable.vue'),
                    meta: { title: '监护详情' }
                },
                {
                    path: '/notify',
                    component: () => import(/* webpackChunkName: "tabs" */ '../components/page/Notify.vue'),
                    meta: { title: '通知信息' }
                },
                {
                    path: '/changeInfo',
                    component: () => import(/* webpackChunkName: "form" */ '../components/page/ChangInfo.vue'),
                    meta: { title: '修改信息' }
                },
                {
                    // 富文本编辑器组件
                    path: '/error404',
                    component: () => import(/* webpackChunkName: "error404" */ '../components/page/404.vue'),
                    meta: { title: 'error404' }
                },
                {
                    // 图片上传组件
                    path: '/changePassword',
                    component: () => import(/* webpackChunkName: "upload" */ '../components/page/ChangePassword.vue'),
                    meta: { title: '修改密码' }
                },
                {
                    // vue-schart组件
                    path: '/charts',
                    component: () => import(/* webpackChunkName: "chart" */ '../components/page/BaseCharts.vue'),
                    meta: { title: '人员录入' }
                },
                {
                    path: '/video',
                    component: () => import(/* webpackChunkName: "i18n" */ '../components/page/Video.vue'),
                    meta: { title: '实时监护' }
                },
                {
                    path: '/about',
                    component: () => import(/* webpackChunkName: "donate" */ '../components/page/About.vue'),
                    meta: { title: '关于我们' }
                }
            ]
        },
        {
            path: '/',
            component: () => import(/* webpackChunkName: "login" */ '../components/page/Login.vue'),
            meta: { title: '登录' }
        },
        {
            path: '/register',
            component: () => import(/* webpackChunkName: "login" */ '../components/page/Register.vue'),
            meta: { title: '注册' }
        },
        {
            path: '*',
            redirect: '/404'
        }
    ]
});
