<template>

<div class="page-membership has-navbar h-100">

	<nav id="main-navbar" class="navbar navbar-light bg-white fixed-top">
		<div class="container">

			<a :href="$appSettings.appUrl + '/dashboard'" class="navbar-brand">
				<img class="logo" :src="$appSettings.logoDark" :alt="$appSettings.appName">
			</a>

			<form v-if="action === 'change'" class="form-inline">
			    <span class="navbar-text mr-2 d-none d-md-block">Never Mind?</span>
			    <router-link to="/billing" class="btn btn-sm btn-light">Back to Billing</router-link>
			</form>

		</div>
	</nav>

    <div id="page-wrapper">

        <main id="page-content">


			<div class="container h-100 d-flex align-items-center">
			    <div class="col">
			        
			        <div class="row justify-content-center">
			            <div class="col-md-10">


							<template v-if="!showPlaceholder">
								<plan-change v-if="action === 'change'" :plans="plans" :subscription="subscription"></plan-change>
								<plan-subscribe v-else :plans="plans" :subscription="subscription"></plan-subscribe>
							</template>


							<div v-if="showPlaceholder" class="card-deck mb-3 text-center" >
								<div v-for="n in 3" class="card mb-4 border">
							        
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


			            </div>
			        </div>

			    </div>
			</div>


        </main> 

    </div>

</div>

</template>

<script>

import PlanSubscribe from './partials/PlanSubscribe.vue';
import PlanChange from './partials/PlanChange.vue';

export default {
	components: {
		PlanSubscribe,
		PlanChange
	},

    data() {
        return {
        	showPlaceholder: false,

	        action: this.$route.query.action,

			plans: {},

			subscription: {}
        };
    },

    created () {
    	this.getIntialData();
	},

    methods: {

		// Get the plans and subscription data
    	getIntialData () {
			let self = this;
	    	this.showPlaceholder = true;

			axios.all([
				axios.get('plans'),
				axios.get('subscription'),
			])
			.then(axios.spread(function (plans, subscription) {

				// Handle access to the change plan page
				if ( self.action === 'change' ) {

					// If the user has no active subscription
					// Or does not have any payment method
					// Prevent them from going to the change plan page
					if ( subscription.data.length === 0 || subscription.data.payment_method === null )
						this.$router.push('/billing/plans');
				
				} else {

					// If the user already have a payment method prevent access to the plan subscribe page
					if ( subscription.data.payment_method )
						this.$router.push('/billing');

				}

				self.showPlaceholder = false;
				
				self.plans = plans.data;
				self.subscription = subscription.data;
			}))
			.catch( function(error) {
	         	self.$alert.error('Something went wrong while processing your request. Please refresh or contact support.');
	        });
    	}

    }
}
</script>