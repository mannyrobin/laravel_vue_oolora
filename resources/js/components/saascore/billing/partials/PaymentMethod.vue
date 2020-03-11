<template>
<div>

	<div class="d-flex justify-content-between align-items-center">
		<h6 class="heading-weight-lighter mb-0">Payment Method</h6>
		<button v-if="subscription.gateway === 'Stripe' && subscription.payment_method != null" type="button" class="btn btn-outline-primary btn-sm" @click="modals.updateCreditCard = true">Update Card</button>
	</div>

    <div v-if="subscription.payment_method === null && subscription.canceled_at === null" class="text-center">
        <p class="mt-3">You do not have any active payment methods, please add one to continue using our service without any interruptions.</p>
    	
    	<router-link :to="'/billing/plans/checkout/' + subscription.plan.id" class="btn btn-primary btn-sm">Add Payment Method</router-link>
    </div>

	<div class="text-center">
		<p>OR </p>
		<a href="/my-voucher" class="btn btn-primary btn-sm"> Use Redemption Code</a>
	</div>

	<template v-if="subscription.gateway === 'Stripe'">
		<div v-if="subscription.payment_method != null" class="mt-4 d-flex">
			<div>
				<p class="mb-2">Name:</p>
				<p class="mb-2">Credit Card:</p>
				<p class="mb-2">Expiration:</p>
			</div>
			<div class="ml-5">
				<p class="mb-2">{{ subscription.payment_method.name }}</p>
				<p class="mb-2"><img :src="$appSettings.appUrl + '/assets/images/credit-cards/' + creditCardBrand + '.svg'" width="22px"> xxxx {{ subscription.payment_method.last4 }}</p>
				<p class="mb-2">{{ subscription.payment_method.exp_month }}/{{ subscription.payment_method.exp_year }}</p>
			</div>
		</div>

		<div v-if="subscription.payment_method === null" class="mt-4 text-center">
			<p>You do not have any credit card attached to your account</p>

			<button type="button" class="btn btn-primary btn-sm" @click="modals.updateCreditCard = true">Add Credit Card</button>
		</div>


		<!-- Update a credit card modal -->
		<modal
	        title="Update Credit Card"
	        size="small"
	        :hide-footer="true"
	        :show.sync="modals.updateCreditCard"
	        @close="modals.updateCreditCard = false">

			<stripe-payment
	            api-url="stripe/update-card"
	            completed-redirect="/billing"
	            button-text="Update Card">
	        </stripe-payment>
		</modal>
	</template>

	<template v-if="subscription.gateway === 'PayPal'">
		<div class="text-center mt-3">
			<img :src="$appSettings.appUrl + '/assets/images/credit-cards/paypal.png'" width="100px">
		</div>

		<div class="mt-3 d-flex">
			<div>
				<p class="mb-2">Name:</p>
				<p class="mb-2">Email:</p>
			</div>
			<div class="ml-5">
				<p class="mb-2">{{ subscription.payment_method.first_name }} {{ subscription.payment_method.last_name }}</p>
				<p class="mb-2">{{ subscription.payment_method.email }}</p>
			</div>
		</div>
	</template>
</div>
</template>


<script>
export default {
	name: 'payment-method',
	
	props: ['subscription'],
	
    data() {
        return {
        	modals: {
        		updateCreditCard: false,
        	},
        };
    },

    computed: {
		creditCardBrand () {
		 	switch(this.subscription.payment_method.brand) {
				case "Visa": return 'visa';
				case "MasterCard": return 'master';
				case "American Express": return 'amex';
				case "Diners Club": return 'diners';
				case "Discover": return 'discover';	
				case "JCB": return 'jcb';						
		        default: return "unknown";
		    }
		}
	}
}
</script>