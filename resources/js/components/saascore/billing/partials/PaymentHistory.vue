<template>
<div class="mt-4">
	
	<h6 class="heading-weight-lighter mb-2 pl-3">Payment History</h6>

    <datatable
    	api-url="invoices"
        :pagination-per-page="8"
        :fields-data="invoicesFields">

        <template slot="invoice-number-slot" slot-scope="props">
        	<div>{{ props.rowData.invoice_number }}</div>
        	<span v-if="props.rowData.refund" class="badge badge-pill badge-danger cursor-pointer" @click="showRefundPanel(props.rowData)">Refunded</span>
        </template>

        <template slot="total-slot" slot-scope="props">
        	{{ props.rowData.currency_symbol }}{{ props.rowData.total }}
        </template>

        <template slot="payment-method-slot" slot-scope="props">
        	<span v-if="props.rowData.payment_gateway === 'Stripe'">
        		<img :src="$appSettings.appUrl + '/assets/images/credit-cards/' + creditCardBrand(props.rowData.card_brand) + '.svg'" width="22px"> xxxx {{ props.rowData.card_last4 }}
			</span>
			<span v-if="props.rowData.payment_gateway === 'PayPal'">{{ props.rowData.paypal_email }}</span>
        </template>

        <template slot="action-slot" slot-scope="props">
        	    
        	<div class="list-icons">
        		<router-link class="list-icons-item" :to="'billing/invoice/' + props.rowData.id" v-tooltip.hover title="View Invoice"><i class="far fa-file-contract"></i></router-link>
        		<a :href="$appSettings.appUrl + '/pdf/invoice/' + props.rowData.id" class="list-icons-item" v-tooltip.hover title="Download Invoice"><i class="far fa-download"></i></a>
			</div>

        </template>
    </datatable>

</div>
</template>

<script>
import ViewRefund from './ViewRefund.vue';

export default {
	name: 'payment-history',
	
	components: {
        ViewRefund
    },

	data() {
	    return {
	        invoicesFields: [
	            {
	                name: 'invoice-number-slot',
	                title: 'Invoice Number',
	                sortField: 'invoice_number',
	            },
	            {
	            	name: 'billed_on',
	                title: 'Billed On',
	            },	            
	            {
	            	name: 'total-slot',
	                title: 'Total',
	            },	            
	            {
	                name: 'payment-method-slot',
	                title: 'Payment Method'
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
		showRefundPanel(payment) {
			this.$showPanel({
                component: ViewRefund,
                props: {
                	payment: payment
                },
                width: 400,
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