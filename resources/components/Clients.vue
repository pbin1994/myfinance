<template>

  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>

  <h1>Заказчики</h1>

  <div class="block_ct w-half">
    <div class="table_ct">
      <div class="table_row_head">  
        <div class="table_head_item table_head_item_name">Заказчик</div>
      </div>


       <div class="table_row" v-for="client in clients">  
          <div class="table_item table_item_name">
             <input type="text" v-if="client.id==edit_now" v-model="edit_new_name" placeholder="Имя заказчика">
             <span v-else>{{ client.name }}</span>
          </div>
          <div class="table_item table_item_actions">

            <div class="edit_actions_ct" v-if="client.id==edit_now"> 
              <span @click="edit_client_ok(client.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
              <span @click="edit_client_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
            </div>

            <div class="default_actions_ct" v-else>
              <span @click="edit_client(client.id, client.name)" class="action_btn action_edit"><i class="bx bx-edit"></i></span>
              <span @click="delete_client(client.id)" class="action_btn action_del"><i class="bx bx-trash"></i></span>
            </div>
          </div>
       </div>

    </div>

  </div>

    <div class="block_ct w-half">
      <h3>Добавить заказчика</h3>
      <form  @submit.prevent="add_client" class="client_add_form">
        <input type="text" v-model="add_data.name" placeholder="Имя заказчика">
        <button :disabled='!add_data.name'>Добавить</button>
      </form>
    </div>

</template>

<script>
import { OrbitSpinner } from 'epic-spinners'
export default {
    name: 'Clients',
    components: {
      OrbitSpinner
    },
     data() {
            
      return {
        fulscreen_loading:true,
        clients:[],
        edit_now:0,
        edit_new_name:'',
        add_data: {
          name: '',
        }
      }
    },
    mounted() {
      this.renew()
  },
  methods: {
    renew()
    {
      this.fulscreen_loading=true;
       axios.get('/api/clients', {withCredentials: true}).then((res) => {
          this.clients = res.data.data;
          this.fulscreen_loading=false;
        })
        .catch((error) => {
           this.fulscreen_loading=false;
        });
    },
    add_client()
    {
        this.fulscreen_loading=true;
         axios.post('/api/clients', this.add_data, {withCredentials: true}).then((res) => {
         this.add_data.name='';
         this.renew();

        })
        .catch((error) => {
          this.fulscreen_loading=false;
           this.fulscreen_loading=false;
        });
    },    
    delete_client(id)
    {
      if (confirm('Удалить клиента?')) {
        this.fulscreen_loading=true;
         axios.delete('/api/clients/'+id, {withCredentials: true}).then((res) => {
         this.renew();

        })
        .catch((error) => {
          this.fulscreen_loading=false;
           this.fulscreen_loading=false;
        });
      }
    },    
    edit_client(id, name)
    {
      this.edit_now=id;
      this.edit_new_name=name;
    },    
    edit_client_cancel()
    {
      this.edit_now=0;
      this.edit_new_name='';
    },    
    edit_client_ok(id)
    {
        if (!this.edit_new_name) return false;
        this.fulscreen_loading=true;
         axios.put('/api/clients/'+id, {'name':  this.edit_new_name}, {withCredentials: true}).then((res) => {
         this.renew();
         this.edit_now=0;
         this.edit_new_name='';

        })
        .catch((error) => {
           this.fulscreen_loading=false;
        });
    },
  }
}
</script>