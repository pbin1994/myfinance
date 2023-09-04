<template>

  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>

  <h1>Настройки</h1>

  <div class="block_ct w-half">
    <h3>Сменить пароль</h3>
    <form  @submit.prevent="change_pwd">
      <div class="change_pwd_ct">
        <input type="password" v-model="user.old_password" placeholder="Текущий пароль">           
        <input type="password" v-model="user.password" placeholder="Новый пароль">
        <input type="password" v-model="user.password_confirmation" placeholder="Подтвердить пароль">
      </div>
      <div class="error_ok_ct" v-if="this.error_ok"> {{ error_ok }} </div>
      <div class="error_ct" v-if="this.error"> {{ error }} </div>
      <button>Сменить пароль</button>
    </form>
  </div>

</template>

<script>
  import { OrbitSpinner } from 'epic-spinners'
  export default {
    name: 'Config',
    data() {
      return {
        error_ok:'',
        error:'',
        fulscreen_loading:false,
        user: {
          old_password: '',
          password: '',
          password_confirmation: ''
        }
      };
    },
    components: {
      OrbitSpinner
    },
    methods: {
      change_pwd() {
        this.fulscreen_loading=true;
        this.error_ok='';
        this.error='';

        axios.post('/api/change_pwd', this.user, {withCredentials: true})
        .then(result => {

         this.error_ok=result.data.message;


         this.fulscreen_loading=false;
          this.user= {
            old_password: '',
            password: '',
            password_confirmation: ''
          }
       })
        .catch((error) => {
          this.fulscreen_loading=false;
          let keys = Object.keys(error.response.data.errors);
          this.error=error.response.data.errors[keys[0]][0];
        });
      }
    }
  }
</script>