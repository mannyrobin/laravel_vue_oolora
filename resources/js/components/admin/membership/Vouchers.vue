<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header header-elements-inline">
            <h4 class="card-title">Vouchers</h4>
            <div class="header-elements">
                <button type="button" class="btn btn-primary" @click="modals.createVoucher = true">Create Vouchers</button>
            </div>
        </div>


        <datatable ref="voucherstable"
            api-url="admin/vouchers"
            :pagination-per-page="4"
            :fields-data="vouchersFields">

            <template slot="serial_no-slot" slot-scope="props">
                    <div>
                       {{ props.rowData.serial_no }}

                    </div>
            </template>

            <template slot="code-slot" slot-scope="props">
                <div>{{ props.rowData.code }}</div>

            </template>

            <template slot="status-slot" slot-scope="props">
                <div>{{ props.rowData.status }}</div>
            </template>

            <template slot="plan_id-slot" slot-scope="props">
                <div>{{ props.rowData.plan_id }}</div>
            </template>

            <template slot="user_id-slot" slot-scope="props">
                <div>{{ props.rowData.user_id }}</div>
            </template>

            <template slot="period-slot" slot-scope="props">
                <div>{{ props.rowData.period }}</div>
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <template>
                        <a v-if="!props.rowData.deleted" href="#" class="list-icons-item" @click.prevent="deleteVoucher(props.rowData)" v-tooltip.hover title="Delete Voucher"><i class="far fa-trash"></i></a>
                    </template>
                </div>
            </template>
        </datatable>

    </div>

    <!-- Create a new user -->
    <modal
        title="Create Voucher"
        submit-text="Generate Vouchers"
        :show.sync="modals.createVoucher"
        :submit-loading="btnLoading.createVoucher"
        @submit="createVoucher"
        @close="modals.createVoucher = false">

        <form data-vv-scope="form-create-voucher" class="mt-3">
            <div class="form-group">
                <label>Count</label>
                <input type="text" name="count"
                    :class="['form-control', { 'is-invalid': errors.has('form-create-voucher.count') }]"
                    v-validate="'required'"
                    v-model="newVoucher.count">
                <div class="invalid-feedback" v-show="errors.has('form-create-voucher.count')">{{ errors.first('form-create-voucher.count') }}</div>
            </div>

            <div class="form-group">
                <label>Plans</label>
                <form-select name="plan_id" v-model="newVoucher.plan_id">
                    <select-option value="2">Standard</select-option>
                    <select-option value="3">Premium</select-option>
                    <select-option value="4">Basic</select-option>
                </form-select>
            </div>


            <div class="form-group">
                <label>Period</label>
                <input type="number" name="period"
                    :class="['form-control', { 'is-invalid': errors.has('fform-create-voucher.period') }]"
                    v-validate="'required'"
                    v-model="newVoucher.period">
                <div class="invalid-feedback" v-show="errors.has('form-create-voucher.period')">{{ errors.first('form-create-voucher.period') }}</div>
            </div>




        </form>

    </modal>


    <!-- Edit a user -->
    <modal
        title="Edit Voucher"
        submit-text="Update Detials"
        :show.sync="modals.editVoucher"
        :submit-loading="btnLoading.updateVoucher"
        @submit="updateVoucher"
        @close="modals.editVoucher = false">


        <form data-vv-scope="form-edit-user">

            <div class="font-weight-semibold font-size-lg">Profile</div>
            <p class="text-gray-600 mb-2">Update user personal information</p>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                    :class="['form-control', { 'is-invalid': errors.has('form-edit-user.name') }]"
                    v-validate="'required|alpha_spaces'"
                    v-model="editVoucher.name">
                <div class="invalid-feedback" v-show="errors.has('form-edit-user.name')">{{ errors.first('form-edit-user.name') }}</div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email"
                    :class="['form-control', { 'is-invalid': errors.has('form-edit-user.email') }]"
                    v-validate="'required|email'"
                    v-model="editVoucher.email">
                <div class="invalid-feedback" v-show="errors.has('form-edit-user.email')">{{ errors.first('form-edit-user.email') }}</div>
            </div>


            <div class="mt-4 font-weight-semibold font-size-lg">Preferences</div>
            <p class="text-gray-600 mb-2">Configure user account preferences</p>

            <div class="form-group">
                <label>Plans</label>
                <form-select name="plan_id" v-model="plans">
                    <select-option
                        v-for="item in plans"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">

                        {{ item.label }}

                    </select-option>
                </form-select>
            </div>

            <div class="mt-4 font-weight-semibold font-size-lg">Roles</div>
            <p class="text-gray-600 mb-2">Assign roles and permission to the user</p>

            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="edit_user_role" name="role_admin" :true-value="true" v-model="editVoucher.role_admin">
                <label class="custom-control-label" for="edit_user_role">Make Administrator</label>
            </div>

        </form>

    </modal>

