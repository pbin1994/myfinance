import {createRouter, createWebHistory} from 'vue-router'
import Dashboard from '../components/Dashboard.vue'
import Clients from '../components/Clients.vue'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import Transactions from '../components/Transactions.vue'
import Pendings from '../components/Pending.vue'
import Debts from '../components/Debts.vue'
import Config from '../components/Config.vue'
import Stats from '../components/Stats.vue'
import Auth from './Auth.js';


const routes = [
{
  path: "/",
  component: Dashboard,
  redirect: "/dashboard"
},
{
  path: '/dashboard',
  name: 'dashboard',
  component: Dashboard,
  meta: { requiresAuth: true }
},

{
  path: '/clients',
  name: 'clients',
  component: Clients,
  meta: { requiresAuth: true }
},
{
  path: '/transactions',
  name: 'transactions',
  component: Transactions,
  meta: { requiresAuth: true }
},    
{
  path: '/pending',
  name: 'pending',
  component: Pendings,
  meta: { requiresAuth: true }
},   
{
  path: '/debts',
  name: 'debts',
  component: Debts,
  meta: { requiresAuth: true }
},  
{
  path: '/config',
  name: 'config',
  component: Config,
  meta: { requiresAuth: true }
}, 
{
  path: '/stats',
  name: 'stats',
  component: Stats,
  meta: { requiresAuth: true }
},  
{
  path: '/login',
  component: Login,
  name: "Login"
},
{
  path: '/register',
  component: Register,
  name: "Register",
  meta: { requiresAuth: true }
},
{
  path: "/:catchAll(.*)",
  redirect: "/dashboard",
}
]




const router = createRouter({
 history: createWebHistory(),
 mode: 'history',
 routes: routes
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth) ) {
    if (Auth.check()) {
      next();
      return;
    } else {
      router.push('/login');
    }
  } else {
    next();
  }
});

export default router;
