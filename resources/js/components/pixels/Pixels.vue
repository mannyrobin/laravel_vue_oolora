<template>
<div class="bg-gray h-100 p-3">

    <transition name="fade">
        <div v-if="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" class="alert alert-warning">
            <i class="fal fa-info-circle"></i> You have maxed out the total number of Pixels allowed on your current plan. In order to add more Pixels <router-link to="/billing/plans?action=change">upgrade your plan</router-link>
        </div>
    </transition>

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <div>
                <h4 class="card-title">Pixels</h4>
                <span v-if="!$can('access admin')" class="font-size-sm text-gray-600">{{ subscriptionUsage.consumed }}/{{ subscriptionUsage.value }} pixels used on your current plan</span>
            </div>

            <div class="header-elements">
                <button :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value" type="button" class="btn btn-primary" @click="modals.addPixel = true">Add Pixel</button>
            </div>
        </div>


        <datatable ref="pixeltable"
            api-url="pixels"
            :pagination-per-page="8"
            :fields-data="pixelsFields">

            <template slot="name-slot" slot-scope="props">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <img :src="$appSettings.appUrl + '/assets/images/brands/' + props.rowData.platform + '.png'" class="rounded-circle" width="32" height="32" :alt="props.rowData.platform">
                    </div>
                    <div>
                        <div class="font-weight-semibold">{{ props.rowData.name }}</div>
                        <div class="text-muted font-size-sm">Pixel ID: {{ props.rowData.code }}</div>
                    </div>
                </div>
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
                    <a href="#" class="list-icons-item" @click.prevent="modals.editPixel = true, setEditPixel(props.rowData)" v-tooltip.hover title="Edit Pixel"><i class="far fa-edit"></i></a>
                    <a href="#" class="list-icons-item" @click.prevent="deletePixel(props.rowData.id)" v-tooltip.hover title="Delete Pixel"><i class="far fa-trash"></i></a>
                </div>
            </template>

        </datatable>

    </div>


    <add-pixel-modal :open.sync="modals.addPixel" @done="addPixelDone"></add-pixel-modal>
    <edit-pixel-modal :open.sync="modals.editPixel" :edit-pixel="editPixel" @done="reloadTableData"></edit-pixel-modal>


</div>
</template>


<script>
import AddPixelModal from './AddModal.vue';
import EditPixelModal from './EditModal.vue';

export default {
    components: {
        AddPixelModal,
        EditPixelModal
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            modals: {
                addPixel: false,
                editPixel: false
            },

            editPixel: {},

            pixelsFields: [
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
            ]
        };
    },

    methods: {
        setEditPixel(pixel) {
            this.editPixel = Object.assign({}, pixel);
        },

        deletePixel(pixelId) {
            this.$confirm('Are you sure you want to delete this pixel? Once deleted it cannot be undone.', 'Delete Pixel', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {
                
                // Send ajax request
                axios.delete('pixels/'+ pixelId)
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

        addPixelDone() {
            // Record feature usage count
            if ( ! this.$can('access admin') )
                this.subscriptionUsage.consumed++;

            this.reloadTableData();
        },

        reloadTableData() {
            this.$refs.pixeltable.loadData();
        }
    }
}
</script>