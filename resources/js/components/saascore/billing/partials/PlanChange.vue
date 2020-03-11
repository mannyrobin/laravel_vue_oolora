<template>
<div>

    <div class="text-center mb-5">
        <h2 class="mb-0">Changing Your Plan?</h2>
        <p class="text-muted lead mb-0">Choose the plan that is right for you</p>
    </div>

    <div v-if="plans.length" class="card-deck mb-3 text-center">
        
        <div v-for="plan in plans" class="card mb-4 border">
            
            <div class="card-header">
                <h4 class="mb-0 font-weight-600">{{ plan.name }}</h4>
            </div>

            <div class="card-body">
                
                <h1 class="card-title font-family-base">
                    <sup>{{ $appSettings.currencySymbol }}</sup>{{ plan.price }} <small class="text-muted font-size-sm">/ {{ plan.interval }}</small></span>
                </h1>

                <hr>

                <ul class="list-unstyled">
                    <li class="mb-2" v-for="feature in plan.features">
                        <span v-if="feature.value != '0' && feature.value != 'INCLUDE' " class="font-weight-bold">{{ feature.value }}</span> <del v-if="feature.value == '0'">{{ feature.name }}</del><span v-else>{{ feature.name }}</span>
                    </li>
                </ul>

            </div>

            <div class="card-footer bg-white">
                <button-spinner :loading="isBtnLoading === plan.id" v-if="plan.id != subscription.plan.id" @click="changePlan(plan.id)" type="button" class="btn btn-lg btn-block btn-primary">Subscribe Now</button-spinner>
                <button v-if="plan.id == subscription.plan.id" disabled type="button" class="btn btn-lg btn-block btn-outline-secondary">Your Current Plan</button>
            </div>

        </div>

    </div>

    <div v-else class="w-50 m-auto alert alert-info">
        <h6 class="mb-0"><i class="fal fa-exclamation-circle"></i> No Available Plans</h6>
        <p class="mb-0">Unfortunately there are no available subscription plans at this time.</p>
    </div>

</div>
</template>

<script>
export default {
	props: ['plans', 'subscription'],

	data() {
        return {
        	isBtnLoading: null,
        };
    },

    methods: {
        changePlan (planId) {
        	this.isBtnLoading = planId;

            // Set the API URL based on the gateway
            let gatewayUrl = '';
            if ( this.subscription.gateway === 'Stripe' )
                gatewayUrl = 'stripe/update';


        	axios.post(gatewayUrl, {plan_id: planId, gateway_subscription_id: this.subscription.gateway_subscription_id})
            .then(response => {
            	this.isBtnLoading = null;
            	this.$alert.success(response.data.message);

            	window.location.href = '/billing';
            })
            .catch(error => {
            	this.isBtnLoading = null;
            	this.$alert.error(error.response.data.message);
            }); 
		}
    }
}
</script>