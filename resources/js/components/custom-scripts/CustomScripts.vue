<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Custom Scripts allowed on your current plan. In order to add more Custom Scripts <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Custom Scripts</h4>                
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} custom scripts used on your current plan</span> 
            </div>

            <div class="header-elements">
                <button :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" type="button" class="btn btn-primary" @click="modals.addScript = true">Add Custom Script</button>
            </div>
        </div>

        <datatable ref="scripttable"
            api-url="custom-scripts"
            :pagination-per-page="8"
            :fields-data="customScriptFields">

            <template slot="name-slot" slot-scope="props">
                <i class="far fa-code fa-fw text-gray-600"></i> {{ props.rowData.name }}
            </template>

            <template slot="status-slot" slot-scope="props">
                <span v-if="props.rowData.disabled" class="badge badge-pill badge-secondary text-white">Disabled</span>
                <span v-else class="badge badge-pill badge-success">Active</span>
            </template>

            <template slot="usage-slot" slot-scope="props">
                {{ props.rowData.links_count }}
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <a href="#" class="list-icons-item" @click.prevent="modals.editScript = true, setEditScript(props.rowData)" v-tooltip.hover title="Edit Script"><i class="far fa-edit"></i></a>
                    <a href="#" class="list-icons-item" @click.prevent="deleteScript(props.rowData.id)" v-tooltip.hover title="Delete Script"><i class="far fa-trash"></i></a>
                </div>
            </template>

        </datatable>

    </div>


    <add-script-modal :open.sync="modals.addScript" @done="addCustomScriptDone"></add-script-modal>
    <edit-script-modal :open.sync="modals.editScript" :edit-script="editScript" @done="reloadTableData"></edit-script-modal>


</div>
</template>


<script>
import AddScriptModal from './AddModal.vue';
import EditScriptModal from './EditModal.vue';

export default {
    components: {
        AddScriptModal,
        EditScriptModal
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            modals: {
                addScript: false,
                editScript: false
            },

            editScript: {},

            customScriptFields: [
                {
                    name: 'name-slot',
                    title: 'Name',
                    sortField: 'name',
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
            ],
        };
    },

    methods: {
        setEditScript(script) {
            this.editScript = Object.assign({}, script);
        },

        deleteScript(scriptId) {
            this.$confirm('Are you sure you want to delete this custom script? Once deleted it cannot be undone.', 'Delete Custom Script', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('custom-scripts/'+ scriptId)
                .then(response => {

                    // Reduce feature usage count
                    if ( ! this.$can('access admin') )
                        this.subscriptionUsage.consumed--;

                    // Remove script from the DOM
                    this.reloadTableData();
                    this.$alert.success(response.data.message);

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        addCustomScriptDone() {
            // Record feature usage count
            if ( ! this.$can('access admin') )
                this.subscriptionUsage.consumed++;

            this.reloadTableData();
        },

        reloadTableData() {
            this.$refs.scripttable.loadData();
        }
    }
}
</script>