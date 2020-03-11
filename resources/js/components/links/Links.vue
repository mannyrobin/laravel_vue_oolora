<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Smart Links allowed on your current plan. In order to add more Smart Links <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Smart Links</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} smart links used on your plan</span>
            </div>

            <div class="header-elements w-50">
                <add-link></add-link>
            </div>
        </div>


        <div class="border-top py-1 px-3">

            <div class="w-25 ml-auto">
                <form-select placeholder="Filter by campaign"
                    multiple
                    collapse-tags
                    v-model="tableParams.campaign_where"
                    @remove-tag="filterTable(false)"
                    @visible-change="e => filterTable(e)">

                    <select-option 
                        v-for="item in campaigns" 
                        :key="item.id" 
                        :label="item.name" 
                        :value="item.id">
                    </select-option>
                </form-select>
            </div>

        </div>

        <datatable ref="linktable"
            api-url="links"
            :disabled-border="true"
            :pagination-per-page="10"
            :append-params="tableParams"
            :fields-data="linksFields">

            <template slot="original-link-slot" slot-scope="props">
                <div class="text-crop-1 line-height-1">
                    <img width="16" height="16" :src="props.rowData.favicon" alt="">
                    <span>{{ props.rowData.title }}</span>
                </div>
                <div class="text-gray-600 font-size-sm text-crop-1">{{ props.rowData.url }}</div>
            </template>

            <template slot="smart-link-slot" slot-scope="props">
                <div ref="copylink">{{ props.rowData.domain }}/{{ props.rowData.slug }} <i @click="copyLink(props.rowData.domain +'/'+ props.rowData.slug)" class="fal fa-copy cursor-pointer"></i></div>
                <div class="text-muted font-size-sm">Added on {{ props.rowData.created_at }}</div>
            </template>

            <template slot="campaign-slot" slot-scope="props">
                <span v-if="props.rowData.campaign">{{ props.rowData.campaign.name }}</span>
                <span v-else>&ndash;</span>
            </template>

            <template slot="clicks-slot" slot-scope="props">
                <div>
                    <i class="far fa-mouse-pointer fa-fw text-gray-600"></i> <span class="font-weight-semibold">{{ props.rowData.total_clicks }}</span> Clicks
                </div>
            </template>

            <template slot="conversion-slot" slot-scope="props">
                <div>
                    <i class="far fa-user-chart fa-fw text-gray-600"></i> <span class="font-weight-semibold">{{ props.rowData.total_conversion }}</span> Conv.
                </div>
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <dropdown menu-right>
                        <a slot="heading" href="#" class="list-icons-item" v-tooltip.hover title="Link Actions"><i class="far fa-ellipsis-v"></i></a>

                        <router-link class="dropdown-item" :to="'/links/creator/' + props.rowData.id"><i class="fal fa-edit"></i> Edit</router-link>
                        <router-link class="dropdown-item" :to="'/analytics/' + props.rowData.id"><i class="fal fa-chart-line"></i> Statistic</router-link>
                        <button v-if="props.rowData.disabled" class="dropdown-item" type="button" @click="changeStatus(props.rowData, null)"><i class="fal fa-check-circle"></i> Enable</button>
                        <button v-if="!props.rowData.disabled" class="dropdown-item" type="button" @click="changeStatus(props.rowData, 1)"><i class="fal fa-ban"></i> Disable</button>
                        <button class="dropdown-item text-danger" type="button" @click="deleteLink(props.rowData.id)"><i class="fal fa-trash"></i> Delete</button>
                    </dropdown>
                </div>
            </template>

        </datatable>

    </div>

</div>
</template>


<script>
import AddLink from './AddLink.vue';

export default {
    components: {
        AddLink
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            campaigns: [],

            tableParams: {
                campaign_where: []
            },

            linksFields: [
               {
                    name: 'original-link-slot',
                    title: 'Original Link',
                    dataClass: 'w-25'
                },
                {
                    name: 'smart-link-slot',
                    title: 'Smart Link',
                }, 
                {
                    name: 'campaign-slot',
                    title: 'Campaign',
                    sortField: 'campaign_id',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },          
                {
                    name: 'clicks-slot',
                    title: 'Clicks',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },
                {
                    name: 'conversion-slot',
                    title: 'Conversion',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },  
                {
                    name: 'action-slot',
                    title: '',
                    dataClass: 'text-right'
                }
            ],

        };
    },

    created() {
        this.getCampaigns();

        // If there is a campaign ID pass it on to the table query 
        if ( this.$route.params.campaignId ) {
            this.campaigns.push({id: this.$route.params.campaignId, name: this.$route.params.campaignName});
            this.tableParams.campaign_where.push(this.$route.params.campaignId);
        }
    },

    methods: {
        copyLink(text) {
            
            self = this;

            this.$copyText(text).then(function (e) {
                self.$alert.success('Smart link copied to your clipboard ' + text);
            }, function (e) {
               self.$alert.error('Unable to copy the link to your clipboard');
            })
        },

        getCampaigns() {
            axios.get('campaigns')
            .then(response => {
                this.campaigns = response.data.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        filterTable(event) {
            if ( event === false )
                this.reloadTableData();
        },

        changeStatus(link, status) {
            axios.put('links/status/'+ link.id, {disabled: status} )
            .then(response => {

                link.disabled = status;
                this.$alert.success(response.data.message);

            }).catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        deleteLink(linkId) {
            this.$confirm('Are you sure you want to delete this link? Once deleted it cannot be undone.', 'Delete Link', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('links/'+ linkId)
                .then(response => {

                    // Reduce feature usage count
                    if ( ! this.$can('access admin') )
                        this.subscriptionUsage.consumed--;                    

                    // Remove pixel from the DOM
                    this.reloadTableData();
                    this.$alert.success(response.data.message);

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        reloadTableData() {
            this.$refs.linktable.loadData();
        }
    }
}
</script>