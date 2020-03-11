<template>
<div class="container h-100 d-flex align-items-center">
    <div class="col">
        
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">

                <div class="empty-state text-center">
                	<i class="fal fa-4x fa-exclamation-circle mb-2 text-gray-600"></i>
                	<h5 class="font-family-base mb-1 font-weight-semibold">Feature Not Supported</h5>
					<p class="lead mb-3">Your current plan doesn't support this feature, please upgrade to gain access.</p>

                    <router-link to="/billing/plans?action=change" class="btn btn-primary">Upgrade Plan</router-link>
                </div>

            </div>
        </div>

    </div>
</div>
</template>