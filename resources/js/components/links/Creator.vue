<template>
<div class="container-fluid slideout-panel p-0 h-100 bg-gray">
    <div class="row no-gutters h-100">

        <div class="col-md-3 h-100" style="z-index: 1100;">

			<div class="panel-wrap has-header has-footer-lg border-right">

			    <div class="panel-header px-3 bg-white border-bottom d-flex justify-content-between align-items-center">
			        <div class="text-crop-1" v-if="!linkProcess.updated">
                        <img width="16" height="16" :src="linkData.favicon" alt="" class="mr-1"> 
                        <span>{{ linkData.title }}</span>
                    </div>
			        <button type="button" class="ml-auto close" @click="$router.push('/links')">&times;</button>
			    </div>

			    <div class="panel-body p-3 bg-white">

			 		<placeholder v-if="showPlaceholder" v-for="n in 5" :key="n" class="mt-3 mb-5" heading :heading-image="false"></placeholder>


                    <form v-if="!showPlaceholder && !linkProcess.updated" data-vv-scope="form_save_link">
                        
                        <div class="form-group mb-4">
                            <label>Destination URL</label>
                            <input type="text" name="url"
                                :class="[{ 'is-invalid': errors.has('form_save_link.url') }, 'form-control']"
                                v-validate="{required: true, url: 'require_protocol'}"
                                v-model="linkData.url"
                                @blur="checkLink">

                            <div class="invalid-feedback" v-show="errors.has('form_save_link.url')">{{ errors.first('form_save_link.url') }}</div>
                        </div>


                        <div class="form-group mb-4">
                            <label>Slug</label>
                        	<div class="input-group">
								<input type="text" class="form-control" disabled :value="'https://' + linkDomain + '/'">

								<input type="text" name="slug"
                                    :class="[{ 'is-invalid': errors.has('form_save_link.slug') }, 'form-control']"
                                    v-validate="'required|alpha_dash'"
                                    v-model="linkData.slug">
							</div>

                            <div class="invalid-feedback" v-show="errors.has('form_save_link.slug')">{{ errors.first('form_save_link.slug') }}</div>
                        </div>


                        <div class="bg-light py-3 px-2 mb-4 rounded border">

                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="shorten_only" :disabled="linkData.iframe_blocked" v-model="linkData.shorten_only">
                                <label class="custom-control-label" for="shorten_only">Shorten URL Only</label>
                            </div>

                            <template v-if="!linkData.shorten_only">

                                <div v-if="!linkData.iframe_blocked" class="form-group mt-3 mb-0">
                                    <label>Call to Action</label>
                                    <router-link class="font-size-sm float-right" :to="'/call-to-actions/creator'">Create CTA</router-link>
                                    <form-select v-model="linkData.cta" clearable >

                                        <select-option 
                                            v-for="item in ctaData" 
                                            :key="item.id" 
                                            :label="item.name" 
                                            :value="item.id">
                                        </select-option>
                                    </form-select>
                                </div>

                            </template>

                            <div v-if="linkData.iframe_blocked" class="text-warning mt-3">We can't show call to actions or custom scripts on this url</div>

                        </div>


                        <div class="form-group mb-4">
                            <label>Custom Domain</label>
                            <a href="#" class="font-size-sm float-right" @click.prevent="modals.addDomain = true">Add New</a>
                            <form-select name="campaign" 
                                clearable
                                v-model="linkData.domain_id">
                                
                                <select-option
                                    v-for="item in domains"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                    
                                    {{ item.name }}

                                </select-option>
                            </form-select>    
                        </div>


                        <div class="form-group mb-4">
                            <label>Campaign</label>
                            <a href="#" class="font-size-sm float-right" @click.prevent="modals.addCampaign = true">Create New</a>
                            <form-select name="campaign" 
                                clearable
                                v-model="linkData.campaign_id">
                                
                                <select-option
                                    v-for="item in campaigns"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                    
                                    {{ item.name }}

                                </select-option>
                            </form-select>    
                        </div>


                        <div class="form-group mb-4">
                            <label>Pixels</label>
                            <a href="#" class="font-size-sm float-right" @click.prevent="modals.addPixel = true">Add New Pixel</a>
                            <form-select
                                multiple
                                collapse-tags
                                v-model="linkData.pixels">

                                <select-option 
                                    v-for="item in pixels" 
                                    :key="item.id" 
                                    :label="item.name" 
                                    :value="item.id">
                                </select-option>
                            </form-select>
                        </div>

                        <div v-if="!linkData.iframe_blocked" class="form-group">
                            <label>Custom Scripts</label>
                            <a href="#" class="font-size-sm float-right" @click.prevent="modals.addScript = true">Add New Script</a>
                            <form-select
                                multiple
                                collapse-tags
                                v-model="linkData.scripts">

                                <select-option 
                                    v-for="item in scripts" 
                                    :key="item.id" 
                                    :label="item.name" 
                                    :value="item.id">
                                </select-option>
                            </form-select>
                        </div>

                    </form>

                    <transition name="fade">
                        <div class="text-center mt-5" v-if="linkProcess.updated">
                            <h5 class="mb-3">Share Your Link</h5>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" readonly 
                                    :value="'https://' + linkDomain + '/' + linkData.slug">

                                <div class="input-group-append">
                                    <button class="btn btn-primary" @click="copyLink('https://' + linkDomain +'/'+ linkData.slug)" type="button">Copy</button>
                                </div>
                            </div>

                            <p>Now go ahead and share this smart link with your audience on social, email, web or any other channel you want.</p>

                            <div>
                                <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + 'https://' + linkDomain + '/' + linkData.slug" target="_blank"><i class="fab fa-facebook-square fa-fw fa-2x" style="color: #3b5998;"></i></a>
                                <a :href="'https://twitter.com/home?status=' + 'https://' + linkDomain + '/' + linkData.slug" target="_blank"><i class="fab fa-twitter-square fa-fw fa-2x" style="color: #00aced;"></i></a>
                                <a :href="'https://plus.google.com/share?url=' + 'https://' + linkDomain + '/' + linkData.slug" target="_blank"><i class="fab fa-google-plus-square fa-fw fa-2x" style="color: #d34836;"></i></a>
                                <a :href="'https://www.linkedin.com/shareArticle?mini=true&url=' + 'https://' + linkDomain + '/' + linkData.slug + '&title=&summary=&source='" target="_blank"><i class="fab fa-linkedin fa-fw fa-2x" style="color: #007bb6;"></i></a>
                                <a :href="'https://pinterest.com/pin/create/button/?url=&media=' + 'https://' + linkDomain + '/' + linkData.slug + '&description='" target="_blank"><i class="fab fa-pinterest-square fa-fw fa-2x" style="color: #cb2027;"></i></a>
                            </div>
                        </div>
                    </transition>

			    </div>

			    <div class="panel-footer">
			    	<div class="footer-lg bg-white border-top d-flex justify-content-center align-items-center">
				    	
                        <button-spinner v-if="!linkProcess.updated" :loading="btnLoadingSave" class="btn-primary" @click="updateLink">Save Smart Link</button-spinner>
                        <router-link v-if="linkProcess.updated" class="btn btn-light" to="/links"><i class="fal fa-long-arrow-left"></i> Back to Links</router-link>

                    </div>
			    </div>

			</div>

        </div>
        <div class="col-md-9 h-100 overflow-hidden">

            <div class="px-4 py-5" v-if="linkProcess.checking">
                <placeholder class="mb-3" heading></placeholder>
                <placeholder text></placeholder>

                <div class="row mt-5">
                    <div class="col-md-6">
                        <placeholder class="mb-3" image></placeholder>
                        <placeholder text></placeholder>
                    </div>
                    <div class="col-md-6">
                        <placeholder class="mb-3" image></placeholder>
                        <placeholder text></placeholder>
                    </div>
                </div>
            </div>

            <template v-if="!showPlaceholder && !linkProcess.checking">
                
                <div v-if="linkProcess.inValid" class="h-100 bg-white d-flex justify-content-center align-items-center">
                    <div class="empty-state text-center">
                        <i class="far fa-link fa-3x mb-2 text-gray-600"></i>
                        <h5 class="font-family-base mb-1 font-weight-semibold">Invalid URL</h5>
                        <p>The URL you have entered is not active or contains invalid <br>characters, please check the URL and try again.</p>
                    </div>
                </div>

                <template v-else>
                    <div v-if="linkData.iframe_blocked" class="h-100 bg-white d-flex justify-content-center align-items-center">
                        <div class="empty-state text-center">
                            <i class="fal fa-window-restore fa-3x mb-2 text-gray-600"></i>
                            <h5 class="font-family-base mb-1 font-weight-semibold">CTA Not Supported</h5>
                            <p>This URL doesn't support call to actions or custom scripts. <br>However, you can still create a shortened smart link and add tracking pixels.</p>
                        </div>
                    </div>

                    <iframe v-if="!linkData.iframe_blocked" :src="linkProcess.iframeUrl" class="w-100 h-100" frameborder="0"></iframe>
                </template>

            </template>

            <template v-if="!linkData.shorten_only">
                <cta-preview v-if="linkData.cta" :cta-id="linkData.cta"></cta-preview>
            </template>

        </div>

    </div>

    
    <add-pixel-modal :open.sync="modals.addPixel" @done="getPixels"></add-pixel-modal>
    <add-script-modal :open.sync="modals.addScript" @done="getScripts"></add-script-modal>
    <add-campaign-modal :open.sync="modals.addCampaign" @done="getCampaigns"></add-campaign-modal>


