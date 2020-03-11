import NotFound  from './components/errors/404.vue';
import FeatureUpgrade  from './components/errors/Upgrade.vue';
import LayoutMaster from './components/layouts/Master.vue';
import LayoutMinimal from './components/layouts/Minimal.vue';

import Dashboard from './components/Dashboard.vue';
import Links from './components/links/Links.vue';
import LinksCreator from './components/links/Creator.vue';
import Campaigns from './components/campaigns/Campaigns.vue';
import Pixels from './components/pixels/Pixels.vue';
import Domains from './components/domains/Domains.vue';
import CallToActions from './components/call-to-actions/CallToActions.vue';
import CTACreator from './components/call-to-actions/Creator.vue';
import CustomScripts from './components/custom-scripts/CustomScripts.vue';
import Analytics from './components/analytics/Analytics.vue';

const routes = [
	{ path: '*', component: NotFound },

    {
        path: '/',
        component: LayoutMaster,
        children: [
            {
                path: 'dashboard',
                component: Dashboard,
                meta: { featureCheck: 'clicks' }
            },            
            {
                path: 'dashboard/feature-upgrade',
                component: FeatureUpgrade
            },              
            {
                path: 'campaigns',
                component: Campaigns,
                meta: { featureCheck: 'campaigns' }
            },            
            {
                path: 'links',
                component: Links,
                name: 'links',
                meta: { featureCheck: 'links' }
            },
            {
                path: 'pixels',
                component: Pixels,
                meta: { featureCheck: 'pixels' }
            },
            {
                path: 'domains',
                component: Domains,
                meta: { featureCheck: 'domains' }
            },            
            {
                path: 'custom-scripts',
                component: CustomScripts,
                meta: { featureCheck: 'custom_scripts' }
            },            
            {
                path: 'call-to-actions',
                component: CallToActions,
                meta: { featureCheck: 'call_to_actions' }
            },           
            {
                path: 'analytics',
                component: Analytics,
                meta: { featureCheck: 'analytics' }
            },            
            {
                path: 'analytics/:linkId',
                component: Analytics,
                name: 'analytics',
                meta: { featureCheck: 'analytics' }
            }
        ]
    },

    {
        path: '/links',
        component: LayoutMinimal,
        meta: { featureCheck: 'links' },
        children: [
            {
                path: 'creator/:linkId',
                component: LinksCreator,
                name: 'links.creator',
                meta: { featureCheck: 'links' }
            },
        ]
    },
    {
        path: '/call-to-actions',
        component: LayoutMinimal,
        meta: { featureCheck: 'call_to_actions' },
        children: [
            {
                path: 'creator',
                component: CTACreator,
                meta: { featureCheck: 'call_to_actions' }
            },            
            {
                path: 'creator/:ctaId',
                component: CTACreator,
                name: 'cta.creator',
                meta: { featureCheck: 'call_to_actions' }
            },
        ]
    },

];

export default routes