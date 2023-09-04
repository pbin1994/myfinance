<template>

  <div class="fulscreen_loader"  v-if="fulscreen_loading">
    <orbit-spinner
    :animation-duration="1200"
    :size="65"
    color="#326ced"
    />
  </div>

  <h1>Долги</h1>

  <div class="block_ct w-half">
    <div class="table_ct mobile_support" :class="{table_edit_mode : edit_data.id!==0, table_pay_mode : pay_data.id!==0}">
      <div class="table_row_head">  
        <div class="table_head_item table_head_item_date">Дата</div>
        <div class="table_head_item table_head_item_amount">Сумма</div>
        <div class="table_head_item table_head_item_name">Кому</div>
      </div>


      <div class="table_row" v-for="debt in debts">  
        <div class="table_item table_item_date">
         <VueDatePicker  v-if="debt.id==edit_data.id" v-model="edit_data.am_date" :text-input="{ format: 'dd.MM.yyyy'}" format="dd.MM.yyyy"  model-type="yyyy-MM-dd" placeholder="Дата" :enable-time-picker="false" locale="ru"/>
         <span v-else>{{ debt.am_date_formated }}</span>
       </div>          
       <div class="table_item table_item_amount">
        <input type="text" v-if="debt.id==edit_data.id" v-model="edit_data.amount" placeholder="Сумма" @input="edit_data.amount = edit_data.amount.slice(0,6)" v-mask-number >
        <input type="text" v-else-if="debt.id==pay_data.id" v-model="pay_data.amount" placeholder="Сумма" @input="pay_data.amount = pay_data.amount.slice(0,6)" v-mask-number >
         <span v-else>{{ debt.amount }}</span>
       </div>          
       <div class="table_item table_item_name">
         <input type="text" v-if="debt.id==edit_data.id" v-model="edit_data.name" placeholder="Кому">
         <span v-else>{{ debt.name }}</span>
       </div>
       <div class="table_item table_item_actions">

        <div class="edit_actions_ct" v-if="debt.id==edit_data.id" > 
          <span @click="edit_debt_ok(debt.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
          <span @click="edit_debt_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
        </div>

        <div class="pay_actions_ct" v-else-if="debt.id==pay_data.id" > 
          <span @click="pay_debt_ok(debt.id)" class="action_btn action_edit_ok"><i class="bx bx-check"></i></span>
          <span @click="pay_debt_cancel()" class="action_btn action_edit_cancel"><i class="bx bx-x"></i></span>
        </div>

        <div class="default_actions_ct" v-else>
          <span @click="edit_debt(debt)" class="action_btn action_edit"><i class="bx bx-edit"></i></span>
          <span @click="delete_debt(debt.id)" class="action_btn action_del"><i class="bx bx-trash"></i></span>
          <span @click="pay_debt(debt)" class="action_btn action_pay" title="Оплачено"><i class="bx bx-dollar"></i></span>
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
  <h3>Добавить долг</h3>
  <form  @submit.prevent="add_debt" class="debt_add_form">
    <VueDatePicker v-model="add_data.am_date" :text-input="{ format: 'dd.MM.yyyy'}" format="dd.MM.yyyy"  model-type="yyyy-MM-dd" placeholder="Дата" :enable-time-picker="false" locale="ru"/>
    <input type="text" v-model="add_data.name" placeholder="Кому">
    <input type="text" v-model="add_data.amount" placeholder="Сумма" @input="add_data.amount = add_data.amount.slice(0,6)" v-mask-number >
    <button :disabled='!add_data.name || !add_data.am_date || !add_data.amount'>Добавить</button>
  </form>
</div>



</template>



<script>
  import { OrbitSpinner } from 'epic-spinners'
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css'


 
  export default {
    name: 'Debt',
    components: {
      OrbitSpinner,
      VueDatePicker 
    },

    data() {

      return {
        itog:0,
        fulscreen_loading:true,
        debts:[],
        add_data: {
          name: '',
          am_date: new Date().getFullYear()+'-'+("0"+(new Date().getMonth()+1)).slice(-2)+'-'+("0"+new Date().getDate()).slice(-2),
          amount:''
        },      
         edit_data: {
          id:0,
          name: '',
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
      axios.get('/api/debts', {withCredentials: true}).then((res) => {
        this.debts = res.data.data.debts;
        this.itog = res.data.data.itog;
        this.fulscreen_loading=false;
      })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },



    add_debt()
    {
      this.fulscreen_loading=true;

      axios.post('/api/debts', this.add_data, {withCredentials: true}).then((res) => {
       this.add_data={
        name: '',
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
    delete_debt(id)
    {
      if (confirm('Удалить клиента?')) {
        this.fulscreen_loading=true;
        axios.delete('/api/debts/'+id, {withCredentials: true}).then((res) => {
         this.renew();

       })
        .catch((error) => {
          this.fulscreen_loading=false;
           console.log(error); this.fulscreen_loading=false;
        });
      }
    },    
    pay_debt(debt)
    {
      this.pay_data.id=debt.id;
      this.pay_data.amount=debt.amount.split(/(\.)/)[0];
    },  
    pay_debt_cancel()
    {
      this.pay_data.id=0;
      this.pay_data.amount='';
    },
    pay_debt_ok(id)
    {
      this.fulscreen_loading=true;
      axios.post('/api/debts/'+id+'/pay', this.pay_data, {withCredentials: true}).then((res) => {
      this.renew();
      this.pay_data.id=0;
      this.pay_data.amount='';

     })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },


    edit_debt(debt)
    {
      this.edit_data.id=debt.id;
      this.edit_data.amount=debt.amount.split(/(\.)/)[0];
      this.edit_data.am_date=debt.am_date.split(/(\s+)/)[0];
      this.edit_data.name=debt.name;
    },  
    edit_debt_cancel()
    {
      this.edit_data.id=0;
      this.edit_data.amount='';
      this.edit_data.am_date='';
      this.edit_data.name='';
    },    
    edit_debt_ok(id)
    {
      this.fulscreen_loading=true;
      axios.put('/api/debts/'+id, this.edit_data, {withCredentials: true}).then((res) => {
       this.renew();
      this.edit_data.id=0;
      this.edit_data.amount='';
      this.edit_data.am_date='';
      this.edit_data.name='';

     })
      .catch((error) => {
         console.log(error); this.fulscreen_loading=false;
      });
    },
  }
}
</script>