<template>

  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>

  <h1>В ожидании</h1>

  <div class="block_ct w-half">
    <div class="table_ct mobile_support" :class="{table_edit_mode : edit_data.id!==0, table_pay_mode : pay_data.id!==0}">
      <div class="table_row_head">  
        <div class="table_head_item table_head_item_date">Дата</div>
        <div class="table_head_item table_head_item_amount">Сумма</div>
        <div class="table_head_item table_head_item_name">Заказчик</div>
      </div>


      <div class="table_row" v-for="pending in pendings">  
        <div class="table_item table_item_date">
         <VueDatePicker  v-if="pending.id==edit_data.id" v-model="edit_data.am_date" :text-input="{ format: 'dd.MM.yyyy'}" format="dd.MM.yyyy"  model-type="yyyy-MM-dd" placeholder="Дата" :enable-time-picker="false" locale="ru"/>
         <span v-else>{{ pending.am_date_formated }}</span>
       </div>          
       <div class="table_item table_item_amount">
        <input type="text" v-if="pending.id==edit_data.id" v-model="edit_data.amount" placeholder="Сумма" @input="edit_data.amount = edit_data.amount.slice(0,6)" v-mask-number >
        <input type="text" v-else-if="pending.id==pay_data.id" v-model="pay_data.amount" placeholder="Сумма" @input="pay_data.amount = pay_data.amount.slice(0,6)" v-mask-number >
         <span v-else>{{ pending.amount }}</span>
       </div>          
       <div class="table_item table_item_name">
         <VueMultiselect  v-if="pending.id==edit_data.id" v-model="edit_data.client"   :options="clients" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите заказчика" track-by="id" label="name" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel=''  tag-placeholder="Добавить" :taggable="true"   @tag="addClient_edit" >   </VueMultiselect>
         <span v-else>{{ pending.client_name }}</span>
       </div>
       <div class="table_item table_item_actions">

        <div class="edit_actions_ct" v-if="pending.id==edit_data.id" > 
          <span @click="edit_pending_ok(pending.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
          <span @click="edit_pending_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
        </div>

        <div class="pay_actions_ct" v-else-if="pending.id==pay_data.id" > 
          <span @click="pay_pending_ok(pending.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
          <span @click="pay_pending_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
        </div>

        <div class="default_actions_ct" v-else>
          <span @click="edit_pending(pending)" class="action_btn action_edit"><i class="bx bx-edit"></i></span>
          <span @click="delete_pending(pending.id)" class="action_btn action_del"><i class="bx bx-trash"></i></span>
          <span @click="pay_pending(pending)" class="action_btn action_pay" title="Оплачено"><i class="bx bx-dollar"></i></span>
        </div>
      </div>
    </div>

      <div class="table_row">  
        <div class="table_item table_item_date"></div>          
       <div class="table_item table_item_amount">
         <span class="itog_text">Итого: </span><span>{{ itog }}</span>
       </div>          
    </div>

  </div>

</div>

<div class="block_ct w-half nooverflow">
  <h3>Добавить платеж</h3>
  <form  @submit.prevent="add_pending" class="pending_add_form">
    <VueDatePicker v-model="add_data.am_date" :text-input="{ format: 'dd.MM.yyyy'}" format="dd.MM.yyyy"  model-type="yyyy-MM-dd" placeholder="Дата" :enable-time-picker="false" locale="ru"/>
    <VueMultiselect   v-model="add_data.client"   :options="clients" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите заказчика" track-by="id" label="name" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel=''  tag-placeholder="Добавить" :taggable="true"   @tag="addClient" >   </VueMultiselect>
    <input type="text" v-model="add_data.amount" placeholder="Сумма" @input="add_data.amount = add_data.amount.slice(0,6)" v-mask-number >
    <button :disabled='!add_data.client || !add_data.am_date || !add_data.amount'>Добавить</button>
  </form>
</div>
</template>

