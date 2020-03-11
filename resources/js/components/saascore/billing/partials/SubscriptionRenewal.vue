<template>
<div>
	
    <div class="d-flex justify-content-between align-items-center">
        <h6 class="heading-weight-lighter mb-0">Membership Renewal</h6>
        
        <button-spinner :loading="btnLoadingCancel" v-if="subscription.canceled_at === null && subscription.payment_method != null" type="button" class="btn-outline-danger btn-sm border-transparent" @click="cancelSubscription()">Cancel</button-spinner>
        <span v-if="subscription.canceled_at" class="badge badge-pill badge-danger">Subscription Canceled</span>
    </div>


    <div v-if="subscription.payment_method === null && subscription.canceled_at === null && subscription.gateway ===null" class="text-center">
        <p class="mt-3 pl-5 pr-5">You are on a free trial, which will end on <strong>{{ subscription.trial_ends_at }}</strong>.</p>
        <span class="badge badge-pill badge-warning text-white">Trial Ending Soon</span>
    </div>

    <div v-if="subscription.payment_method === null && subscription.canceled_at === null && subscription.gateway ==='voucher'" class="text-center">
        <p class="mt-3 pl-5 pr-5">You are on {{ subscription.plan.name }} Plan.</p>
        <span class="badge badge-pill badge-success text-white">LIFE TIME SUBSCRIPTION</span>
    </div>



	<template v-if="subscription.payment_method != null && subscription.canceled_at === null">
		<p class="mt-2 mb-3">Your next subscription payment will be billed on</p>

		<div class="d-flex justify-content-between align-items-center w-75 m-auto">
			<div class="text-center">
				<strong>{{ subscription.ends_at}}</strong>
			</div>

			<div class="text-center" v-if="subscription.trial_ends_at === null">
				<span class="badge badge-pill badge-success">Active Subscription</span>
			</div>
            
            <div class="text-center" v-if="subscription.trial_ends_at">
                <span class="badge badge-pill badge-warning text-white">Trial End: {{ subscription.trial_ends_at }}</span>
            </div>



		</div>
	</template>


	<div v-if="subscription.canceled_at" class="text-center mt-3">
        <div class="alert alert-warning p-1">
    	   Your subscription will not be automatically renewed and membership access will end on <strong>{{ subscription.ends_at }}</strong>
        </div>

		<button-spinner :loading="btnLoadingReactivate" type="button" class="btn btn-primary btn-sm" @click="reactivateSubscription()">Reactivate Subscription</button-spinner>
	</div>

</div>
</template>


<script>
export default {
    name: 'subscription-renewal',
    
	props: ['subscription'],
	
    data() {
        return {
        	btnLoadingReactivate: false,
            btnLoadingCancel: false
        };
    },

    methods: {
    	cancelSubscription () {
        	this.$confirm('You are about to cancel your membership subscription, do you want to continue?', 'Cancel Subscription', {
                confirmButtonText: 'Confirm Cancellation',
                cancelButtonText: "Don't Cancel",
            }).then(() => {
                
                this.btnLoadingCancel = true;

                axios.post('subscription/cancel', this.subscription)
                .then(response => {
                    this.btnLoadingCancel = false;
                    this.$alert.success(response.data.message);

                    this.$router.go();
                }).catch(error => {
                    console.log(error.response);
                    this.btnLoadingCancel = false;
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        reactivateSubscription () {
        	this.btnLoadingReactivate = true;

            axios.post('subscription/reactivate', this.subscription)
            .then(response => {
                this.btnLoadingReactivate = false;
                this.$alert.success(response.data.message);

            	this.$router.go();
            })
            .catch(error => {
                this.btnLoadingReactivate = false;
                this.$alert.error(error.response.data.message);
            }); 
        }
    }

}
</script>