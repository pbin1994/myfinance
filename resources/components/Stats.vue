<template>
  <h1>Статистика</h1>

  <div class="widgets_ct">

    <div class="widget_item widget_item_full">
      <div class="widget_loader"  v-if="amount_by_year.loading">
        <orbit-spinner
        :animation-duration="1200"
        :size="65"
        color="#326ced"
        />
      </div>
      <h3>Суммы по месяцам</h3>
      <div class="widget_item_big_ct widget_item_big_table">
        <div class="widget_graph">
          <VueMultiselect v-model="amount_by_year.value_year" :options="getYears(amount_by_year, false)" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите год" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' track-by="value" label="label" :allow-empty="false"/>
          <LineChart :chartData="amount_by_year.graph_data" :options="amount_by_year.graph_options"  :styles="{height: '360px', position: 'relative'}"/>
        </div>

        <div class="table_ct mobile_support">
          <div class="table_row_head">  
            <div class="table_head_item table_head_item_month">Месяц</div>
            <div class="table_head_item table_head_item_amount table_item_amount_left">Сумма</div>
            <div class="table_head_item table_head_item_amount table_item_amount_left">Пред. год</div>
            <div class="table_head_item table_head_item_amount table_item_amount_left">Прогноз</div>

          </div>

          <div class="table_row" v-for="item in amount_by_year.items"> 
            <div class="table_item table_item_month">{{ item.month_name }}</div>      
            <div class="table_item table_item_amount table_item_amount_left">{{ item.sum_amount }}</div>          
            <div class="table_item table_item_amount table_item_amount_light table_item_amount_left">{{ item.prev_year }}</div>          
            <div class="table_item table_item_amount table_item_amount_light table_item_amount_left">{{ item.prediction }}</div>          

          </div>
        </div>

      </div>
    </div>




    <div class="widget_item widget_item_full">
      <div class="widget_loader"  v-if="amount_by_all_years.loading">
        <orbit-spinner
        :animation-duration="1200"
        :size="65"
        color="#326ced"
        />
      </div>
      <h3>Суммы по годам</h3>
      <div class="widget_item_big_ct">
        <div class="widget_graph">
          <LineChart :chartData="amount_by_all_years.graph_data" :options="amount_by_all_years.graph_options"  :styles="{height: '360px', position: 'relative'}"/>
        </div>

        <div class="table_ct mobile_support">
          <div class="table_row_head">  
            <div class="table_head_item table_head_item_year">Год</div>
            <div class="table_head_item table_head_item_amount table_item_amount_left">Сумма</div>
            <div class="table_head_item table_head_item_amount table_item_amount_left">В месяц</div>

          </div>

          <div class="table_row" v-for="item in amount_by_all_years.items"> 
            <div class="table_item table_item_year">{{ item.am_year }}</div>      
            <div class="table_item table_item_amount table_item_amount_left">{{ item.sum_amount }}</div>          
            <div class="table_item table_item_amount table_item_amount_light table_item_amount_left">{{ item.am_avg }}</div>          

          </div>
        </div>

      </div>
    </div>


    <div class="widget_item">
      <div class="widget_loader"  v-if="top_clients.loading">
        <orbit-spinner
        :animation-duration="1200"
        :size="65"
        color="#326ced"
        />
      </div>
      <h3>Топ заказчиков</h3>
      <VueMultiselect v-model="top_clients.value_year" :options="getYears(top_clients, true)" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :allow-empty="false" :preserve-search="true" placeholder="Выберите год" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' track-by="value" label="label">
      </VueMultiselect>
      <div class="table_ct mobile_support">
        <div class="table_row_head">  
          <div class="table_head_item table_head_item_amount">Сумма</div>
          <div class="table_head_item table_head_item_name">Заказчик</div>
        </div>

        <div class="table_row" v-for="item in top_clients.items">       
         <div class="table_item table_item_amount">{{ item.sum_amount }}</div>          
         <div class="table_item table_item_name">{{ item.client_name }}</div>
       </div>
     </div>
   </div>


   <div class="widget_item widget_item_big">
    <div class="widget_loader"  v-if="amount_by_client.loading">
      <orbit-spinner
      :animation-duration="1200"
      :size="65"
      color="#326ced"
      />
    </div>
    <h3>Детализация по заказчикам</h3>
    <div class="widget_item_big_ct">
      <div class="widget_graph">
        <div class="widget_filter_ct">
          <VueMultiselect v-model="amount_by_client.value_year" :options="getYears(amount_by_client, true)" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите год" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' track-by="value" label="label" :allow-empty="false"/>

          <VueMultiselect v-model="amount_by_client.value_client" :options="amount_by_client.clients" :multiple="false" :limit="1" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Выберите заказчика" selectLabel=''   deselectGroupLabel=''   selectedLabel=''  deselectLabel='' track-by="id" label="name" :allow-empty="false"/>

        </div>

        <LineChart :chartData="amount_by_client.graph_data" :options="amount_by_client.graph_options"  :styles="{height: '360px', position: 'relative'}"/>
      </div>

      <div class="table_ct mobile_support">
        <div class="table_row_head">  
          <div class="table_head_item table_head_item_month" v-if="amount_by_client.value_year.value!='all'">Месяц</div>
          <div class="table_head_item table_head_item_year" v-if="amount_by_client.value_year.value=='all'">Год</div>
          <div class="table_head_item table_head_item_amount table_item_amount_left">Сумма</div>


        </div> 

        <div class="table_row" v-for="item in amount_by_client.items"> 
          <div class="table_item table_item_month" v-if="amount_by_client.value_year.value!='all'">{{ item.month_name }}</div>      
          <div class="table_item table_item_year" v-if="amount_by_client.value_year.value=='all'">{{ item.am_year }}</div>      
          <div class="table_item table_item_amount table_item_amount_left">{{ item.sum_amount }}</div>          


        </div>
      </div>

    </div>
  </div>