<script>
  import VueMultiselect from 'vue-multiselect'
  import { OrbitSpinner } from 'epic-spinners'
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css'
  import "vue-multiselect/dist/vue-multiselect.css";


 
  export default {
    name: 'Pending',
    components: {
      OrbitSpinner,
      VueMultiselect,
      VueDatePicker 
    },

    data() {

      return {
        selected_client:[],
        options:[],
        itog:0,
        fulscreen_loading:true,
        clients:[],
        pendings:[],
        add_data: {
          client: '',
          am_date: new Date().getFullYear()+'-'+("0"+(new Date().getMonth()+1)).slice(-2)+'-'+("0"+new Date().getDate()).slice(-2),
          amount:''
        },      
         edit_data: {
          id:0,
          client: '',
          am_date: '',
          amount: ''
        },         
        pay_data: {
          id:0,
          amount: ''
        }
      }
    },
    watch: {

    },

    mounted() {
      this.renew()
    },
    methods: {
     renew()
     {
      this.fulscreen_loading=true;
      axios.get('/api/pending', {withCredentials: true}).then((res) => {
        this.clients = res.data.data.clients;
        this.pendings = res.data.data.pendings;
        this.itog = res.data.data.itog;
        this.fulscreen_loading=false;
      })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },

    addClient (newClient) {
      const client = {
        name: newClient,
        id: 0
      }

      let obj = this.clients.find(item => item.id === 0);
      if (obj !== undefined)
      {
        let index = this.clients.indexOf(obj);
        this.clients.splice(index, 1);
      }

      this.clients.push(client)
      this.add_data.client=client;
    },

    addClient_edit (newClient) {
      const client = {
        name: newClient,
        id: 0
      }

      let obj = this.clients.find(item => item.id === 0);
      if (obj !== undefined)
      {
        let index = this.clients.indexOf(obj);
        this.clients.splice(index, 1);
      }

      this.clients.push(client)
      this.edit_data.client=client;
    },



    add_pending()
    {
      this.fulscreen_loading=true;
      this.add_data.client={id:this.add_data.client.id, name:this.add_data.client.name}

      axios.post('/api/pending', this.add_data, {withCredentials: true}).then((res) => {
       this.add_data={
        client: '',
        am_date: new Date().getFullYear()+'-'+("0"+(new Date().getMonth()+1)).slice(-2)+'-'+("0"+new Date().getDate()).slice(-2),
        amount:''
      };

      this.renew();

    })
      .catch((error) => {
        this.fulscreen_loading=false;
         console.log(error); this.fulscreen_loading=false;
      });
    },    
    delete_pending(id)
    {
      if (confirm('Удалить клиента?')) {
        this.fulscreen_loading=true;
        axios.delete('/api/pending/'+id, {withCredentials: true}).then((res) => {
         this.renew();

       })
        .catch((error) => {
          this.fulscreen_loading=false;
           console.log(error); this.fulscreen_loading=false;
        });
      }
    },    
    pay_pending(pending)
    {
      this.pay_data.id=pending.id;
      this.pay_data.amount=pending.amount.split(/(\.)/)[0];
    },  
    pay_pending_cancel()
    {
      this.pay_data.id=0;
      this.pay_data.amount='';
    },
    pay_pending_ok(id)
    {
      this.fulscreen_loading=true;
      axios.post('/api/pending/'+id+'/pay', this.pay_data, {withCredentials: true}).then((res) => {
      this.renew();
      this.pay_data.id=0;
      this.pay_data.amount='';

     })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },


    edit_pending(pending)
    {
      this.edit_data.id=pending.id;
      this.edit_data.amount=pending.amount.split(/(\.)/)[0];
      this.edit_data.am_date=pending.am_date.split(/(\s+)/)[0];
      this.edit_data.client={'id':pending.client_id, 'name':pending.client_name};
    },  
    edit_pending_cancel()
    {
      this.edit_data.id=0;
      this.edit_data.amount='';
      this.edit_data.am_date='';
      this.edit_data.client='';
    },    
    edit_pending_ok(id)
    {
      this.fulscreen_loading=true;
      axios.put('/api/pending/'+id, this.edit_data, {withCredentials: true}).then((res) => {
       this.renew();
      this.edit_data.id=0;
      this.edit_data.amount='';
      this.edit_data.am_date='';
      this.edit_data.client='';

     })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },
  }
}
</script>