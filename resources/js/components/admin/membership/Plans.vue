<template>
<div class="bg-gray p-3 h-100">

    <div :class="['card border', { 'h-100': !showPlaceholder && plans.length === 0 }]">

        <div class="card-header header-elements-inline p-2">
            <h4 class="card-title">Subscription Plans</h4>
            <div class="header-elements">
                <button type="button" class="btn btn-primary" @click="modals.addPlan = true">Add a Plan</button>
            </div>
        </div>

        <div v-if="!showPlaceholder && plans.length === 0" class="card-body d-flex align-items-center justify-content-center">
            <div class="empty-state text-center">
                <i class="fal fa-4x fa-shopping-basket mb-2 text-gray-600"></i>
                <h5 class="font-family-base mb-1 font-weight-semibold">No Subscription Plans</h5>
                <p class="text-gray-600 mb-0">Create your first subscription plan by <a href="#" @click.prevent="modals.addPlan = true">Clicking Here</a></p>
            </div>
        </div>

    </div>


    <div v-if="!showPlaceholder && plans.length != 0" class="row">
        <div v-for="plan in plans" class="col-md-4">
        
            <div class="card mb-4 shadow-sm text-center">

                <span v-if="plan.active" class="badge badge-success rotate-90-inverse rounded-0" style="position: absolute; left: -10px; top: 10px;">Active</span>
                <span v-if="!plan.active" class="badge badge-warning text-white rotate-90-inverse rounded-0" style="position: absolute; left: -16px; top: 16px;">Disabled</span>

                <div class="card-header">
                    <h4 class="mb-0 font-weight-600">{{ plan.name }}</h4>
                </div>

                <div class="card-body">

                    <h1 class="card-title font-family-base">
                        <sup>{{ $appSettings.currencySymbol }}</sup>{{ plan.price }} <small class="text-muted font-size-sm">/ {{ plan.interval }}</small></span>
                    </h1>

                    <span v-if="plan.trial_period_days" class="badge badge-info">{{ plan.trial_period_days }} Days Free Trial</span>

                    <hr>

                    <ul class="list-unstyled">
                        <li class="mb-2" v-for="feature in plan.features">
                            <span v-if="feature.value != '0' && feature.value != 'INCLUDE' " class="font-weight-bold">{{ feature.value }}</span> <del v-if="feature.value == '0'">{{ feature.name }}</del><span v-else>{{ feature.name }}</span>
                        </li>
                    </ul>

                    <div class="text-left mt-4">
                        <p class="text-muted mb-0">Description</p>
                        <p>{{ plan.description }}</p>
                    </div>

                </div>

                    
                <div class="card-footer border-top d-sm-flex justify-content-sm-between align-items-sm-center">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-body font-size-sm" @click.prevent="modals.editPlan = true, setEditPlan(plan)"><i class="fal fa-edit fa-fw"></i> Edit Plan</a></li>
                        <li class="list-inline-item"><a href="#" class="text-body font-size-sm" @click.prevent="modals.editFeatures = true, setEditFeatures(plan.features)"><i class="fal fa-file-edit fa-fw"></i> Edit Features</a></li>
                    </ul>
                    <ul class="list-inline mb-0">
                        <li v-if="!plan.active" class="list-inline-item"><a href="#" class="text-success font-size-sm" @click.prevent="enablePlan(plan.id)"><i class="fal fa-check-double fa-fw"></i> Enable</a></li>
                        <li v-if="plan.active" class="list-inline-item"><a href="#" class="text-danger font-size-sm" @click.prevent="deletePlan(plan.id)"><i class="fal fa-trash fa-fw"></i> Delete</a></li>
                    </ul>
                </div>


            </div>

        </div>
    </div>


    <div v-if="showPlaceholder" class="card-deck mb-3 text-center" >
        <div v-for="n in 3" class="card mb-4 shadow-sm">
            
            <div class="card-header">
                <placeholder heading heading-single :heading-image="false" center></placeholder>
            </div>

            <div class="card-body">
                <div class="py-2 px-5 mb-4">
                    <placeholder list :list-lines="5"></placeholder>
                </div>
                <placeholder heading heading-single :heading-image="false" center class="w-50 m-auto"></placeholder>
            </div>

        </div>
    </div>



    <!-- Add a plan-->
    <modal
        title="Add a Plan"
        submit-text="Create Plan"
        :show.sync="modals.addPlan"        
        :submit-loading="btnLoading.addPlan"
        @submit="createPlan"
        @close="modals.addPlan = false">

        <form data-vv-scope="form-add-plan" class="mt-3">
            
            <div class="row">
                <div class="col-md-8">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Basic Plan" 
                            :class="['form-control', { 'is-invalid': errors.has('form-add-plan.name') }]"
                            v-validate="'required'"
                            v-model="newPlan.name">
                        <div class="invalid-feedback" v-show="errors.has('form-add-plan.name')">{{ errors.first('form-add-plan.name') }}</div>
                    </div>

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <label>Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $appSettings.currencySymbol }}</span>
                            </div>

                            <input type="text" name="price" class="form-control" placeholder="4.95" 
                                :class="['form-control', { 'is-invalid': errors.has('form-add-plan.price') }]"
                                v-validate="'required|decimal:2'"
                                v-model="newPlan.price">
                        </div>

                        <div class="invalid-feedback" v-show="errors.has('form-add-plan.price')">{{ errors.first('form-add-plan.price') }}</div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-5">

                    <div class="form-group">
                        <label>Cycle</label>
                       <form-select name="interval" placeholder="Monthly" v-model="newPlan.interval">
                            <select-option
                                v-for="item in intervalData"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                                
                                {{ item.label }}

                            </select-option>
                        </form-select>
                    </div>

                </div>
                <div class="col-md-2">

                    <div class="form-group">
                        <label>Frequency</label>
                        <input type="number" name="interval_count"
                            :class="['form-control', { 'is-invalid': errors.has('form-add-plan.interval_count') }]"
                            v-validate="'required|max_value:12'"
                            v-model="newPlan.interval_count">
                        <div class="invalid-feedback" v-show="errors.has('form-add-plan.interval_count')">{{ errors.first('form-add-plan.interval_count') }}</div>
                    </div>

                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label>Trial Period</label>
                        <div class="input-group">
                            <input type="number" name="trial_period" class="form-control" v-model="newPlan.trial_period">
                            
                            <div class="input-group-append">
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-2">

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" v-model="newPlan.sort_order">
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea placeholder="A brief statement that summarize what the plan is about"
                    name="description" 
                    rows="3"                    
                    :class="['form-control', { 'is-invalid': errors.has('form-add-plan.description') }]"
                    v-validate="'required|max:127'"
                    v-model="newPlan.description">
                </textarea>     
                <div class="invalid-feedback" v-show="errors.has('form-add-plan.description')">{{ errors.first('form-add-plan.description') }}</div>
            </div>

        </form>

    </modal>


    <!-- Edit a plan-->
    <modal
        title="Edit Plan"
        submit-text="Update Plan"
        :show.sync="modals.editPlan"        
        :submit-loading="btnLoading.editPlan"
        @submit="updatePlan"
        @close="modals.editPlan = false">

        <div class="alert alert-info p-2">PayPal and Stripe plans are immutable, meaning you can't change the price, cycle, trial period etc once you have created it. However, you can delete/disable the plan and re-create it using the new information.</div>

        <form data-vv-scope="form-edit-plan" class="mt-3">
            
            <div class="row">
                <div class="col-md-9">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Basic Plan" 
                            :class="['form-control', { 'is-invalid': errors.has('form-edit-plan.name') }]"
                            v-validate="'required'"
                            v-model="editPlan.name">
                        <div class="invalid-feedback" v-show="errors.has('form-edit-plan.name')">{{ errors.first('form-edit-plan.name') }}</div>
                    </div>

                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" v-model="editPlan.sort_order">
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea placeholder="A brief statement that summarize what the plan is about"
                    name="description" 
                    rows="3"
                    :class="['form-control', { 'is-invalid': errors.has('form-edit-plan.description') }]"
                    v-validate="'required|max:127'"
                    v-model="editPlan.description">
                </textarea>
                <div class="invalid-feedback" v-show="errors.has('form-edit-plan.description')">{{ errors.first('form-edit-plan.description') }}</div>
            </div>

        </form>

    </modal>


    <!-- Update features -->
    <modal
        title="Edit Plan Features"
        submit-text="Update Features"
        size="small"
        :show.sync="modals.editFeatures"        
        :submit-loading="btnLoading.editFeatures"
        @submit="updateFeatures"
        @close="modals.editFeatures = false">

        <div class="alert alert-info p-2">

            <div>You can change the feature usage count along with the display name.</div>
            <div class="mt-1">
                <span class="font-weight-semibold">0</span> - to disable a feature  <br /><span class="font-weight-semibold">UNLIMITED</span> - to remove the usage restriction
            </div>

        </div>

        <form @keyup.enter.prevent="updateFeatures" data-vv-scope="form-edit-features" class="mt-3">
         
            <div class="form-group" v-for="feature in editFeatures">
                <label>ID: {{ feature.code }}</label>
                <div class="input-group">

                    <div v-if="feature.value_selection">
                       <form-select v-model="feature.value">
                            <select-option
                                v-for="item in featureValueSelection"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                                
                                {{ item.label }}

                            </select-option>
                        </form-select>
                    </div>

                    <input v-if="!feature.value_selection" type="text" class="form-control" v-model="feature.value" required>
                    <input type="text" class="form-control" v-model="feature.name" required>
                </div>
            </div>   

        </form>

    </modal>

