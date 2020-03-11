<template>
<div class="page-subscription bg-gray h-100 p-3">

	<h4 class="mb-2">Billing</h4>

	<div class="card shadow-sm">
		<div class="card-body p-0">

			<div class="row no-gutters">

				<template v-if="!showPlaceholder">
					<div class="col-md p-3 border-right">
						<subscription-plan :subscription="subscription"></subscription-plan>
					</div>

					<div class="col-md p-3 border-right">
						<payment-method :subscription="subscription"></payment-method>
					</div>

					<div class="col-md p-3">
						<subscription-renewal :subscription="subscription"></subscription-renewal>
					</div>
				</template>

				<template v-if="showPlaceholder">
					<div v-for="n in 2" :key="n" class="col-md p-3 border-right">
						<placeholder class="mb-3" heading heading-single :heading-image="false"></placeholder>
						<placeholder text></placeholder>
					</div>

					<div class="col-md p-3">
						<placeholder class="mb-3" heading heading-single :heading-image="false"></placeholder>
						<placeholder text></placeholder>
					</div>
				</template>

			</div>


			<div class="border-top">
				<payment-history></payment-history>
			</div>

		</div>
	</div>

</div>
</template>


<script>
import SubscriptionPlan from './partials/SubscriptionPlan.vue';
import SubscriptionRenewal from './partials/SubscriptionRenewal.vue';
import PaymentMethod from './partials/PaymentMethod.vue';
import PaymentHistory from './partials/PaymentHistory.vue';

export default {
	components: {
		SubscriptionPlan,
		SubscriptionRenewal,
		PaymentMethod,
		PaymentHistory
	},

    data() {
        return {
        	showPlaceholder: false,

			subscription: {}
        };
    },

    created() {
    	this.getSubscription();
    },

    methods: {
        getSubscription() {
        	this.showPlaceholder = true;

            axios.get('subscription')
            .then(response => {

            	// If there is no subscription data redirect to plans page
            	if ( response.data.length === 0 )
            		return this.$router.push({ name: 'billing.plans'});

            	this.showPlaceholder = false;
            	this.subscription = response.data;

            })
            .catch(error => {
            	this.$alert.error(error.response.data.message);
            }); 
        }
    }
}
</script>