<template>

  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>

  <h1>Доходы</h1>


  <div class="block_ct w-half nooverflow">
    <h3>Период</h3>
    <div class="mounth_selects_ct">
      <div>
        <VueMultiselect v-model="value_year" :options="getYears()" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите год" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' >
        </VueMultiselect>
      </div>      
      <div>
        <VueMultiselect v-model="value_month" :options="months" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите месяц" track-by="name" label="name" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' >
        </VueMultiselect>
      </div>
    </div>
  </div>

  <div class="block_ct w-half">
    <div class="table_ct mobile_support" :class="{table_edit_mode : edit_data.id!==0}">
      <div class="table_row_head">  
        <div class="table_head_item table_head_item_date">Дата</div>
        <div class="table_head_item table_head_item_amount">Сумма</div>
        <div class="table_head_item table_head_item_name">Заказчик</div>
      </div>


      <div class="table_row" v-for="transaction in transactions">  
        <div class="table_item table_item_date">
         <VueDatePicker  v-if="transaction.id==edit_data.id" v-model="edit_data.am_date" :text-input="{ format: 'dd.MM.yyyy'}" format="dd.MM.yyyy"  model-type="yyyy-MM-dd" placeholder="Дата" :enable-time-picker="false" locale="ru"/>
         <span v-else>{{ transaction.am_date_formated }}</span>
       </div>          
       <div class="table_item table_item_amount">
        <input type="text" v-if="transaction.id==edit_data.id" v-model="edit_data.amount" placeholder="Сумма" @input="edit_data.amount = edit_data.amount.slice(0,6)" v-mask-number >
         <span v-else>{{ transaction.amount }}</span>
       </div>          
       <div class="table_item table_item_name">
         <VueMultiselect  v-if="transaction.id==edit_data.id" v-model="edit_data.client"   :options="clients" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите заказчика" track-by="id" label="name" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel=''  tag-placeholder="Добавить" :taggable="true"   @tag="addClient_edit" >   </VueMultiselect>
         <span v-else>{{ transaction.client_name }}</span>
       </div>
       <div class="table_item table_item_actions">

        <div class="edit_actions_ct" v-if="transaction.id==edit_data.id" > 
          <span @click="edit_transaction_ok(transaction.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
          <span @click="edit_transaction_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
        </div>

        <div class="default_actions_ct" v-else>
          <span @click="edit_transaction(transaction)" class="action_btn action_edit"><i class="bx bx-edit"></i></span>
          <span @click="delete_transaction(transaction.id)" class="action_btn action_del"><i class="bx bx-trash"></i></span>
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
  <h3>Добавить доход</h3>
  <form  @submit.prevent="add_transaction" class="transaction_add_form">
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


  const month = ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];

  export default {
    name: 'Transactions',
    components: {
      OrbitSpinner,
      VueMultiselect,
      VueDatePicker 
    },

    data() {

      return {
        months:[
         {name: 'Январь', value: '01'},
         {name: 'Февраль', value: '02'},
         {name: 'Март', value: '03'},
         {name: 'Апрель', value: '04'},
         {name: 'Май', value: '05'},
         {name: 'Июнь', value: '06'},
         {name: 'Июль', value: '07'},
         {name: 'Август', value: '08'},
         {name: 'Сентябрь', value: '09'},
         {name: 'Октябрь', value: '10'},
         {name: 'Ноябрь', value: '11'},
         {name: 'Декарь', value: '12'},
         ],
        value_year: (new Date()).getFullYear(),
        value_month: {name: month[(new Date()).getMonth()], value: (new Date()).getMonth()+1},
        selected_client:[],
        options:[],
        itog:0,
        min_year:2020,
        fulscreen_loading:true,
        clients:[],
        transactions:[],
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
        }
      }
    },
    watch: {
      value_year: function (val) {
        this.renew()
      },    
      value_month: function (val) {
        this.renew()
      },
    },

    mounted() {
      this.renew()
    },
    methods: {
     getYears() {
       let years=[];
       for (var i = (new Date()).getFullYear(); i >= this.min_year; i--) {
         years.push(i);
       }
       return years;
     },
     renew()
     {
      this.fulscreen_loading=true;
      axios.get('/api/transactions?year='+this.value_year+"&month="+this.value_month.value, {withCredentials: true}).then((res) => {
        this.clients = res.data.data.clients;
        this.transactions = res.data.data.transactions;
        this.itog = res.data.data.itog;
        this.min_year = res.data.data.min_year;
        this.fulscreen_loading=false;
      })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });


      if (new Date().getFullYear()+'-'+("0"+(new Date().getMonth()+1)).slice(-2)===this.value_year+'-'+("0"+(this.value_month.value)).slice(-2))
      {
        this.add_data.am_date= this.value_year+'-'+("0"+(this.value_month.value)).slice(-2)+'-'+("0"+new Date().getDate()).slice(-2)
      }
      else
      {
        this.add_data.am_date= this.value_year+'-'+this.value_month.value+'-01'
      }
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



    add_transaction()
    {
      this.fulscreen_loading=true;
      this.add_data.client={id:this.add_data.client.id, name:this.add_data.client.name}

      axios.post('/api/transactions', this.add_data, {withCredentials: true}).then((res) => {
       this.add_data={
        client: '',
        am_date: new Date().getFullYear()+'-'+("0"+(new Date().getMonth()+1)).slice(-2)+'-'+("0"+new Date().getDate()).slice(-2),
        amount:''
      };

      this.renew();

    })
      .catch((error) => {
        this.fulscreen_loading=false;
         console.log(error); 
      });
    },    
    delete_transaction(id)
    {
      if (confirm('Удалить клиента?')) {
        this.fulscreen_loading=true;
        axios.delete('/api/transactions/'+id, {withCredentials: true}).then((res) => {
         this.renew();

       })
        .catch((error) => {
          this.fulscreen_loading=false;
           console.log(error); 
        });
      }
    },    
    edit_transaction(transaction)
    {
      this.edit_data.id=transaction.id;
      this.edit_data.amount=transaction.amount.split(/(\.)/)[0];
      this.edit_data.am_date=transaction.am_date.split(/(\s+)/)[0];
      this.edit_data.client={'id':transaction.client_id, 'name':transaction.client_name};
    },    
    edit_transaction_cancel()
    {
      this.edit_data.id=0;
      this.edit_data.amount='';
      this.edit_data.am_date='';
      this.edit_data.client='';
    },    
    edit_transaction_ok(id)
    {
      this.fulscreen_loading=true;
      axios.put('/api/transactions/'+id, this.edit_data, {withCredentials: true}).then((res) => {
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