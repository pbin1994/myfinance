<template>
  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>
  <div class="login_form_ct">  
    <div class="login_form">
        <h1>Вход</h1>
        <form  @submit.prevent="login">
            <div>
                <input type="email" v-model="user.email" placeholder="Email">
            </div>
            <div>
                <input type="password" v-model="user.password" placeholder="Пароль">
            </div>
            <div class="error_ct" v-if="this.error"> {{ error }} </div>
            <button>Войти</button>
        </form>
    </div>
</div>


</template>

<script>
    import Auth from './Auth.js';
    import { OrbitSpinner } from 'epic-spinners'
    export default {
        data() {
            return {
                error:'',
                fulscreen_loading:false,
                user: {
                    email: '',
                    password: '',
                }
            };
        },
        mounted() {
            if (Auth.check()) this.$router.push('/dashboard')
        },
    components: {
      OrbitSpinner
  },
  methods: {
    login() {
        this.fulscreen_loading=true;
        axios.post('/api/login', this.user)
        .then(({data}) => {
                        Auth.login(data.access_token,data.user); //set local storage
                        //this.$router.push('/dashboard');
                    })
        .catch((error) => {
            this.fulscreen_loading=false;
            this.error=error.response.data.error;
        });
    }
}
}
</script>