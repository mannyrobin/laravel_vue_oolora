<template>
<div>

    <div v-if="subscription.free_trial_ended" class="text-center mb-5">
        <h2 class="mb-0">Free Trial Ended</h2>
        <p class="text-muted lead mb-0">Your free trial has ended, choose a plan to continue using our service</p>
    </div>

    <div v-else class="text-center mb-5">
        <h2 class="mb-0">Pricing Plans</h2>
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

                <button
                    type="button"
                    @click="$router.push({ name: 'billing.checkout', params: { planId: plan.id }})"
                    :class="['btn btn-lg btn-block btn-primary', { 'btn-outline-primary': subscription.length === 0 && plan.trial_period_days }]">
                    <span v-if="subscription.length === 0 && plan.trial_period_days">Try free for {{ plan.trial_period_days }} days</span>
                    <span v-else>Subscribe Now</span>
                </button>

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
	props: ['plans', 'subscription']
}
</script>