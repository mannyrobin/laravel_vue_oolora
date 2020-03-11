<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Domains allowed on your current plan. In order to add more Domains <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Domains</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} domains used on your current plan</span>
            </div>

            <div class="header-elements">
                <button :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" type="button" class="btn btn-primary" @click="modals.addDomain = true">Add Domain</button>
            </div>
        </div>


        <datatable ref="domaintable"
            api-url="domains"
            :pagination-per-page="8"
            :fields-data="domainFields">

            <template slot="name-slot" slot-scope="props">
                <div>{{ props.rowData.name }}</div>
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <a href="#" class="list-icons-item" @click.prevent="modals.editDomain = true, setEditDomain(props.rowData)" v-tooltip.hover title="Edit Domain"><i class="far fa-edit"></i></a>
                    <a href="#" class="list-icons-item" @click.prevent="deleteDomain(props.rowData.id)" v-tooltip.hover title="Delete Domain"><i class="far fa-trash"></i></a>
                </div>
            </template>

        </datatable> 

    </div>


    <add-domain-modal :open.sync="modals.addDomain" @done="addDomainDone"></add-domain-modal>
    <edit-domain-modal :open.sync="modals.editDomain" :edit-domain="editDomain" @done="reloadTableData"></edit-domain-modal>


</div>
</template>


<script>
import AddDomainModal from './AddModal.vue';
import EditDomainModal from './EditModal.vue';

export default {
    components: {
        AddDomainModal,
        EditDomainModal
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            modals: {
                addDomain: false,
                editDomain: false
            },

            editDomain: {},

            domainFields: [
                {
                    name: 'name-slot',
                    title: 'Domain Name',
                    sortField: 'name',
                },
                {
                    name: 'created_at',
                    title: 'Added On',
                    sortField: 'created_at',
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
        setEditDomain(domain) {
            this.editDomain = Object.assign({}, domain);
        },

        deleteDomain(domainId) {
            this.$confirm('Are you sure you want to delete this domain? Once deleted it cannot be undone.', 'Delete Domain', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('domains/'+ domainId)
                .then(response => {

                    // Reduce feature usage count
                    if ( ! this.$can('access admin') )
                        this.subscriptionUsage.consumed--;
                    
                    // Remove from the DOM
                    this.reloadTableData();
                    this.$alert.success(response.data.message);

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        addDomainDone() {
            // Record feature usage count
            if ( ! this.$can('access admin') )
                this.subscriptionUsage.consumed++;

            this.reloadTableData();
        },

        reloadTableData() {
            this.$refs.domaintable.loadData();
        }
    }
}
</script>