<template>
<div class="has-navbar h-100">

    <navbar></navbar>

    <div id="page-wrapper">

        <main id="page-content">

			<div class="container h-100 d-flex align-items-center">
			    <div class="col">
			        
			        <div class="row justify-content-center">
			            <div class="col-md-6 col-lg-5 col-xl-4">

			                <div class="text-center">
			                    <h2 class="mb-0 display-3">404</h2>
			                    <p class="lead mb-4">Sorry, the page you are looking for could not be found.</p>

			                    <a class="btn btn-light text-uppercase" :href="$appSettings.appUrl + '/dashboard'">Go to Dashboard</a>
			                </div>

			            </div>
			        </div>

			    </div>
			</div>

        </main> 

    </div>

</div>
</template>

<script>
import Navbar from '../layouts/Navbar.vue';

export default {

    components: {
        Navbar,
    },
}
</script>