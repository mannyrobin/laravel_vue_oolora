<template>
<div class="bg-gray h-100 p-3">
 
    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Call to Actions allowed on your current plan. In order to add more Call to Actions <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Call to Actions</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} CTA used on your current plan</span>
            </div>

            <div class="header-elements">
                <router-link :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="btn btn-primary" :to="'/call-to-actions/creator'">Create CTA</router-link>
            </div>
        </div>


        <datatable ref="ctatable"
            api-url="cta"
            :pagination-per-page="8"
            :fields-data="ctaFields">

            <template slot="status-slot" slot-scope="props">
                <span v-if="props.rowData.disabled" class="badge badge-pill badge-secondary text-white">Disabled</span>
                <span v-else class="badge badge-pill badge-success">Active</span>
            </template>

            <template slot="usage-slot" slot-scope="props">
                {{ props.rowData.links_count }}
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <a href="#" v-if="props.rowData.disabled" class="list-icons-item" @click.prevent="changeStatus(props.rowData, null)" v-tooltip.hover title="Enable Call to Action"><i class="far fa-check-circle"></i></a>
                    <a href="#" v-if="!props.rowData.disabled" class="list-icons-item" @click.prevent="changeStatus(props.rowData, 1)" v-tooltip.hover title="Disable Call to Action"><i class="far fa-ban"></i></a>
                    <router-link class="list-icons-item" :to="'/call-to-actions/creator/' + props.rowData.id" v-tooltip.hover title="Edit Call to Action"><i class="far fa-edit"></i></router-link>
                    <a href="#" class="list-icons-item" @click.prevent="deleteCta(props.rowData.id)" v-tooltip.hover title="Delete Call to Action"><i class="far fa-trash"></i></a>
                </div>
            </template> 

        </datatable>

    </div>

</div>
</template>


<script>
export default {
    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            ctaFields: [
                {
                    name: 'name',
                    title: 'Name',
                    sortField: 'name',
                },                
                {
                    name: 'type',
                    title: 'Type',
                    sortField: 'type',
                },
                {
                    name: 'created_at',
                    title: 'Created',
                    sortField: 'created_at',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                }, 
                {
                    name: 'status-slot',
                    title: 'Status',
                    sortField: 'disabled',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                }, 
                {
                    name: 'usage-slot',
                    title: 'Usage',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },   
                {
                    name: 'action-slot',
                    title: '',
                    dataClass: 'text-right'
                }
            ]
        };
    },

    methods: {
        deleteCta(ctaId) {
            this.$confirm('Are you sure you want to delete this call to action? Once deleted it cannot be undone.', 'Delete Call to Action', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('cta/'+ ctaId)
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

        changeStatus(cta, status) {
            axios.put('cta/status/'+ cta.id, {disabled: status} )
            .then(response => {

                cta.disabled = status;
                this.$alert.success(response.data.message);

            }).catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        reloadTableData() {
            this.$refs.ctatable.loadData();
        }
    }
}
</script>