</div>
</template>


<script>
export default {
    data() {
        return {
            btnLoading: {
                updateVoucher: false,
                createVoucher: false,
                deleteVoucher: false,
            },

            modals: {
                createVoucher: false,
                editVoucher: false
            },

            newVoucher: {},

            editVoucher: {},

            vouchersFields: [
                {
                    name: 'serial_no-slot',
                    title: 'Serial No',
                    sortField: 'serial_no',
                },
	            {
                    name: 'code-slot',
                    title: 'Code',
                    sortField: 'code',
                },

                {
                    name: 'status-slot',
                    title: 'Status',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },

                {
                    name: 'plan_id-slot',
                    title: 'Plan',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },

                {
                    name: 'period-slot',
                    title: 'Duration',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },

                {
                    name: 'user_id-slot',
                    title: 'Assigned User',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },


                {
                    name: 'created_at',
                    title: 'Registered',
                    sortField: 'created_at',
                    titleClass: 'text-center',
                    dataClass: 'text-center'
                },
	            {
	                name: 'action-slot',
	                title: '',
	                dataClass: 'text-right'
	            }
	        ],
            plans: [
                {value: 2, label: "Standard"},
                {value: 3, label: "Premium"},
                {value: 4, label: "Basic"},
            ],

        };
    },



    methods: {
        createVoucher() {
            this.$validator.validateAll('form-create-voucher').then((success) => {

                if ( ! success )
                    return

                this.btnLoading.createVoucher = true;

                axios.post('admin/vouchers', this.newVoucher)
                .then(response => {

                    this.btnLoading.createVoucher = false;
                    this.modals.createVoucher = false;

                    this.$resetForm(this.newVoucher);
                    this.$alert.success(response.data.message);

                    // Refresh the table data
                    this.reloadTableData();
                })
                .catch(error => {

                    this.btnLoading.createVoucher = false;

                    this.$backendErrors(error.response.data, 'form-create-voucher');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },



        deleteVoucher(voucher) {
            this.$prompt('You are about to delete ' + ' ' + voucher.code , {
                confirmButtonText: 'Delete Voucher',
                cancelButtonText: "Don't Delete",
            }).then(() => {

                axios.delete('admin/vouchers/'+ voucher.id)
                .then(response => {
                    this.$alert.success(response.data.message);

                    // Refresh the table data
                    this.reloadTableData();

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        updateVoucher() {
            this.$validator.validateAll('form-edit-voucher').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoading.updateVoucher = true;

                axios.put('admin/users/'+ this.editVoucher.id, this.editVoucher)
                .then(response => {

                    this.btnLoading.updateVoucher = false;
                    this.modals.editVoucher = false;
                    this.$alert.success(response.data.message);

                    // Refresh the table data
                    this.reloadTableData();
                })
                .catch(error => {

                    this.btnLoading.updateVoucher = false;
                    this.$backendErrors(error.response.data, 'form-edit-voucher');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },



        reloadTableData() {
            this.$refs.voucherstable.loadData();
        },



    }
}
</script>