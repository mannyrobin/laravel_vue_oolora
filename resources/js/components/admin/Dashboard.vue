<template>
<div class="bg-gray p-3 h-100">

	<div class="container-fluid">
		<div class="row mt-2">
			
			<div class="col-sm-6 col-xl-3">

				<div class="card card-body" style="background-color: #6CB2EB; color: #fff;">
					<div class="media">
						<div class="media-body">
							<h3 class="mb-0">{{ statistic.total_users }}</h3>
							<small class="text-uppercase font-size-xs">Registered Users</small>
						</div>

						<div class="ml-3 align-self-center">
							<i class="far fa-user-chart fa-3x opacity-75"></i>
						</div>
					</div>
				</div>

			</div>

			<div class="col-sm-6 col-xl-3">

				<div class="card card-body" style="background-color: #17C5CB; color: #fff;">
					<div class="media">
						<div class="media-body">
							<h3 class="mb-0">{{ statistic.active_subscribers }}</h3>
							<small class="text-uppercase font-size-xs">Active Subscribers</small>
						</div>

						<div class="ml-3 align-self-center">
							<i class="far fa-user-check fa-3x opacity-75"></i>
						</div>
					</div>
				</div>

			</div>

			<div class="col-sm-6 col-xl-3">

				<div class="card card-body" style="background-color: #51D88A; color: #fff;">
					<div class="media">
						<div class="media-body">
							<h3 class="mb-0"><sup>{{ $appSettings.currencySymbol }}</sup>{{ statistic.revenue }}</h3>
							<small class="text-uppercase font-size-xs">Total Revenue</small>
						</div>

						<div class="ml-3 align-self-center">
							<i class="far fa-file-invoice-dollar fa-3x opacity-75"></i>
						</div>
					</div>
				</div>

			</div>

			<div class="col-sm-6 col-xl-3">

				<div class="card card-body" style="background-color: #FAAD63; color: #fff;">
					<div class="media">
						<div class="media-body">
							<h3 class="mb-0"><sup>{{ $appSettings.currencySymbol }}</sup>{{ statistic.refunds }}</h3>
							<small class="text-uppercase font-size-xs">Total Refunds</small>
						</div>

						<div class="ml-3 align-self-center">
							<i class="far fa-envelope-open-dollar fa-3x opacity-75"></i>
						</div>
					</div>
				</div>

			</div>
		</div>


		<div class="row mt-3">
			<div class="col-md-7">

				<div class="card border" style="min-height: 330px;">
					<div class="card-body p-5 text-center">

						<template v-if="$appSettings.latestVersion > $appSettings.currentVersion">
							<i class="far fa-shield-alt fa-5x text-warning"></i>
							<h5 class="card-title mt-3 mb-0">Upgrade Available</h5>
							<p class="mb-3">A new version is available, please contact <span class="font-weight-semibold">HelpCommerce</span> support team to assist you with the upgrade.</p>
							<a href="http://helpcommerce.com" target="_blank" class="btn btn-light">Contact Support</a>
						</template>

						<template v-else>
							<i class="far fa-shield-check fa-5x text-success"></i>
							<h5 class="card-title mt-3 mb-0">No Updates Available</h5>
							<p class="mb-3">Your application is currently up to date, you will be notified as soon as new updates are available.</p>
						</template>

					</div>
				</div>

			</div>
			<div class="col-md-5">

				<div class="card border" style="min-height: 330px;">
			        <div class="card-header">
			            <h5 class="card-title">Application Details</h5>
			        </div>

			        <div class="card-body">

	        			<ul class="media-list mt-2 pl-0">
							<li class="media mb-3">
								<div class="mr-3 mt-1">
									<i class="fal fa-question fa-2x fa-fw text-gray-600"></i>
								</div>

								<div class="media-body">
									<span>Current Version</span>
									<div class="font-weight-semibold">{{ $appSettings.currentVersion }}</div>
								</div>
							</li>

							<li class="media mb-3">
								<div class="mr-3 mt-1">
									<i class="fal fa-info-circle fa-2x fa-fw text-gray-600"></i>
								</div>

								<div class="media-body">
									<span>Latest Version</span>
									<div class="font-weight-semibold">{{ $appSettings.latestVersion }}</div>
								</div>
							</li>

							<li class="media mb-3">
								<div class="mr-3 mt-1">
									<i class="fal fa-sync-alt fa-2x fa-fw text-gray-600"></i>
								</div>

								<div class="media-body">
									<span class="font-weight-semibold">Live Updates</span>
									<div class="text-gray-600">Exclusive upgrade to new features and improvements</div>
								</div>
							</li>

							<li class="media">
								<div class="mr-3 mt-1">
									<i class="fal fa-hands-helping fa-2x fa-fw text-gray-600"></i>
								</div>

								<div class="media-body">
									<span class="font-weight-semibold">Premium Support</span>
									<div class="text-gray-600">Direct help from our qualified support team</div>
								</div>
							</li>

						</ul>	

			        </div>
				</div>

			</div>	
		</div>


		<div class="card">
			<iframe src="https://feedback.userreport.com/d6837c50-c208-47f8-9022-d7a04d42b596/#ideas/popular" class="w-100 border" style="min-height: 500px;" frameborder="0"></iframe>
		</div>

	</div>

</div>
</template>


<script>
export default {
	
    data() {
        return {
        	statistic: {}
        };
    },

    created() {
    	
        axios.get('admin/dashboard')
        .then(response => {
            this.statistic = response.data;
        })
        .catch(error => {
        	this.$alert.error('Unable to load statistic data');
        });

    }
}
</script>