@include('frontend.header')

    <section id="home" class="bg-light">
        <div class="container text-center">

        	<div class="m-auto mt-5 mb-3" style="max-width: 600px;">
	            <h2>Add Call to Actions to the links you share</h2>
	            <p class="lead">Increase conversions, sales and traffic by adding branded call-to-actions and retargeting pixels on every link you share</p>
				<a href="/register/" class="btn btn-primary mt-3">Get Started For Free</a>
	        </div>

			<div class="browser-window mt-5">
			    <div class="container-fluid bar bg-light">
			        <div class="row">
			            <div class="col-3 pl-4">
			                <span class="dot" style="background:#ff4444;"></span>
			                <span class="dot" style="background:#FDD800;"></span>
			                <span class="dot" style="background:#5AC05A;"></span>
			            </div>
			            <div class="col-7">
			                <input type="text" readonly>
			            </div>
			        </div>                        
			    </div>

			    <div class="browser-content">
			    	<img src="/assets/images/analytics.png" class="img-fluid" alt="">
			    </div>
			</div>

        </div>
    </section>


	<div class="page-content">

	    <section id="how-it-works" class="text-center pos-r">
	        <div class="container">
	            <div class="row">
	                <div class="col-lg-6 col-md-10 ml-auto mr-auto mb-5">
	                    <div class="section-title">
	                        <h2 class="title">How it Works</h2>
	                    </div>
	                </div>
	            </div>
	            <div class="row">
	                <div id="svg-container">
	                    <svg id="svgC" width="100%" height="100%" viewBox="0 0 620 120" preserveAspectRatio="xMidYMid meet"></svg>
	                </div>
	                <div class="col-lg-4 col-md-12">
	                    <div class="work-process">
	                        <div class="box-loader"> <span></span>
	                            <span></span>
	                            <span></span>
	                        </div>
	                        <div class="step-num-box">
	                            <div class="step-icon">
	                            	<img alt="" class="img-fluid" src="{{ url('assets/frontend/images/light-bulb.png') }}">
	                            </span>
	                            </div>
	                            <div class="step-num">1</div>
	                        </div>
	                        <div class="step-desc">
	                            <h5 style="font-weight: 600;">Find a useful link to share</h5>
	                            <p class="mb-0">Get the link to any website you want to share with your audience. It can be a blog post, a news article or anything from any website, you don't need to be associated with that website at all.</p>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-lg-4 col-md-12 md-mt-5">
	                    <div class="work-process">
	                        <div class="box-loader"> <span></span>
	                            <span></span>
	                            <span></span>
	                        </div>
	                        <div class="step-num-box">
	                            <div class="step-icon">
	                            	<img alt="" class="img-fluid" src="{{ url('assets/frontend/images/link.png') }}">
	                            </span>
	                            </div>
	                            <div class="step-num">2</div>
	                        </div>
	                        <div class="step-desc">
	                            <h5 style="font-weight: 600;">Optimize Your Link</h5>
	                            <p class="mb-0">Add your re-targeting pixels, Call-to-Action popups, opt-in forms or any custom scripts that will help you track, retarget or convert your audience.</p>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-lg-4 col-md-12 md-mt-5">
	                    <div class="work-process">
	                        <div class="step-num-box">
	                            <div class="step-icon">
	                            	<img alt="" class="img-fluid" src="{{ url('assets/frontend/images/browser.png') }}">
	                            </span>
	                            </div>
	                            <div class="step-num">3</div>
	                        </div>
	                        <div class="step-desc">
	                            <h5 style="font-weight: 600;">Share it with your audience</h5>
	                            <p class="mb-0">Simply share your smart link on social media networks, email newsletters, or any other way choose. Anyone who visits that link will see your call-to-action.</p>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>


	    <section id="features" class="pos-r o-hidden">
	        <!--<div class="bg-animation">
	            <img class="zoom-fade" src="images/pattern/03.png" alt="">
	        </div>-->
	        <div class="container">
	            <div class="row">
	                <div class="col-sm-12">
	                    <div class="tab style-2">

	                        <nav>
	                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
	                                <a class="nav-item nav-link active" id="nav-tab1" data-toggle="tab" href="#tab1-1" role="tab" aria-selected="true"><i class="fal fa-layer-group"></i> <h5 style="font-weight: 600;">Campaign Manager</h5></a> 
	                                <a class="nav-item nav-link" id="nav-tab2" data-toggle="tab" href="#tab1-2" role="tab" aria-selected="false"><i class="fal fa-window-restore"></i> <h5 style="font-weight: 600;">Call to Actions</h5></a>
	                                <a class="nav-item nav-link" id="nav-tab3" data-toggle="tab" href="#tab1-3" role="tab" aria-selected="false"><i class="fal fa-location-arrow"></i> <h5 style="font-weight: 600;">Retargeting Pixels</h5></a>
	                                <a class="nav-item nav-link" id="nav-tab4" data-toggle="tab" href="#tab1-4" role="tab" aria-selected="false"><i class="fal fa-chart-line"></i> <h5 style="font-weight: 600;">Advance Analytics</h5></a>
	                            </div>
	                        </nav>

	                        <div class="tab-content" id="nav-tabContent">
	                            <div role="tabpanel" class="tab-pane fade show active" id="tab1-1">
	                                <div class="row align-items-center">
	                                    <div class="col-lg-6 col-md-12">
	                                        <img class="img-fluid" src="{{ url('assets/frontend/images/campaings.png') }}" alt="">
	                                    </div>
	                                    <div class="col-lg-6 col-md-12 md-mt-5">
	                                        <p class="mb-4">Create, save and organize all your optimize links by campaigns, this allows you to finally get rid of messy spreadsheets and efficiently track and analyze your overall clicks and conversions for the links you are sharing.</p>
	                                        <ul class="custom-li list-unstyled list-icon-2 d-inline-block">
	                                            <li>Track & Manage Links</li>
	                                            <li>Unique Clicks</li>
	                                            <li>Total Clicks</li>
	                                            <li>Conversion Rate</li>
	                                        </ul>
	                                    </div>
	                                </div>
	                            </div>
	                            <div role="tabpanel" class="tab-pane fade" id="tab1-2">
	                                <div class="row align-items-center">
	                                    <div class="col-lg-6 col-md-12">
	                                        <img class="img-fluid" src="{{ url('assets/frontend/images/feature-cta.png') }}" alt="">
	                                    </div>
	                                    <div class="col-lg-6 col-md-12 md-mt-5">
	                                        <p class="mb-0" style="font-weight: 600;">Popup Overlays</p>
	                                        <p class="mb-4">With {{ config('app.name') }} you are able to add your own custom call-to-action to any web page that you choose to share, which allows you to engage your audience through every link you share.</p>
	                                        
                                      		<p class="mb-0" style="font-weight: 600;">Email Opt-in Forms</p>
	                                        <p class="mb-4">Our service enables you to add your own email opt-in form to any website thus allowing you to generate lots more leads.</p>

                                      		<p class="mb-0" style="font-weight: 600;">Custom Scripts</p>
	                                        <p class="mb-4">We support all marketing tools that you may use such as Intercom, Google Analytics, Manychat, Convertfox and much more for an even better experience!</p>

	                                    </div>
	                                </div>
	                            </div>
	                            <div role="tabpanel" class="tab-pane fade" id="tab1-3">
	                                <div class="row align-items-center">
	                                    <div class="col-lg-6 col-md-12">
	                                        <img class="img-fluid" src="{{ url('assets/frontend/images/add-pixel.png') }}" alt="">
	                                    </div>
	                                    <div class="col-lg-6 col-md-12 md-mt-5">
	                                        <p class="mb-4">{{ config('app.name') }} supports retargeting pixels from all advertising platforms such as FaceBook, Twitter, Quora, Google, Linkedin and much more!</p>
	                                        <p>People who click specific links are an incomparably better target than random users since they have already shown their interest in the topic being shared.</p>
										</div>
	                                </div>
	                            </div>
	                            <div role="tabpanel" class="tab-pane fade" id="tab1-4">
	                                <div class="row align-items-center">
	                                    <div class="col-lg-6 col-md-12">
	                                        <img class="img-fluid" src="{{ url('assets/frontend/images/feature-analytics.png') }}" alt="">
	                                    </div>
	                                    <div class="col-lg-6 col-md-12 md-mt-5">
	                                        <p class="mb-4">Easily keep tabs on how your links are doing with detailed analytics on your link clicks, conversions, impressions and much more!</p>
											<p>For example, you can use this data to measure your effectiveness and return on investment for social media efforts.</p>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>

	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>


	    <section id="pricing" class="pos-r">
	        <div class="container">
	            <div class="row text-center">
	                <div class="col-lg-8 col-md-12 ml-auto mr-auto">
	                    <div class="section-title">
	                        <h2 class="title">Affordable Prices</h2>
	                        <p>Our pricing plans were crafted to fit your needs and budget</p>
	                    </div>
	                </div>
	            </div>


	            @if ( ! $plans->isEmpty() )

		            <div class="row align-items-center">
		               
		               	@foreach ($plans as $plan) 
		                <div class="col-lg-4 col-md-12">
		                    <div class="price-table">
		                        <div class="price-inside">{{ $plan->name }}</div>
		                        <div class="price-header">
		                            <div class="price-value">
		                                <h2><span>{{ config('settings.currency_symbol') }}</span>{{ $plan->price }}</h2>
		                                <span>per {{ $plan->interval }}</span>
		                            </div>
		                            <h3 class="price-title">{{ $plan->name }}</h3>
		                        </div>
		                        <a class="btn btn-theme btn-circle my-4" href="{{ config('app.url') }}/account/membership/checkout/{{ $plan->id }}">{{ $plan->trial_period_days ? "Start Free Trial" : "Subscribe Now" }}</a>
		                        <div class="price-list">
		                            <ul class="list-unstyled">
							        	@foreach ($plan->features as $feature)
							            <li>
							            	@if ( $feature->value != '0' && $feature->value != 'INCLUDE' )
					                        	<span class="font-weight-bold">{{ $feature->value }}</span> 
					                        @endif

					                        @if ( $feature->value == '0' )
					                        <del v-if="feature.value == 0">{{ $feature->name }}</del>
					                        @else
					                        <span v-else>{{ $feature->name }}</span>
					                        @endif
					                    </li>
							            @endforeach
		                            </ul>
		                        </div>
		                    </div>
		                </div>
		                @endforeach
		          
		            </div>

				@else
				<div class="w-50 m-auto alert alert-info">
			    	<h6 class="mb-0"><i class="fal fa-exclamation-circle"></i> No Available Plans</h6>
			    	<p class="mb-0">Unfortunately there are no available subscription plans at this time.</p>
			    </div>
			    @endif


	        </div>
	    </section>


	    <section id="testimonials" class="grey-bg pos-r o-hidden">
	        <!--<div class="wave-shape">
	            <img class="img-fluid" src="images/bg/03.png" alt="">
	        </div>-->
	        <div class="container">
	            <div class="row text-center">
	                <div class="col-lg-8 col-md-12 ml-auto mr-auto">
	                    <div class="section-title">
	                        <h2 class="title">What our customers are saying?</h2>
	                    </div>
	                </div>
	            </div>
	            <div class="row mt-5">
	                <div class="col-md-12">
	                    <div id="testimonial-1" class="testimonial-carousel carousel slide" data-ride="carousel" data-interval="2500">
	                        <div class="row align-items-center">
	                            <div class="col-lg-7 col-md-12">
	                                <div class="carousel-inner">
	                                    <div class="carousel-item active">
	                                        <div class="testimonial style-1">
	                                            <div class="testimonial-content">
	                                                <p> It has all the features that I was looking for plus it's easy to use. Also, the ability to add different pixels is a huge plus.</p>
	                                                <div class="testimonial-caption">
	                                                    <h5>Bella Ramos</h5>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class="carousel-item">
	                                        <div class="testimonial style-1">
	                                            <div class="testimonial-content">
	                                                <p>This tool is amazing. Simple, clean, highly effective.</p>
	                                                <div class="testimonial-caption">
	                                                    <h5>Harold Thompson</h5>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class="carousel-item">
	                                        <div class="testimonial style-1">
	                                            <div class="testimonial-content">
	                                                <div class="testimonial-quote"><i class="la la-quote-left"></i>
	                                                </div>
	                                                <p>A must in your arsenal if you are in the internet marketing industry</p>
	                                                <div class="testimonial-caption">
	                                                    <h5>Eddie Jenkins</h5>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-lg-5 col-md-12 md-mt-5">
	                                <div class="controls">
	                                    <div class="anti-01-holder">
	                                        <div class="anti-01"></div>
	                                    </div>
	                                    <div class="anti-02-holder">
	                                        <div class="anti-02"></div>
	                                    </div>
	                                    <div class="anti-03-holder">
	                                        <div class="anti-03"></div>
	                                    </div>
	                                    <ul class="nav">
	                                        <li data-target="#testimonial-1" data-slide-to="0" class="active">
	                                            <a href="#">
	                                                <img class="img-fluid" src="{{ url('assets/frontend/images/avatar-1.jpg') }}" alt="">
	                                            </a>
	                                        </li>
	                                        <li data-target="#testimonial-1" data-slide-to="1">
	                                            <a href="#">
	                                                <img class="img-fluid" src="{{ url('assets/frontend/images/avatar-2.jpg') }}" alt="">
	                                            </a>
	                                        </li>
	                                        <li data-target="#testimonial-1" data-slide-to="2">
	                                            <a href="#">
	                                                <img class="img-fluid" src="{{ url('assets/frontend/images/avatar-3.jpg') }}" alt="">
	                                            </a>
	                                        </li>
	                                    </ul>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>

	</div>


@include('frontend.footer') --}}