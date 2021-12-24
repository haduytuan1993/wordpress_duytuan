import Vue from 'vue'
import Router from 'vue-router'

function route (path, file, name, children, redirect_url) {
  var data = {
    exact: false,
    path,
    name,
    children,
    component: require("./pages/" + file + ".vue").default
  };

  if( typeof redirect_url != 'undefined' ) {

    data.redirect = redirect_url;
  }

  return data;
}

Vue.use(Router)

const router = new Router({
  base: __dirname,
  mode: 'hash',
  scrollBehavior: () => ({ y: 0 }),
  routes: [
    route('/login', 'Login', 'login'),
    route('/error', 'Error', 'error'),

    // path, file(*.vue), name, children

    route('/', 'Main', null, [
      route('/dashboard', 'Dashboard'),
      route('/tests_assigned', 'Assigned'),
      route('/tests_results', 'Results'),
      route('/tests_results/:resource/:id', 'Result'),
      route('/settings', 'Settings'),
      route('/logout', 'Logout')
    ], '/tests_results')

    // Global redirect for 404
    // { path: '*', redirect: '/error', query: {code: 404, message: 'Page Not Found.'} }
  ]
})

router.beforeEach((to, from, next) => {
  global.store.dispatch('checkPageTitle', to.path)
  
  if (typeof ga !== 'undefined') {
    ga('set', 'page', to.path)
    ga('send', 'pageview')
  }
  next()
})



export default router