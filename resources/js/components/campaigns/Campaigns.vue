<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Campaigns allowed on your current plan. In order to add more Campaigns <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div :class="['card border', { 'h-100': !showPlaceholder && campaigns.length === 0 }]">

        <div class="card-header header-elements-inline p-2">
            <div>
                <h4 class="card-title">Campaigns</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} campaigns used on your current plan</span>
            </div>

            <div class="header-elements">
                <button :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" type="button" class="btn btn-primary" @click="modals.createCampaign = true">Create Campaign</button>
            </div>
        </div>

        <div v-if="!showPlaceholder && campaigns.data.length === 0" class="card-body py-5 d-flex align-items-center justify-content-center">
            <div class="empty-state text-center">
                <i class="fal fa-4x fa-layer-group mb-2 text-gray-600"></i>
                <h5 class="font-family-base mb-1 font-weight-semibold">No Campaigns</h5>
                <p class="text-gray-600 mb-0">Create your first campaigns by <a :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" href="#" @click.prevent="modals.createCampaign = true">Clicking Here</a></p>
            </div>
        </div>

    </div>


    <div v-show="showPlaceholder" class="row">
        <div class="col-sm-6 col-md-6 col-lg-4 mb-4" v-for="n in 6" :key="n">

            <div class="card shadow-sm">
                <div class="card-body">

                    <placeholder class="mb-3" heading></placeholder>
                    <placeholder text :text-lines="3"></placeholder>

                </div>
            </div>

        </div>                 
    </div>


    <div v-show="!showPlaceholder" class="row">
        <div class="col-sm-6 col-md-6 col-lg-4" v-for="campaign in campaigns.data">
            <div class="card shadow-sm mb-4">
                
                <div class="card-header header-elements-inline bg-light border-bottom">
                    <span class="text-crop-2 font-weight-bold">{{ campaign.name }}</span>

                    <div class="header-elements">
                        
                        <dropdown menu-right>
                            <a slot="heading" href="#" class="text-body" v-tooltip.hover title="Campaign Actions"><i class="far fa-lg fa-ellipsis-v"></i></a>

                            <router-link class="dropdown-item" :to="{ name: 'links', params: { campaignId: campaign.id, campaignName: campaign.name }}"><i class="fal fa-file-alt"></i> View</router-link>
                            <button class="dropdown-item" type="button" @click="modals.editCampaign = true; setEditCampaign(campaign)"><i class="fal fa-edit"></i> Edit</button>
                            <button class="dropdown-item text-danger" type="button" @click="deleteCampaign(campaign)"><i class="fal fa-trash"></i> Delete</button>
                        </dropdown>

                    </div>
                </div>
                <div class="card-body p-0 text-center">

                    <router-link class="text-body no-underline" :to="{ name: 'links', params: { campaignId: campaign.id, campaignName: campaign.name }}">
                        <div class="row no-gutters">
                            
                            <div class="col-6 p-3 border-right border-bottom">
                                <div>Clicks</div>
                                <div><i class="far fa-mouse-pointer fa-fw text-muted"></i> <span class="lead font-weight-semibold text-primary">{{ campaign.clicks }}</span></div>
                            </div>

                            <div class="col-6 p-3 border-bottom">
                                <div>Unique Clicks</div>
                                <div><i class="far fa-user-shield fa-fw text-muted"></i> <span class="lead font-weight-semibold text-primary">{{ campaign.unique_clicks }}</span></div>
                            </div>

                            <div class="col-6 p-3 border-right">
                                <div>Links</div>
                                <div><i class="far fa-link fa-fw text-muted"></i> <span class="lead font-weight-semibold text-primary">{{ campaign.links }}</span></div>
                            </div>

                            <div class="col-6 p-3">
                                <div>Conversion ({{ campaign.conversion_rate }})</div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar" :style="{'width': campaign.conversion_rate}" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="lead font-weight-semibold text-primary">{{ campaign.conversion }}</span>
                            </div>

                        </div>
                    </router-link>

                </div>

            </div>
        </div>
    </div>

    <pagination v-show="!showPlaceholder" class="pagination-rounded"
        align="center"
        :data-meta="campaigns.meta"
        :data-links="campaigns.links"
        @change-page="getCampaigns">
    </pagination>


    <add-campaign-modal :open.sync="modals.createCampaign" @done="addCampainDone"></add-campaign-modal>
    <edit-campaign-modal :open.sync="modals.editCampaign" :edit-campaign="editCampaign" @done="getCampaigns"></edit-campaign-modal>


</div>
</template>


<script>
import AddCampaignModal from './AddModal.vue';
import EditCampaignModal from './EditModal.vue';

export default {
    components: {
        AddCampaignModal,
        EditCampaignModal
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            showPlaceholder: false,
            
            modals: {
                createCampaign: false,
                editCampaign: false,
            },

            campaigns: [],

            editCampaign: {}
        };
    },

    created() {
        this.getCampaigns();
    },

    methods: {
        getCampaigns(page = 1) {
            this.showPlaceholder = true;

            axios.get('campaigns?per_page=6&page=' + page)
            .then(response => {
                this.showPlaceholder = false;
                this.campaigns = response.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        setEditCampaign(campaign) {
            this.editCampaign = Object.assign({}, campaign);
        },

        deleteCampaign(campaign) {

            // Show alert confirmation
            this.$confirm('Are you sure you want to delete this campaign? Once deleted it cannot be undone.', 'Delete Campaign', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('campaigns/'+ campaign.id)
                .then(response => {

                    // Reduce feature usage count
                    if ( ! this.$can('access admin') )
                        this.subscriptionUsage.consumed--;

                    // Remove campaign from the DOM
                    let index = this.campaigns.data.indexOf(campaign);
                    this.campaigns.data.splice(index, 1);

                    this.$alert.success(response.data.message);

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });

        },

        addCampainDone() {
            // Record feature usage count
            if ( ! this.$can('access admin') )
                this.subscriptionUsage.consumed++;

            this.getCampaigns();
        },
    }
}
</script>