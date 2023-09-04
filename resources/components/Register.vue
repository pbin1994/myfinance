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
        <h1>Регистрация</h1>
        <form  @submit.prevent="register">
            <div>
                <input type="text" v-model="user.name" placeholder="Имя">
            </div>
            <div>
                <input type="text" v-model="user.email" placeholder="Email">
            </div>
            <div>
                <input type="password" v-model="user.password" placeholder="Пароль">
            </div>
            <div>
                <input type="password" v-model="user.password_confirmation" placeholder="Подтвердить пароль">
            </div>
            <div class="error_ok_ct" v-if="this.error_ok"> {{ error_ok }} </div>
            <button>Зарегистрировать</button>
        </form>
    </div>
</div>
</template>

<script>
    import { OrbitSpinner } from 'epic-spinners'
    export default {
        data() {
            return {
                error_ok:'',
                fulscreen_loading:false,
                user: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                }
            };
        },
        components: {
          OrbitSpinner
      },
        methods: {
            register() {
                this.fulscreen_loading=true;
                axios.post('/api/register', this.user)
                .then(({data}) => {
                    //this.$router.push('/login');
                    this.error_ok=data.data.message;
                    this.fulscreen_loading=false;
                })
                .catch((error) => {
                    console.log(error);
                    this.fulscreen_loading=false;
                });
            }
        }
    }
</script>