<template>
<div>
	
	<div class="d-flex justify-content-between align-items-center">
		<h6 class="heading-weight-lighter mb-0">{{ subscription.plan.name }} Plan</h6>
		<router-link v-if="subscription.payment_method != null && subscription.gateway != 'PayPal'" to="/billing/plans?action=change" class="btn btn-outline-primary btn-sm">Change Plan</router-link>
	</div>

	<p class="mt-2 mb-4 text-crop-2">{{ subscription.plan.description }}</p>

	<div class="mt-4 d-flex">
		<div>
			<p class="mb-2">Price:</p>
			<p class="mb-2">Billing Cycle:</p>
		</div>
		<div class="ml-5">
			<p class="mb-2">{{ $appSettings.currencySymbol }}{{ subscription.plan.price }}</p>
			<p class="mb-2 text-capitalize">{{ subscription.plan.interval }}ly</p>
		</div>
	</div>

</div>
</template>


<script>
export default {
	name: 'subscription-plan',
	
	props: ['subscription']
}
</script>