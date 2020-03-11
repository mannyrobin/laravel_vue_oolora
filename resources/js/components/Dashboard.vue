<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <h6><i class="fal fa-info-circle"></i> Maximum Clicks Reached</h6>You have reached the maximum number of clicks allowed on your current plan, therefore all your smart links have been suspended. In order to have your smart links resumed <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card border">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Dashboard</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} clicks used on your plan</span>
            </div>
            <div class="header-elements w-50">
                <add-link></add-link>
            </div>
        </div>

        <div class="card-body pt-2">

            <div class="row">
                <div class="col-md-3 border-right">

                    <placeholder v-if="showPlaceholder" class="w-50 m-auto pt-2" heading heading-single :heading-image="false" center></placeholder>

                    <div v-if="!showPlaceholder" class="media pl-5">
                        <div class="align-self-center">
                            <i class="fal fa-link text-muted fa-2x"></i>
                        </div>

                        <div class="media-body ml-3">
                            <div class="font-size-lg text-gray-600">Links</div>
                            <span class="lead font-weight-semibold">{{ overviewData.links }}</span>
                        </div>
                    </div>

                </div>                    
                <div class="col-md-3 border-right">

                    <placeholder v-if="showPlaceholder" class="w-50 m-auto pt-2" heading heading-single :heading-image="false" center></placeholder>

                    <div v-if="!showPlaceholder" class="media pl-5">
                        <div class="align-self-center">
                            <i class="fal fa-layer-group text-muted fa-2x"></i>
                        </div>

                        <div class="media-body ml-3">
                            <div class="font-size-lg text-gray-600">Campaigns</div>
                            <span class="lead font-weight-semibold">{{ overviewData.campaigns }}</span>
                        </div>
                    </div> 

                </div>
                <div class="col-md-3 border-right">

                    <placeholder v-if="showPlaceholder" class="w-50 m-auto pt-2" heading heading-single :heading-image="false" center></placeholder>

                    <div v-if="!showPlaceholder" class="media pl-5">
                        <div class="align-self-center">
                            <i class="fal fa-location-arrow text-muted fa-2x"></i>
                        </div>

                        <div class="media-body ml-3">
                            <div class="font-size-lg text-gray-600">Pixels</div>
                            <span class="lead font-weight-semibold">{{ overviewData.pixels }}</span>
                        </div>
                    </div> 

                </div>                    
                <div class="col-md-3">

                    <placeholder v-if="showPlaceholder" class="w-50 m-auto pt-2" heading heading-single :heading-image="false" center></placeholder>

                    <div v-if="!showPlaceholder" class="media pl-5">
                        <div class="align-self-center">
                            <i class="fal fa-window-restore text-muted fa-2x"></i>
                        </div>

                        <div class="media-body ml-3">
                            <div class="font-size-lg text-gray-600">Call to Actions</div>
                            <span class="lead font-weight-semibold">{{ overviewData.cta }}</span>
                        </div>
                    </div> 

                </div>                    
            </div>

        </div>

    </div>


    <div class="card border">

        <div class="card-header pb-0">
            <div>
                <h6 v-if="!showPlaceholder" class="card-title font-weight-semibold font-family-base">Overall Statistic</h6>
            </div>
        </div>

        <div class="card-body px-2 pb-2">

            <div class="row">
                <div class="col-md-10" style="height: 400px;">

                    <placeholder v-if="showPlaceholder" class="w-75 m-auto" heading :heading-image="false" style="margin-top: 180px !important;"></placeholder>

                    <canvas ref="bar"></canvas>

                </div>
                <div class="col-md-2">

                    

                    <div class="media border-bottom pb-4">
                        
                        <placeholder v-if="showPlaceholder" class="w-75 pt-2 m-auto" heading heading-single :heading-image="false" center></placeholder>

                        <template v-if="!showPlaceholder">
                            <div class="align-self-center">
                                <i class="far fa-mouse-pointer text-muted" style="font-size: 1.6em"></i>
                            </div>

                            <div class="media-body ml-3">
                                <div class="font-size-lg text-gray-600">Total Clicks</div>
                                <span class="lead font-weight-semibold">{{ totalData.clicks }}</span>
                            </div>
                        </template>

                    </div>

                   <div class="media border-bottom py-4">

                        <placeholder v-if="showPlaceholder" class="w-75 pt-2 m-auto" heading heading-single :heading-image="false" center></placeholder>

                        <template v-if="!showPlaceholder">
                            <div class="align-self-center">
                                <i class="far fa-user-shield text-muted" style="font-size: 1.6em"></i>
                            </div>

                            <div class="media-body ml-3">
                                <div class="font-size-lg text-gray-600">Unique Clicks</div>
                                <span class="lead font-weight-semibold">{{ totalData.unique_clicks }}</span>
                            </div>
                        </template>
                    </div> 


                   <div class="media border-bottom py-4">
                        
                        <placeholder v-if="showPlaceholder" class="w-75 pt-2 m-auto" heading heading-single :heading-image="false" center></placeholder>

                        <template v-if="!showPlaceholder">
                            <div class="align-self-center">
                                <i class="far fa-user-chart text-muted" style="font-size: 1.6em"></i>
                            </div>

                            <div class="media-body ml-3">
                                <div class="font-size-lg text-gray-600">Conversion</div>
                                <span class="lead font-weight-semibold">{{ totalData.conversion }}</span>
                            </div>
                        </template>
                    </div> 


                   <div class="media pt-4">
                        
                        <placeholder v-if="showPlaceholder" class="w-75 pt-2 m-auto" heading heading-single :heading-image="false" center></placeholder>

                        <template v-if="!showPlaceholder">
                            <div class="align-self-center">
                                <i class="far fa-heart-rate text-muted" style="font-size: 1.6em"></i>
                            </div>

                            <div class="media-body ml-3">
                                <div class="font-size-lg text-gray-600">Conversion Rate</div>
                                <span class="lead font-weight-semibold">{{ totalData.conversion_rate }}</span>
                            </div>
                        </template>

                    </div> 

                </div>
            </div>

        </div>

    </div>


    <div class="row">
        <div class="col-md-6">

            <div class="card border">
                
                <div v-if="showPlaceholder" class="card-body">
                    <placeholder list :list-lines="5"></placeholder>
                </div>

                <template v-if="!showPlaceholder">
                    <div class="card-header">
                        <div>
                            <h6 class="card-title font-weight-semibold font-family-base">Top Performing Links</h6>
                        </div>
                    </div>

                    <table v-if="topLinks.length" class="table">
                        <tbody>
                            <tr v-for="link in topLinks">
                                <td>
                                    <div class="text-crop-1 line-height-1"><img width="16" height="16" :src="link.favicon" alt=""> {{ link.title }}</div>
                                    <p class="text-gray-600 font-size-sm mb-0">{{ $appSettings.linkShortenDomain + '/' + link.slug }}</p>
                                </td>
                                <td class="text-right">
                                    <i class="far fa-mouse-pointer fa-fw text-gray-600"></i> <span v-if="link.total_clicks" class="font-weight-semibold">{{ link.total_clicks }}</span><span v-else class="font-weight-semibold">0</span> 
                                    <div>Clicks</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-else class=" text-center py-5">
                        <i class="fal fa-exclamation-circle fa-2x mb-1"></i>
                        <div>You have no Smart Links</div>
                    </div>

                </template>

            </div>

        </div>
        <div class="col-md-6">

            <div class="card border">

                <div v-if="showPlaceholder" class="card-body">
                    <placeholder list :list-lines="5"></placeholder>
                </div>

                <template v-if="!showPlaceholder">

                    <div class="card-header">
                        <div>
                            <h6 class="card-title font-weight-semibold font-family-base">Top Referrers</h6>
                        </div>
                    </div>

                    <table v-if="topReferrers.length" class="table">
                        <thead>
                            <tr>
                                <th scope="col">Referrer</th>
                                <th scope="col" class="text-right">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="referrer in topReferrers">
                                <td>
                                    <p v-if="referrer.referrer_url === 'Direct'" class="text-gray-600 font-size-sm mb-0">{{ referrer.referrer_url }}</p>
                                    <a v-else :href="referrer.referrer_url" target="_blank"><i class="fal fa-external-link-square"></i> {{ referrer.referrer_url }}</a>
                                </td>
                                <td class="text-right">
                                    <span class="font-weight-semibold">{{ referrer.count }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-else class=" text-center py-5">
                        <i class="fal fa-exclamation-circle fa-2x mb-1"></i>
                        <div>You have no Referrers</div>
                    </div>

                </template>


            </div>

        </div>
    </div>

</div>
</template>


<script>
import AddLink from './links/AddLink.vue';

export default {
    components: {
        AddLink
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            showPlaceholder: false,

            overviewData: {},

            totalData: {},

            topLinks: [],

            topReferrers: [],

            chartData: {
                labels: [],

                datasets: [
                    { 
                        label: 'Clicks',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, .5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    },
                    { 
                        label: 'Unique Clicks',
                        data: [],
                        backgroundColor: 'rgba(201, 203, 207, .5)',
                        borderColor: 'rgb(201, 203, 207)',
                        borderWidth: 1
                    },
                    { 
                        label: 'Conversion',
                        data: [],
                        backgroundColor: 'rgba(255, 159, 64, .5)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    }
                ]
            }
        };
    },

    created() {
        this.getStattisticData();
    },

    methods: {
        getStattisticData () {

            let self = this;
            this.showPlaceholder = true;

            axios.all([
                axios.get('analytics/top-links?amount=5'),
                axios.get('analytics/referrers?amount=6'),
                axios.get('analytics/overall-statistic'),
            ])
            .then(axios.spread(function (topLinks, topReferrers, overallStatistic) {

                self.showPlaceholder = false;

                self.topLinks = topLinks.data;
                self.topReferrers = topReferrers.data;

                self.overviewData = overallStatistic.data.overview;
                self.totalData = overallStatistic.data.total;

                // set the chart data
                self.chartData.labels = overallStatistic.data.daily.labels;
                self.chartData.datasets[0].data = overallStatistic.data.daily.clicks;
                self.chartData.datasets[1].data = overallStatistic.data.daily.unique_clicks;
                self.chartData.datasets[2].data = overallStatistic.data.daily.conversion;

                self.createChart();        

            }))
            .catch( function(error) {
                self.$alert.error('Something went wrong while fetching the statistic data, please refresh or contact support.');
            });
        },

        createChart() {
            const myChart = new Chart(this.$refs.bar, {
                type: 'bar',
                
                data: this.chartData,
                
                options: {
                    maintainAspectRatio: false,
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
    }
}
</script>