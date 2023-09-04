<template>
    <sidebar-menu-akahon  v-if="loggedUser" :isMenuOpen="this.isMenuOpen" @menuItemClcked="close_menu()"  @button-exit-clicked="logout()"  :isSearch="false" :isUsedVueRouter="true" menuTitle='MyFinance' :menuItems=menuItems menuIcon='' :profileName='this.auth.user.name' :profileRole='this.auth.user.email' bgColor='#326ced' />
    <div class="container" id="main_container">
        <router-view> </router-view>
    </div>

</template>

<script>
    import Auth from './Auth.js';
    import SidebarMenuAkahon from "../components/Sidebar.vue"
    export default  {
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
                {
                    link: '/config',
                    name: 'Настройки',
                    tooltip: 'Настройки',
                    icon: 'bx-cog',
                },
                ],
                loggedUser: this.auth.user
            };
        },
        mounted() {

        },
         components: {
            SidebarMenuAkahon,
          },
        methods: {
              close_menu()
              {
                this.isMenuOpen=false;
              },
            logout() {
                axios.post('/api/logout')
                .then(({data}) => {
                    Auth.logout(); //reset local storage
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>