</div>


</template>

<script>
  const month = ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];
  import VueMultiselect from 'vue-multiselect'
  import { OrbitSpinner } from 'epic-spinners'
  import "vue-multiselect/dist/vue-multiselect.css";

  import { LineChart } from 'vue-chart-3';
  import { Chart, registerables } from "chart.js";
  Chart.register(...registerables);



  export default {
    name: 'Stats',
    components: {
      OrbitSpinner,
      VueMultiselect,
      LineChart 
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
        top_clients:
        {
          value_year:  {label:(new Date()).getFullYear(),value:(new Date()).getFullYear()},
          min_year:2020,
          items:[],
          loading: true
        },         
        amount_by_year:
        {
          value_year:  {label:(new Date()).getFullYear(),value:(new Date()).getFullYear()},
          min_year:2020,
          items:[],
          loading: true,
          graph_options : {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
              point:{
                borderWidth: 0,
                hoverBorderWidth: 0,
                pointBackgroundColor: 'transparent',
                pointBorderColor: 'transparent',
              }
            },
            plugins: {
              legend: {
               display: true
             }
           },

         },
         graph_data: {
          labels: [''],
          datasets: [
          {
            label:'Текущий год',
            data: [],
            borderColor: '#326ced',
            backgroundColor: '#326ced',
          },          
          {
            label:'Прошлый год',
            data: [],
            borderColor: '#326ced61',
            backgroundColor: '#326ced61',
          },          
          {
            label:'Прогноз',
            data: [],
            borderColor: '#326ced61',
            backgroundColor: '#326ced61',
            borderDash: [5, 5],
          },
          ],
        },
      },        
      amount_by_all_years:
      {
        items:[],
        loading: true, 
        graph_options : {
          responsive: true,
          maintainAspectRatio: false,
          elements: {
            point:{
              borderWidth: 0,                
              hoverBorderWidth: 0,
              borderColor: 'transparent',
            }
          },
          plugins: {
            legend: {
             display: false
           }
         },

       },
       graph_data: {
        labels: [''],
        datasets: [
        {
          label:'',
          data: [],
          borderColor: '#326ced',
          backgroundColor: 'transparent',
        },
        ],
      },
    },        
    amount_by_client:
    {
      value_year:  {label:(new Date()).getFullYear(),value:(new Date()).getFullYear()},
      value_client:  {name:'',id:0},
      min_year:2020,
      items:[],
      clients:[ {name:'',id:0}],
      loading: true, 
      graph_options : {
        responsive: true,
        maintainAspectRatio: false,
        elements: {
          point:{
            borderWidth: 0,                
            hoverBorderWidth: 0,
            borderColor: 'transparent',
          }
        },
        plugins: {
          legend: {
           display: false
         }
       },

     },
     graph_data: {
      labels: [''],
      datasets: [
      {
        label:'',
        data: [],
        borderColor: '#326ced',
        backgroundColor: 'transparent',
      },
      ],
    },
  },
}
},
watch: {
  'top_clients.value_year': function (val) {
    this.renew_top_clients();
  },      
  'amount_by_year.value_year': function (val) {
    this.renew_amount_by_year();
  },   
  'amount_by_client.value_year': function (val) {
    this.renew_amount_by_client();
  },     
  'amount_by_client.value_client': function (val) {
    this.renew_amount_by_client();
  },    
},