</div>
</template>


<script>
import AddPixelModal from '../pixels/AddModal.vue';
import AddScriptModal from '../custom-scripts/AddModal.vue';
import addCampaignModal from '../campaigns/AddModal.vue';
import addDomainModal from '../domains/AddModal.vue';

export default {    
    components: {
        AddPixelModal,
        AddScriptModal,
        addCampaignModal,
        addCampaignModal
    },

    data() {
        return {
            showPlaceholder: false,
            
            modals: {
                addPixel: false,
                addScript: false,
                addCampaign: false
            },

            btnLoadingSave: false,

            linkData: {
                shorten_only: false
            },

            linkProcess: {
                updated: false,
                checking: false,
                inValid: false,
                iframeUrl: ''
            },

            campaigns: [],

            pixels: [],

            ctaData: [],

            scripts: [],

            domains: [],

            selectedDomain: null
        };
    },

    computed: {
        linkDomain: function () {
            return (this.selectedDomain ? this.selectedDomain : this.$appSettings.linkShortenDomain);
        }
      },

    watch: {
        'linkData.domain_id'(val) {
            if (val) {

                var result = this.domains.filter(obj => {
                    return obj.id === val
                })

                this.selectedDomain = result[0].name;
            } else {
                this.selectedDomain = null;
            }
        },

        'linkData.iframe_blocked'(val) {
            // Watch iframe option for the url 
            // and empty any selected CTA or Scripts if it's blocked
            if (val) {
                this.linkData.cta = [];
                this.linkData.scripts = [];

                // set shorten url only
                this.linkData.shorten_only = true;
            }
        }
    },

    created() {
    	this.getLink();
        this.getCampaigns();
        this.getDomains();
        this.getPixels();
        this.getScripts();
        this.getCta();
    },

    methods: {
    	getLink() {
            this.showPlaceholder = true;

            axios.get('links/' + this.$route.params.linkId)
            .then(response => {

                this.showPlaceholder = false;
                this.linkData = response.data;
                this.linkProcess.iframeUrl = response.data.url;

            })
            .catch(error => {

                // Redirect if the link doesn't exist
                if ( error.response.status === 404 )
                    return this.$router.push('/links');

            	this.$alert.error(error.response.data.message);

            });
        },

        // On url input blur get data for the given link
        checkLink(event) {
            this.linkProcess.checking = true;

            axios.post('links/check', {'url': event.target.value})
            .then(response => {

                this.linkProcess.checking = false;
                this.linkProcess.inValid = false;
                this.linkProcess.iframeUrl = response.data.url;

                // Replace the current link data with the new data from the check
                this.linkData = Object.assign({}, this.linkData, response.data);

            })
            .catch(error => {

                this.linkProcess.checking = false;
                this.linkProcess.inValid = true;

                this.$backendErrors(error.response.data, 'form_save_link');

                // Show error message if there is any
                if ( error.response.data.message )
                    this.$alert.error(error.response.data.message);

            });
        },

        updateLink() {
            this.$validator.validateAll('form_save_link').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoadingSave = true;

                axios.put('links/'+ this.linkData.id, this.linkData)
                .then(response => {

                    this.btnLoadingSave = false;
                    this.linkProcess.updated = true;
                    this.$alert.success(response.data.message);

                })
                .catch(error => {

                    this.btnLoadingSave = false;
                    this.$backendErrors(error.response.data, 'form_save_link');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        },

        copyLink(text) {
            
            self = this;

            this.$copyText(text).then(function (e) {
                self.$alert.success('Smart link copied to your clipboard ' + text);
            }, function (e) {
               self.$alert.error('Unable to copy the link to your clipboard');
            })
        },

        getDomains() {
            axios.get('domains')
            .then(response => {
                this.domains = response.data.data;

            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        }, 

    	getCampaigns() {
            axios.get('campaigns')
            .then(response => {
                this.campaigns = response.data.data;
            })
            .catch(error => {
            	this.$alert.error(error.response.data.message);
            });
        },        

        getPixels() {
            axios.get('pixels')
            .then(response => {
                this.pixels = response.data.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },   

        getScripts() {
            axios.get('custom-scripts')
            .then(response => {
                this.scripts = response.data.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        getCta() {
            axios.get('cta')
            .then(response => {
                this.ctaData = response.data.data;
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },
    }
}
</script>


<style scoped>
    >>> #popup .modal-dialog {
        left: 13% !important;
    }
</style>