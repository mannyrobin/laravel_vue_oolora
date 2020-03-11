<template>
<div class="bg-gray p-3 h-100">

	<div class="card shadow-sm">

		<div class="card-header">
			<h4 class="card-title">Payments</h4>
		</div>


		<div class="border-top py-1 px-3 text-right">

			<dropdown class="btn-group" menu-right>
	            <button slot="heading" class="btn btn-light rounded-round btn-sm px-2 dropdown-toggle">
	            	<i class="fal fa-filter mr-1"></i> Filter By
	            </button>

	            <div class="px-3 py-2">
		            <form>
						<div class="custom-control custom-radio mb-3">
							<input type="radio" id="filter_payments" name="type_filter" class="custom-control-input"
								:value="'payments'" 
								v-model="tableParams.filter_type"
								@change="filterTable">
							<label class="custom-control-label" for="filter_payments">Payments Only</label>
						</div>

						<div class="custom-control custom-radio">
							<input type="radio" id="filter_refunds" name="type_filter" class="custom-control-input"
								:value="'refunds'" 
								v-model="tableParams.filter_type"
								@change="filterTable">
							<label class="custom-control-label" for="filter_refunds">Refunds Only</label>
						</div>
					</form>

					<div class="text-center">
						<button type="button" class="btn btn-sm btn-link text-body font-size-sm mt-3 text-uppercase" @click.prevent="tableParams.filter_type = null, filterTable()">Clear Filter</button>
					</div>
				</div>

	        </dropdown>                    

		</div>

	    <datatable ref="paymentstable"
	    	api-url="admin/payments"
	        :pagination-per-page="10"
	        :append-params="tableParams"
	        :fields-data="paymentFields">

	        <template slot="invoice-number-slot" slot-scope="props">
	        	<div>{{ props.rowData.invoice_number }}</div>
	        	<span v-if="props.rowData.refund" class="badge badge-pill badge-danger cursor-pointer" @click="showRefundPanel(props.rowData)">Refunded</span>
	        </template>

	        <template slot="payment-method-slot" slot-scope="props">
	        	<span v-if="props.rowData.payment_gateway === 'Stripe'">
	        		<img :src="$appSettings.appUrl + '/assets/images/credit-cards/' + creditCardBrand(props.rowData.card_brand) + '.svg'" width="22px"> xxxx {{ props.rowData.card_last4 }}
				</span>
				<span v-if="props.rowData.payment_gateway === 'PayPal'">{{ props.rowData.paypal_email }}</span>
	        </template>

	        <template slot="total-slot" slot-scope="props">
	        	<div class="font-weight-semibold">{{ props.rowData.currency_symbol }}{{ props.rowData.total }}</div>
	        </template>

	        <template slot="user-slot" slot-scope="props">
				<div class="d-flex align-items-center">
					<div class="mr-3">
						<img :src="props.rowData.user.avatar" class="rounded-circle" width="32" height="32" alt="">
					</div>
					<div>
						<div>{{ props.rowData.user.name }}</div>
						<div class="text-muted font-size-sm">via {{ props.rowData.payment_gateway }}</div>
					</div>
				</div>
	        </template>

	        <template slot="action-slot" slot-scope="props">
	        	<div class="list-icons">
					<a href="#" class="list-icons-item" @click.prevent="showInvoicePanel(props.rowData.id)" v-tooltip.hover title="View Invoice"><i class="far fa-file-contract"></i></a>
					<a v-if="!props.rowData.refund" href="#" class="list-icons-item" @click.prevent="modals.refundPayment = true, refundData.amount = props.rowData.total, refundData.invoice = props.rowData" v-tooltip.hover title="Refund Payment"><i class="far fa-envelope-open-dollar"></i></a>
					<a v-if="props.rowData.refund" href="#" class="list-icons-item" @click.prevent="showRefundPanel(props.rowData)" v-tooltip.hover title="Refund Details"><i class="far fa-comments-alt-dollar"></i></a>
				</div>
			</template> 

	    </datatable>


	    <!-- Refund Modal -->
	    <modal
	        title="Refund Payment"
	        submit-text="Process Refund"
	        size="small"
	        :show.sync="modals.refundPayment"        
	        :submit-loading="btnLoading.refundPayment"
	        @submit="refundPayment"
	        @close="modals.refundPayment = false">

	        <form @submit.prevent="refundPayment" data-vv-scope="form-refund" class="mt-3">

	            <div class="form-group">
	                <label>Amount <span class="font-weight-semibold text-muted">(Max: {{ refundData.invoice.total }})</span> </label>
	                <input type="text" name="amount"
						:class="['form-control', { 'is-invalid': errors.has('form-refund.amount') }]"
						v-validate="'required|max_value:'+refundData.invoice.total+'|decimal:2'"
						v-model="refundData.amount">
					<div class="invalid-feedback" v-show="errors.has('form-refund.amount')">{{ errors.first('form-refund.amount') }}</div>
	            </div>

	            <div class="form-group">
	                <label>Reason</label>
	                <textarea class="form-control" name="reason" v-model="refundData.reason" rows="5"></textarea>
	            </div>

	            <div class="font-weight-semibold mb-2">Subscription Cancellation</div>

				<div class="custom-control custom-radio">
					<input type="radio" id="cancel_subscription_true" name="cancel_subscription" :value="true" v-model="refundData.cancel_subscription" class="custom-control-input">
					<label class="custom-control-label" for="cancel_subscription_true">Cancel Immediately</label>
				</div>

				<div class="custom-control custom-radio">
					<input type="radio" id="cancel_subscription_false" name="cancel_subscription" :value="false" v-model="refundData.cancel_subscription" class="custom-control-input">
					<label class="custom-control-label" for="cancel_subscription_false">Do Not Cancel</label>
				</div>

	        </form>

	    </modal>

	</div>