</div>
</template>


<script>
export default {
    data() {
        return {
            showPlaceholder: false,

            btnLoading: {
                addPlan: false,
                editPlan: false,
                editFeatures: false,
            },

            modals: {
                addPlan: false,
                editPlan: false,
                editFeatures: false,
            },

            plans: [],

            newPlan: {
                interval: 'month',
                interval_count: 1,
            },

            editPlan: {},

            editFeatures: [],

            featureValueSelection: [
                {value: "INCLUDE", label: "Include"},
                {value: "0", label: "Exclude"},
            ],

            intervalData: [
                {value: "day", label: "Daily"},
                {value: "week", label: "Weekly"},
                {value: "month", label: "Monthly"},
                {value: "year", label: "Yearly"},
            ]
        };
    },

    created() {
        this.getPlans();

    },

    methods: {
        getPlans() {
            this.showPlaceholder = true;

            axios.get('admin/plans')
            .then(response => {

                this.showPlaceholder = false;
                this.plans = response.data;

            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            }); 
        },

        createPlan() {
            this.$validator.validateAll('form-add-plan').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading.addPlan = true;

                axios.post('admin/plans', this.newPlan)
                .then(response => {

                    this.btnLoading.addPlan = false;
                    this.modals.addPlan = false;

                    // Reset new plan data
                    this.newPlan = {interval: 'month', interval_count: 1 },
                    this.$alert.success(response.data.message);

                    this.getPlans();

                })
                .catch(error => {

                    this.btnLoading.addPlan = false;
                    this.$backendErrors(error.response.data, 'form-add-plan');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },

        setEditFeatures(features) {
            this.editFeatures = JSON.parse(JSON.stringify( features ));
        },

        updateFeatures() {
            this.$validator.validateAll('form-edit-features').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading.editFeatures = true;

                axios.put('admin/plans/update-features', this.editFeatures)
                .then(response => {

                    this.btnLoading.editFeatures = false;
                    this.modals.editFeatures = false;
                    this.$alert.success(response.data.message);

                    this.getPlans();

                })
                .catch(error => {

                    this.btnLoading.editFeatures = false;
                    this.$backendErrors(error.response.data, 'form-edit-features');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },

        setEditPlan(plan) {
            this.editPlan = Object.assign({}, plan);
        },

        updatePlan() {
            this.$validator.validateAll('form-edit-plan').then((success) => {
                
                if ( ! success )
                    return

                this.btnLoading.editPlan = true;

                axios.put('admin/plans/' + this.editPlan.id, this.editPlan)
                .then(response => {

                    this.btnLoading.editPlan = false;
                    this.modals.editPlan = false;
                    this.$alert.success(response.data.message);

                    this.getPlans();

                })
                .catch(error => {

                    this.btnLoading.editPlan = false;
                    this.$backendErrors(error.response.data, 'form-edit-plan');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },

        deletePlan(planId) {
            this.$confirm('You are about to permanently delete this plan. However, if it has any active subscribers it will be disabled instead', 'Delete Plan', {
                type: 'warning',
                confirmButtonText: 'Remove Plan',
                cancelButtonText: "Don't Remove",
            }).then(() => {

                axios.delete('admin/plans/'+ planId)
                .then(response => {
                    
                    this.$alert.success(response.data.message);

                    // Refresh the table data
                    this.getPlans();

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        enablePlan(planId) {
            this.$confirm('Do you want to make this plan active?', 'Enable Plan', {
                confirmButtonText: 'Enable It',
                cancelButtonText: "Don't Enable",
            }).then(() => {

                axios.put('admin/plans/enable/'+ planId)
                .then(response => {
                    this.$alert.success(response.data.message);

                    // Refresh the data
                    this.getPlans();

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });            
        },

    }
}
</script>