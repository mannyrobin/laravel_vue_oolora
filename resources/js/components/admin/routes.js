import Layout from './layouts/Layout.vue';

import Dashboard from './Dashboard.vue';

import MembershipUsers from './membership/Users.vue';
import MembershipPayments from './membership/Payments.vue';
import MembershipPlans from './membership/Plans.vue';
import MembershipVouchers from './membership/Vouchers.vue';

import SettingsGeneral from './settings/General.vue';
import SettingsBranding from './settings/Branding.vue';
import SettingsCustomCode from './settings/CustomCode.vue';
import SettingsMail from './settings/Mail.vue';
import SettingsPaymentGateway from './settings/PaymentGateway.vue';

const routes = [

{
    path: '/admin',
    component: Layout,
    children: [            
        {
            path: '/',
            component: Dashboard,
        },
        {
            path: 'membership/payments',
            component: MembershipPayments,
        },            
        {
            path: 'membership/users',
            component: MembershipUsers,
        },
        {
            path: 'membership/plans',
            component: MembershipPlans,
        },

        {
            path: 'membership/vouchers',
            component: MembershipVouchers,
        },
        {
            path: 'settings/general',
            component: SettingsGeneral,
        },
        {
            path: 'settings/custom-code',
            component: SettingsCustomCode,
        },
        {
            path: 'settings/payment-gateway',
            component: SettingsPaymentGateway,
        }, 
        {
            path: 'settings/branding',
            component: SettingsBranding,
        },
        {
            path: 'settings/mail',
            component: SettingsMail,
        },                                                          
    ]
}

];

export default routes