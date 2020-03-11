<template>
<div class="page-invoice-view bg-gray h-100 p-3">

	<div class="d-flex justify-content-between align-items-center mb-2">
		<h4 class="mb-0 cursor-pointer" @click="$router.push('/billing')"><i class="far fa-long-arrow-left"></i> Invoice Details</h4>
		<div>
			<button class="btn btn-sm btn-light" type="button" v-print="'#printMe'"><i class="fal fa-print fa-fw"></i> Print</button>
			<a :href="$appSettings.appUrl + '/pdf/invoice/' + invoice.id" class="btn btn-sm btn-light"><i class="fal fa-download fa-fw"></i> Download</a>
		</div>
	</div>


	<div class="card shadow-sm">
		<div id='printMe' class="py-3">

			<div class="d-flex justify-content-between px-3">
				<div style="width: 180px;" class="mr-3">
					
					<img v-if="!showPlaceholder" class="img-fluid" :src="$appSettings.logoDark" :alt="$appSettings.appName">

					<placeholder v-if="showPlaceholder" class="w-50" heading heading-single :heading-image="false"></placeholder>

				</div>
				<div>

					<placeholder v-if="showPlaceholder" class="w-50 ml-auto" heading :heading-image="false"></placeholder>

					<template v-if="!showPlaceholder">
						<h2 class="text-uppercase font-base" style="letter-spacing: 0.5px;">Invoice</h2>
						<p class="mb-0">Number: {{ invoice.invoice_number }}</p>
						<p class="mb-1">Date: {{ invoice.billed_on }}</p>
						<span class="badge badge-success d-block">Paid via {{ displayPaymentMethod }}</span>
					</template>

				</div>
			</div>

			<div class="container px-3 py-4">
				<div class="row">
					<div class="col-6">

						<template v-if="!showPlaceholder">
							<div class="text-uppercase mb-2">Invoice From</div>
							<address class="mb-0">
								<span class="font-weight-semibold">{{ $appSettings.appName }}</span><br />{{ $appSettings.appUrl }}
							</address>
						</template>

						<placeholder v-if="showPlaceholder" class="w-50" text :text-lines="5"></placeholder>

					</div>
					<div class="col-6">

						<template v-if="!showPlaceholder">
							<div class="text-uppercase mb-2">Invoice To</div>
							<address class="mb-0">
								<span class="font-weight-semibold">{{ invoice.user.name }}</span><br />{{ invoice.user.email }}
							</address>
						</template>

						<placeholder v-if="showPlaceholder" class="w-50" text :text-lines="5"></placeholder>

					</div>				
				</div>
			</div>


			<table class="table border-top border-bottom">
				<thead>
					
					<tr v-if="!showPlaceholder" class="bg-light">
						<th scope="col">Description</th>
						<th scope="col">Period</th>
						<th scope="col">Amount</th>
					</tr>

					<tr v-if="showPlaceholder">
						<th scope="col"><placeholder class="w-50" heading heading-single :heading-image="false"></placeholder></th>
						<th scope="col" v-for="n in 2"><placeholder class="w-50 m-auto" heading heading-single :heading-image="false"></placeholder></th>
					</tr>

				</thead>
				<tbody>

					<tr v-if="!showPlaceholder" v-for="line in invoice.lines">
						<td>{{ line.description }}</td>
						<td>{{ line.period_start }} – {{ line.period_end }}</td>
						<td>{{ invoice.currency_symbol }}{{ line.amount }}</td>
					</tr>

					<tr v-if="showPlaceholder">
						<td><placeholder class="w-75" text :text-lines="1"></placeholder></td>
						<td v-for="n in 2" ><placeholder class="w-25 m-auto" text :text-lines="1"></placeholder></td>
					</tr>

				</tbody>
			</table>

			<table class="table w-25 ml-auto">
				
				<tbody v-if="!showPlaceholder">
					<tr>
						<td>Subtotal:</td>
						<td class="text-right">{{ invoice.currency_symbol }}{{ invoice.subtotal }}</td>
					</tr>
					<tr>
						<td class="font-weight-bold">Total:</td>
						<td class="font-weight-bold text-right">{{ invoice.currency_symbol }}{{ invoice.total }}</td>
					</tr>
				</tbody>

				<tbody v-if="showPlaceholder">
					<tr v-for="n in 2">
						<td><placeholder class="w-75" heading heading-single :heading-image="false"></placeholder></td>
						<td class="text-right"><placeholder class="w-50 ml-auto" text :text-lines="1"></placeholder></td>
					</tr>
				</tbody>

			</table>

		</div>
	</div>

</div>
</template>


<script>
export default {
    data() {
        return {
        	showPlaceholder: false,
        	appSettings: window.appSettings,
        	invoice: {}
        };
    },

    created () {
    	this.getInvoice();
    },

	computed: {
		displayPaymentMethod: function () {
			if ( this.invoice.payment_gateway === "Stripe")
				return "Credit Card"

			return this.invoice.payment_gateway;
		}
	},

    methods: {
    	getInvoice () {
            this.showPlaceholder = true;

            axios.get('invoices/' + this.$route.params.invoiceId)
            .then(response => {
            	this.showPlaceholder = false;
                this.invoice = response.data;
            })
            .catch(error => {
            	// Redirect to billing page if the invoice is invalid
            	if ( error.response.status === 404 )
            		return this.$router.push('/billing');

            	this.$alert.error(error.response.data.message);
            }); 
        }
    }
}
</script>