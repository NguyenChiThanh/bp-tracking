export default [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default },
    { path: '/profile', component: require('./components/Profile.vue').default },
    { path: '/developer', component: require('./components/Developer.vue').default },
    { path: '/users', component: require('./components/Users.vue').default },

    { path: '/stores', component: require('./components/store/Stores.vue').default },
    { path: '/channels', component: require('./components/channel/Channels.vue').default },
    { path: '/positions', component: require('./components/position/Positions.vue').default },
    { path: '/campaigns', component: require('./components/campaign/Campaigns.vue').default },
    { path: '/company', component: require('./components/company/Company.vue').default },


    { path: '*', component: require('./components/NotFound.vue').default }
];
