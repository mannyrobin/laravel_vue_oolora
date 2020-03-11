<template>
<div class="page-checkout has-navbar h-100 bg-gray">

	<nav id="main-navbar" class="navbar navbar-light border-bottom bg-white fixed-top">
		<div class="container">

			<a :href="$appSettings.appUrl + '/dashboard'" class="navbar-brand">
				<img class="logo" :src="$appSettings.logoDark" :alt="$appSettings.appName">
			</a>

		</div>
	</nav>

    <div id="page-wrapper">

        <main id="page-content">

			<div class="container h-100 d-flex align-items-center">
			    <div class="col">
			        
			        <div class="row justify-content-center">
			            <div class="col-md-10">

			            	<div class="card shadow-sm">

							    <div class="row no-gutters">
									<div class="col-lg-8">
										
										<h5 v-if="!showPlaceholder" class="m-3">Select Payment Method</h5>
										<placeholder class="w-50 m-3" v-if="showPlaceholder" heading heading-single :heading-image="false"></placeholder>
							
										
										<tabs v-if="!showPlaceholder" :pills="false" :fill="false">
									        
									        <tab-pane class="bg-white p-3" v-if="$appSettings.paymentGateways.stripe == '1'">
									            <span slot="title"><i class="fal fa-credit-card"></i> Credit Card</span>

									            <stripe-payment
							                        api-url="stripe/subscribe"
							                        :additional-data="{plan_id: plan.id}">
							                    </stripe-payment>
											</tab-pane>

									        <tab-pane v-if="$appSettings.paymentGateways.paypal == '1'">
									            <span slot="title"><i class="fab fa-paypal"></i> PayPal</span>

									            <div class="text-center py-5">
										            <p class="lead">Click the button below to process your payment via PayPal</p>
								                            
								                    <a :href="$appSettings.appUrl + '/paypal/subscribe/?plan=' + plan.id" class="btn btn-lg btn-primary"><i class="fab fa-paypal fa-fw"></i> Process Payment</a>
								                </div>
											</tab-pane>
									    </tabs>

									    <placeholder  v-if="showPlaceholder" v-for="n in 2" :key="n" class="my-5 mx-3" heading :heading-image="false"></placeholder>


									</div>
									<div class="col-lg-4 p-3 border-left bg-light">
										
										<h5 v-if="!showPlaceholder" class="mb-4">Plan Summary</h5>
										<placeholder class="w-75" v-if="showPlaceholder" heading heading-single :heading-image="false"></placeholder>

										<div v-if="!showPlaceholder">
											
											<div class="font-weight-semibold">{{ plan.name }}</div>
											<p class="text-gray-600">{{ plan.description }}</p>

											<hr>

											<div class="d-flex justify-content-between text-gray-600 mb-2">
												<span>Amount</span>
												<span>{{ $appSettings.currencySymbol }}{{ plan.price }}</span>
											</div>

											<div class="d-flex justify-content-between text-gray-600 mb-2">
												<span>Billing Cycle</span>
												<span class="text-capitalize">{{ plan.interval }}ly</span>
											</div>

											<div v-if="subscription.length === 0 && plan.trial_period_days" class="d-flex justify-content-between text-gray-600 mb-2">
												<span>Trial Period</span>
												<span>{{ plan.trial_period_days }} Days</span>
											</div>

											<div class="d-flex justify-content-between text-gray-600">
												<span class="font-weight-bold">Total:</span>
												<span class="font-weight-bold">{{ $appSettings.currencySymbol }}{{ plan.price }}</span>
											</div>

											<div v-if="subscription.length === 0 && plan.trial_period_days" class="alert alert-primary mt-3 mb-0 p-2">
												<p class="font-weight-bold mb-2"><i class="fal fa-info-circle fa-fw font-size-lg"></i> Free Trial Plan</p>
												<p class="mb-0">Your Credit Card/PayPal will not be charged until the free trial period ends. You can also cancel at anytime.</p>
											</div>

										</div>

										<placeholder v-if="showPlaceholder" class="mt-4" list></placeholder>

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
export default {
    data() {
        return {
        	showPlaceholder: false,

			plan: {},

			subscription: {}
        };
    },

    created () {
    	this.getIntialData();
    },
	
    methods: {
        // Get the plan and subscription data
    	getIntialData () {
			let self = this;
	    	this.showPlaceholder = true;

			axios.all([
				axios.get('plans/' + this.$route.params.planId),
				axios.get('subscription'),
			])
			.then(axios.spread(function (plan, subscription) {

				// If the user already have a payment method prevent access to membership page
				if ( subscription.data.payment_method )
					this.$router.push('/billing');

				self.showPlaceholder = false;
				
				self.plan = plan.data;
				self.subscription = subscription.data;
			}))
			.catch( function(error) {
	         	this.$alert.error('Something went wrong while processing your request. Please refresh or contact support.');
	        });
    	}
    }
}
</script>