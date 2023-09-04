<template>
  <h1>Добро пожаловать!</h1>

  <div class="widgets_ct">

    <div class="widget_item">
      <div class="widget_loader"  v-if="text_widget.loading">
        <orbit-spinner
        :animation-duration="1200"
        :size="65"
        color="#326ced"
        />
      </div>
      <h3 class="text_widget_h">За текущий месяц </h3>
      <div class="dashboard_amount">{{ text_widget.cur_m_amount }} ₽</div>

      <h3 class="text_widget_h">В прошлом году</h3>
      <div class="dashboard_amount">{{ text_widget.prev_year_m_amount }} ₽</div>

      <h3 class="text_widget_h">Прогноз</h3>
      <div class="dashboard_amount">{{ text_widget.prediction }} ₽</div>

      <h3 class="text_widget_h">В ожидании</h3>
      <div class="dashboard_amount">{{ text_widget.pending_amount }} ₽</div>

      <h3 class="text_widget_h">Долгов</h3>
      <div class="dashboard_amount">{{ text_widget.debt_amount }} ₽</div>
    </div>



    <div class="widget_item widget_item_big">
      <div class="widget_loader"  v-if="text_widget.loading">
        <orbit-spinner
        :animation-duration="1200"
        :size="65"
        color="#326ced"
        />
      </div>
      <h3>За последние 12 месяцев</h3>
      <LineChart :chartData="mounthly_graph.graph_data" :options="mounthly_graph.graph_options"  :styles="{height: '340px', position: 'relative'}"/>
    </div>



  </div>

</template>

<script>
  import { OrbitSpinner } from 'epic-spinners'
  import { LineChart } from 'vue-chart-3';
  import { Chart, registerables } from "chart.js";
  Chart.register(...registerables);

  export default {
    name: 'Dashboard',
    components: {
      OrbitSpinner,
      LineChart 
    },
    data() {
      return {
        mounthly_graph:
        {
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
      text_widget:
      {
        text_widget: true,
        cur_m_amount: '0.00',
        prev_year_m_amount: '0.00',
        pending_amount: '0.00',
        debt_amount: '0.00',
        prediction: '0.00',
      },  
    }
  },
  mounted() {
    this.renew()
  },
  methods: {
    renew_text_widget()
    {
      this.text_widget.loading=true;
      axios.get('/api/dashboard/text_widget', {withCredentials: true}).then((res) => {
        this.text_widget.cur_m_amount = res.data.data.cur_m_amount;
        this.text_widget.prev_year_m_amount = res.data.data.prev_year_m_amount;
        this.text_widget.pending_amount = res.data.data.pending_amount;
        this.text_widget.debt_amount = res.data.data.debt_amount;
        this.text_widget.prediction = res.data.data.prediction;
        this.text_widget.loading=false;
      })
      .catch((error) => {
        console.log(error); this.text_widget.loading=false;
      });
    },      
    renew_mounthly_graph()
    {
     this.mounthly_graph.loading=true;
     axios.get('/api/dashboard/mounthly_graph', {withCredentials: true}).then((res) => {
       this.mounthly_graph.graph_data.labels=res.data.data.months.map(item => item['month_name']);
       this.mounthly_graph.graph_data.datasets[0].data=res.data.data.months.map(item => item['sum_amount'].replaceAll(' ', '').split('.')[0]);
       this.mounthly_graph.loading=false;
      })
      .catch((error) => {
          console.log(error); //this.mounthly_graph.loading=false;
        });
    },
    renew()
    {
      this.renew_text_widget();
      this.renew_mounthly_graph();
    }
  }
}
</script>