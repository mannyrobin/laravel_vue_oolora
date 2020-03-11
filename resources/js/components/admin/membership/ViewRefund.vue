<template>
<div class="panel-wrap has-header">
	<div class="panel-header px-3 bg-white border-bottom d-flex justify-content-between align-items-center">
		<h6 class="mb-0 cursor-pointer" @click="closePanel"><i class="far fa-long-arrow-left"></i> Refund Details</h6>
	</div>
	<div class="panel-body p-2 bg-gray">

		<div class="card p-3 shadow-sm">

			<div class="d-flex justify-content-center mb-4">
				<div class="d-flex align-items-center">
					<div class="mr-3">
						<img :src="payment.user.avatar" class="rounded-circle" width="32" height="32" alt="">
					</div>
					<div>
						<div>{{ payment.user.name }}</div>
						<div class="text-muted font-size-sm">via {{ payment.payment_gateway }}</div>
					</div>
				</div>
			</div>

			<div class="d-flex">
				<div class="font-weight-semibold">
					<p class="mb-2">Amount:</p>
					<p class="mb-2">Invoice #:</p>
					<p class="mb-2">Date Issued:</p>
					<p class="mb-2">Refunded By:</p>
				</div>
				<div class="ml-5">
					<p class="mb-2">{{ payment.currency_symbol }}{{ payment.refund.amount }}</p>
					<p class="mb-2">{{ payment.invoice_number }}</p>
					<p class="mb-2">{{ payment.refund.created_at }}</p>
					<p class="mb-2"><span class="badge badge-info">{{ payment.refund.user.name }}</span></p>
				</div>
			</div>

			<div class="font-weight-semibold">Reason:</div>
			<p>{{ payment.refund.reason }}</p>
		</div>

	</div>
</div>
</template>


<script>
export default {
	name: 'view-refund',
	
	props: ['payment'],

    data() {
        return {};
    },

    methods: {
    	closePanel () {
            this.$emit("closePanel", {});
        }
    }
}
</script>