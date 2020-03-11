<template>
<div class="card border" style="height: 400px;">

    <div class="card-header header-elements-inline pb-0">
        <div>
            <h6 class="card-title font-weight-semibold font-family-base">Daily Performance</h6>
        </div>
        <div class="header-elements">
            <dropdown class="btn-group" menu-right>
                <button slot="heading" class="btn btn-light btn-sm dropdown-toggle">
                    {{ periodFilter }}
                </button>

                <button class="dropdown-item" @click="getLinkPerformance(7), periodFilter = 'Last 7 Days'">Last 7 Days</button>
                <button class="dropdown-item" @click="getLinkPerformance(14), periodFilter = 'Last 14 Days'">Last 14 Days</button>
                <button class="dropdown-item" @click="getLinkPerformance(30), periodFilter = 'Last 30 Days'">Last 30 Days</button>
                <button class="dropdown-item" @click="getLinkPerformance(90), periodFilter = 'Last 90 Days'">Last 90 Days</button>
            </dropdown>
        </div>
    </div>

    <div class="card-body px-2 pb-2">
        
        <canvas ref="line"></canvas>

    </div>

</div>
</template>


<script>
import Chart from 'chart.js';

export default {
	
	props: ['linkId'],

    data() {
        return {
            showPlaceholder: false,

        	periodFilter: 'Last 7 Days',

            chartData: {
                labels: [],

                datasets: [
                    { 
                        label: 'Clicks',
                        fill: false,
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, .2)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    },
                    { 
                        label: 'Unique Clicks',
                        fill: false,
                        data: [],
                        backgroundColor: 'rgba(201, 203, 207, .2)',
                        borderColor: 'rgb(201, 203, 207)',
                        borderWidth: 1
                    },
                    { 
                        label: 'Conversion',
                        fill: false,
                        data: [],
                        backgroundColor: 'rgba(255, 159, 64, .2)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    }
                ]
            }
        };
    },

    watch: {
        linkId(val, oldVal) {
            this.getLinkPerformance();
        }
    },

    created() {
        this.getLinkPerformance();
    },

    methods: {
        createChart() {
            const myChart = new Chart(this.$refs.line, {
                type: 'line',
                
                data: this.chartData,
                
                options: {
                    maintainAspectRatio: false,         
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    }
                }
            });

            myChart.update();
        },

        getLinkPerformance(period = 7) {
            this.showPlaceholder = true;

            axios.get('analytics/link-daily?link_id=' + this.linkId + '&period=' + period)
            .then(response => {
                this.showPlaceholder = false;
                
                // set the chart data
                this.chartData.labels = response.data.dates;
                this.chartData.datasets[0].data = response.data.clicks;
                this.chartData.datasets[1].data = response.data.unique_clicks;
                this.chartData.datasets[2].data = response.data.conversion;

                this.createChart();

            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },
    }
}
</script>