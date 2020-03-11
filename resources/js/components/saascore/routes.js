import LayoutMaster from '../../components/layouts/Master.vue';
import LayoutMinimal from '../../components/layouts/Minimal.vue';

import Account from './account/Index.vue';
import Billing from './billing/Index.vue';
import ViewInvoice from './billing/ViewInvoice.vue';
import SubscriptionPlans from './billing/Plans.vue';
import SubscriptionCheckout from './billing/Checkout.vue';

const routes = [
    {
        path: '/account',
        component: LayoutMaster,
        children: [            
            {
                path: '/',
                component: Account
            },
        ]
    },

    {
        path: '/billing',
        component: LayoutMaster,
        beforeEnter: (to, from, next) => {
            
            if ( Vue.prototype.$can('access admin') )
                next({ path: '/dashboard' })

            // Continue normal
            next();

        },
        children: [
            {
                path: '/',
                component: Billing
            },
            {
                path: 'invoice/:invoiceId',
                component: ViewInvoice
            }
        ]
    },

    {
        path: '/billing/plans',
        component: LayoutMinimal,        
        beforeEnter: (to, from, next) => {
            
            if ( Vue.prototype.$can('access admin') )
                next({ path: '/dashboard' })

            // Continue normal
            next();

        },
        children: [            
            {
                path: '/',
                component: SubscriptionPlans,
                name: 'billing.plans'
            },
            {
                path: 'checkout/:planId',
                component: SubscriptionCheckout,
                name: 'billing.checkout'
            }
        ]
    }
];

export default routes