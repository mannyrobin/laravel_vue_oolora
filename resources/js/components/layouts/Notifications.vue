<template>
<div class="panel-wrap has-header has-footer">

    <div class="panel-header px-3 bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 cursor-pointer" @click="closePanel"><i class="far fa-long-arrow-left"></i> Notifications</h6>
        <button-spinner :loading="btnLoadingMarkAll" :disabled="!canMarkAll" class="btn-sm btn-link text-body" @click.prevent="markAllAsRead">Mark all as read</button-spinner>
    </div>

    <div class="panel-body p-2 bg-gray">

        <infinite-scroll class="h-100" :api-data.sync="notifications" api-url="notifications" :query-params="queryParams" :initial-data-loading="showPlaceholder">

            <template v-if="!showPlaceholder">

                <div v-if="notifications.meta.total === 0" class="d-flex align-items-center justify-content-center text-muted h-100">
                    <div>
                        <i class="fal fa-bells fa-4x"></i>
                        <p class="font-weight-bold mt-1">All Caught Up!</p>
                    </div>
                </div> 

            <div class="card border mb-2 cursor-pointer"
                v-if="notifications.meta.total >= 0" 
                v-for="notify in notifications.data" 
                @click="markAsRead(notify)"
                :key="notify.id">

                <div class="card-body p-2">
                    
                    <div>
                        <span class="badge p-2 rounded-circle mr-1" :style="{ 'background-color': notify.icon_color, color: '#fff'}"><i :class="'font-size-lg fal fa-' + notify.icon"></i></span>
                        <span class="font-weight-semibold">{{ notify.title }}</span>
                    </div>

                    <p class="mb-0 mt-2 text-gray-600">{{ notify.message }}</p>

                    <div class="mt-1 d-flex justify-content-between">
                        <span v-if="notify.read" class="text-success"><i class="fal fa-check-double"></i> Read</span>
                        <div class="ml-auto"><small class="text-muted">{{ notify.date }}</small></div>
                    </div>

                </div>

            </div>

            </template>

           
            <div v-if="showPlaceholder"  v-for="n in 6" class="card mb-2">
                <div class="card-body p-2">
                    <placeholder heading></placeholder>
                </div>
            </div>
            
        </infinite-scroll>

    </div>

    <div class="panel-footer text-center border-top">
        <button v-if="queryParams.unread" type="button" class="btn btn-link text-uppercase text-body" @click="readOrUnread">See Read Notifications</button>
        <button v-if="!queryParams.unread" type="button" class="btn btn-link text-uppercase text-body" @click="readOrUnread">See Unread Notifications</button>
    </div>

</div>
</template>


<script>
export default {
    name: 'notifications',

    data() {
        return {
        	showPlaceholder: false,
        	btnLoadingMarkAll: false,
        	canMarkAll: true,
        	notifications: {},
	        queryParams: {
	        	page: 1,
	        	per_page: 15,
	        	unread: true
			},
        };
    },

    created () {
    	this.getNotifications();  	
    },

    watch: {
		'notifications.meta': function (newVal, oldVal) {

			// Update unread total count
			if ( this.queryParams.unread )
				window.appUser.unreadNotifications = newVal.total;


			// Disable can mark all action
			if ( newVal.total === 0 || ! this.queryParams.unread )
            	this.canMarkAll = false;
            else
            	this.canMarkAll = true;
        }
	},

    methods: {
    	getNotifications () {
        	this.showPlaceholder = true;
        	this.queryParams.page = 1;

            axios.get('notifications', {params: this.queryParams})
            .then(response => {
            	
            	this.showPlaceholder = false;
            	this.notifications = response.data;

            })
            .catch(error => {
            	this.$alert.error(error.response.data.message);
            }); 
        },

        // Change the type of notifications to load read/unread
        readOrUnread () {
        	this.queryParams.unread = ! this.queryParams.unread;
        	
        	this.getNotifications();
        },

        markAllAsRead () {
        	this.btnLoadingMarkAll = true;

            axios.delete('notifications/mark-all')
            .then(response => {
            	this.btnLoadingMarkAll = false;

	            // Reload notifications
	            this.getNotifications();
            })
            .catch(error => {
            	this.btnLoadingMarkAll = false;
            });
		},

        markAsRead (notification) {
        	// Only mark unread if the notification isn't already set
        	if ( notification.read === false) {
            	axios.delete('notifications/'+ notification.id);

            	// Update total notifications count
            	window.appUser.unreadNotifications = window.appUser.unreadNotifications - 1;
            }

            // close the panel
            this.closePanel();

            // Redirect to action URL
            if (notification.action)
            	window.location.href = notification.action;
		},

        closePanel () {
            this.$emit("closePanel", {});
        }
    }
}
</script>