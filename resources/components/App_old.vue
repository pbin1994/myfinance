<template>
  <div>
 
    <sidebar-menu-akahon :isMenuOpen="this.isMenuOpen" @menuItemClcked="close_menu()"  :isSearch="false" :isUsedVueRouter="true" menuTitle='MyFinance' :menuItems=menuItems menuIcon='' bgColor='#326ced' />
 

 <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
          <div class="container">
              <router-link :to="{name: 'dashboard'}" class="navbar-brand">Dashboard</router-link>
              <router-link :to="{name: 'clients'}" class="navbar-brand">Clients</router-link>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav mr-auto"></ul>
                  <ul class="navbar-nav ml-auto">
                      <li class="nav-link"> App</li>
                  </ul>
              </div>
          </div>
      </nav>


     <span @click="close_menu()">{{ isMenuOpen }}</span>


      <main class="py-4">
          <router-view></router-view>
      </main>
  </div>
</template>
<script>

  import SidebarMenuAkahon from "../components/Sidebar.vue"

  export default {

data() {
    return {
      isMenuOpen: false,
      menuItems: [
      {link: "/dashboard",name: "Консоль", tooltip: "Консоль", icon: "bx-grid-alt" },         
      {
            link: '/transactions',
            name: 'Доходы',
            tooltip: 'Доходы',
            icon: 'bx-ruble',
        },  
        {
            link: '/pending',
            name: 'В ожидании',
            tooltip: 'В ожидании',
            icon: 'bx-timer',
        },         
        {
            link: '/debts',
            name: 'Долги',
            tooltip: 'Долги',
            icon: 'bx-credit-card',
        },     
        {
            link: '/clients',
            name: 'Заказчики',
            tooltip: 'Заказчики',
            icon: 'bx-user',
        },
      {
            link: '/stats',
            name: 'Статистика',
            tooltip: 'Статистика',
            icon: 'bx-pie-chart-alt-2',
        },



          ]
    }
  },
methods: {
  close_menu()
  {
    this.isMenuOpen=false;
    console.log('sad');
  },
},
mounted() {
       axios.get('/api/clients', {withCredentials: true}).then((res) => {
          posts.value = res.data.data;
        })
        .catch((error) => {
          console.log(error.res.data);
        });
  },
components: {
    SidebarMenuAkahon,
  },

  }
</script> 