mounted() {
  this.renew()
},
methods: {
  getYears(item, can_all) {
   let years=[];
   if (can_all) years.push({label:'За все время',value:'all'});
   for (var i = (new Date()).getFullYear(); i >= item.min_year; i--) {
     years.push({label:i,value:i});
   }
   return years;
 },


 renew_top_clients()
 {
  this.top_clients.loading=true;
  axios.get('/api/stats/top_clients?year='+ this.top_clients.value_year.value, {withCredentials: true}).then((res) => {
    this.top_clients.min_year = res.data.data.min_year;
    this.top_clients.items = res.data.data.data;
    this.top_clients.loading=false;
  })
  .catch((error) => {
   console.log(error); this.top_clients.loading=false;
 });
},

renew_amount_by_all_years()
{
  this.amount_by_all_years.loading=true;
  axios.get('/api/stats/amount_by_all_years', {withCredentials: true}).then((res) => {
    this.amount_by_all_years.loading=false;
    this.amount_by_all_years.items=res.data.data.data;
    this.amount_by_all_years.graph_data.labels=res.data.data.data.map(item => item['am_year']);
    this.amount_by_all_years.graph_data.datasets[0].data=res.data.data.data.map(item => item['sum_amount'].replaceAll(' ', '').split('.')[0]);
  })
  .catch((error) => {
   console.log(error); this.amount_by_all_years.loading=false;
 });
},

renew_amount_by_year()
{
  this.amount_by_year.loading=true;
  axios.get('/api/stats/amount_by_year?year='+ this.amount_by_year.value_year.value, {withCredentials: true}).then((res) => {
    this.amount_by_year.min_year = res.data.data.min_year;
    this.amount_by_year.items=res.data.data.data;
    this.amount_by_year.graph_data.labels=res.data.data.data.map(item => item['month_name']);
    this.amount_by_year.graph_data.datasets[0].data=res.data.data.data.map(item => item['sum_amount'].replaceAll(' ', '').split('.')[0]);
    this.amount_by_year.graph_data.datasets[1].data=res.data.data.data.map(item => item['prev_year'].replaceAll(' ', '').split('.')[0]);
    this.amount_by_year.graph_data.datasets[2].data=res.data.data.data.map(item => item['prediction'].replaceAll(' ', '').split('.')[0]);
    this.amount_by_year.loading=false;
  })
  .catch((error) => {
   console.log(error); this.amount_by_year.loading=false;
 });
},

renew_amount_by_client()
{
  this.amount_by_client.loading=true;
  axios.get('/api/stats/amount_by_client?year='+this.amount_by_client.value_year.value+'&client_id='+this.amount_by_client.value_client.id, {withCredentials: true}).then((res) => {
    this.amount_by_client.loading=false;
    this.amount_by_client.items=res.data.data.data;
    if (this.amount_by_client.value_client.id==0 && res.data.data.clients.length) 
    { 
     this.amount_by_client.clients=res.data.data.clients;
     this.amount_by_client.value_client=this.amount_by_client.clients[0];
   }
   this.amount_by_client.min_year = res.data.data.min_year;

   if (this.amount_by_client.value_year.value!='all')
   {
    this.amount_by_client.graph_data.labels=res.data.data.data.map(item => item['month_name']);
    this.amount_by_client.graph_data.datasets[0].data=res.data.data.data.map(item => item['sum_amount'].replaceAll(' ', '').split('.')[0]);
  } 
  else
  {
    this.amount_by_client.graph_data.labels=res.data.data.data.map(item => item['am_year']);
    this.amount_by_client.graph_data.datasets[0].data=res.data.data.data.map(item => item['sum_amount'].replaceAll(' ', '').split('.')[0]);
  }

})
  .catch((error) => {
   console.log(error); this.amount_by_client.loading=false;
 });

},


renew()
{
  this.renew_top_clients();
  this.renew_amount_by_all_years();
  this.renew_amount_by_year();
  this.renew_amount_by_client();
}
}
}
</script>