</div>
</template>


<script>
import ViewInvoice from './ViewInvoice.vue';
import ViewRefund from './ViewRefund.vue';

export default {
	components: {
        ViewInvoice,
        ViewRefund
    },

	data() {
	    return {
	    	tableParams: {
	    		filter_type: null
	    	},

	    	refundData: {
	    		invoice: {},
	    		cancel_subscription: true,
	    	},

	    	btnLoading: {
                refundPayment: false
            },
            
            modals: {
                refundPayment: false
            },

	    	paymentFields: [
	            {
	                name: 'invoice-number-slot',
	                title: 'Invoice Number',
	                sortField: 'invoice_number',
	            },
	            {
	                name: 'total-slot',
	                title: 'Amount'
	            },
	            {
	                name: 'payment-method-slot',
	                title: 'Payment Method'
	            },      
	            {
	            	name: 'billed_on',
	                title: 'Paid On',
	                sortField: 'created_at',
	            },      
	            {
	                name: 'user-slot',
	                title: 'Paid By'
	            },	
	            {
	                name: 'action-slot',
	                title: '',
	                dataClass: 'text-right'
	            }
	        ]
	    };
	},

	methods: {
		showInvoicePanel(invoiceId) {
            this.$showPanel({
                component: ViewInvoice,
                props: {
                	'invoiceId': invoiceId
                },
                width: 980,
            });
        },

		showRefundPanel(payment) {
			this.$showPanel({
                component: ViewRefund,
                props: {
                	payment: payment
                },
                width: 400,
            });
        },

        filterTable() {
        	this.$refs.paymentstable.loadData();
        },

        refundPayment() {
    		this.$validator.validateAll('form-refund').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoading.refundPayment = true;

	    		axios.post('admin/payments/refund', this.refundData)
	            .then(response => {

	            	this.btnLoading.refundPayment = false;
                 	this.$alert.success(response.data.message);
                 	this.modals.refundPayment = false;

                 	this.$refs.paymentstable.loadData();

	            })
	            .catch(error => {

	            	this.btnLoading.refundPayment = false;

                    this.$backendErrors(error.response.data, 'form-refund');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

	            });

            });
        },

        creditCardBrand (brand) {
		 	switch(brand) {
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