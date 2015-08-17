window.appp_push = (function(window, document, undefined){
    'use strict';

    var push = {}

    /**
     * Application Constructor
     * @since  1.0.0
     */
    push.initialize = function() {
        push.bindEvents();
    }

    /**
     * Bind Event Listeners
     *
     * Bind any events that are required on startup. Common events are:
     * 'load', 'deviceready', 'offline', and 'online'.
     *
     * @since  1.0.0
     * @return {[type]} [description]
     */
    push.bindEvents = function() {
        document.addEventListener( 'deviceready', push.onDeviceReady, false );
    }

    /**
     * deviceready Event Handler
     * @since  1.0.0
     */
    push.onDeviceReady = function() {
        // check and add device id
        var device_id = PushWoosh.getHWId();
        push.receivedEvent('deviceready');
        
        var url = apppCore.ajaxurl;
        
        if ( ! url )
          return;

        // alert(url);
        
        jQuery.ajax({
          type: 'POST',
          dataType: "json",
          url: url,
          data: {
            'action': 'appp_push_device_id',
            'device_id': device_id,
          },
          success: function( response ) {
            // alert(response.data);
          }
        });
    }

    push.tokenHandler = function(result) {
        // Your iOS push server needs to know the token before it can push to this device
        // here is where you might want to send it the token for later use.
        if(!PushWoosh) {
            push.log('No PushWoosh object present');
            return;
        }
        // PushWoosh Application ID, looks like this 40F80-E43E5
        PushWoosh.appCode = apppPushVars.app_code;

        PushWoosh.register(result, function(data) {
            push.log("PushWoosh register success: " + JSON.stringify(data));
        }, function(errorregistration) {
            push.log("Couldn't register with PushWoosh" +  errorregistration);
        });
        push.log("Token Handler " + result);
    }

    push.errorHandler = function(error) {
        push.log( "Error Handler  " + error );
        // alert(error);
    }

    /**
     * result contains any message sent from the plugin call
     * @since  1.0.0
     * @param  {mixed} result Success result
     */
    push.successHandler = function(result) {
        push.log( 'Success! Result = ' + result );
    }

    /**
     * Update DOM on a Received Event
     * @since  1.0.0
     */
    push.receivedEvent = function(id) {

        var pushNotification = window.plugins.pushNotification;

        // Unregister before registering again
        pushNotification.unregister(push.successHandler, push.errorHandler);

        // TODO: Enter your own GCM Sender ID in the register call for Android
        if (device.platform == 'android' || device.platform == 'Android') {
            push.log( 'GCM ID: ' + apppPushVars.gcm_id );
            pushNotification.register(push.successHandler, push.errorHandler,{"senderID":apppPushVars.gcm_id,"ecb":"appp_push.onNotificationGCM"});
        }
        else {
            pushNotification.register(push.tokenHandler,push.errorHandler,{"badge":"true","sound":"true","alert":"true","ecb":"appp_push.onNotificationAPN"});
        }
    }

    /**
     * iOS
     * @since  1.0.0
     */
    push.onNotificationAPN = function(event) {
        // push.log('onNotificationAPN');
        var pushNotification = window.plugins.pushNotification;
        
        if( apppPushVars.notifications_title ) {
          // otherwise use setting
          var pushTitle = apppPushVars.notifications_title; 
        } else {
          var pushTitle = 'Notification';
        }

        if ( event.alert ) {

          //var string1 = JSON.stringify( event );
          // alert( event.u );

            navigator.notification.alert(
                event.alert,  // message
                null,         // callback
                pushTitle,            // title
                'Done'                // buttonName
            );

        }
        if (event.badge) {
            // alert("Set badge on  " + pushNotification);
            pushNotification.setApplicationIconBadgeNumber(push.successHandler, event.badge);
        }
        if (event.sound) {
            var snd = new Media(event.sound);
            snd.play();
        }
    }

    /**
     * Android
     * @since  1.0.0
     */
    push.onNotificationGCM = function(e) {

        if( apppPushVars.notifications_title ) {
          var pushTitle = apppPushVars.notifications_title; 
        } else {
          var pushTitle = 'Notification';
        }

        switch( e.event )
        {
            case 'registered':
                if ( e.regid.length > 0 )
                {

                /* http://www.pushwoosh.com/programming-push-notification/android/android-additional-platforms/phonegap-build-generic-plugin-integration/ */
                PushWoosh.appCode = apppPushVars.app_code;
                PushWoosh.register(e.regid, function(data) {
                    push.log("PushWoosh register success: " + JSON.stringify(data));
                    }, function(errorregistration) {
                    push.log("Couldn't register with PushWoosh" +  errorregistration);
                });
                    // Your GCM push server needs to know the regID before it can push to this device
                    // here is where you might want to send it the regID for later use.
                    push.log('Registration id = '+e.regid);
                }

            break;

            case 'message':
              // this is the actual push notification. its format depends on the data model
              // of the intermediary push server which must also be reflected in GCMIntentService.java

              // Title for android header still not working when app is closed. Title not being received, may have to do with push plugin

              navigator.notification.alert(
                  e.message,  // message
                  null,         // callback
                  pushTitle,            // title
                  'Done'                // buttonName
              );
              // Not sure what msgcnt is
              push.log('msgcnt = '+e.msgcnt);
            break;

            case 'error':
              push.log('GCM error = '+e.msg);
            break;

            default:
              push.log('An unknown GCM event has occurred');
              break;
        }
    }

    /* Callback for push notifications
     * Use for url redirects
     */

    // push.notificationCallback = function(event) {
    //   if(event.l) {
    //     var url = l;
    //     window.apppresser.loadAjaxContent( url, false, null );
    //   }
    // }

    push.log = function() {
        if ( apppCore.log ) {
            apppCore.log( arguments );
        }
    }

    push.initialize();

    return push;

})(window, document);