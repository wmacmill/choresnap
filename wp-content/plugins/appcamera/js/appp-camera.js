var isGallery = false;

appcamera.init = function(){
    var $ = jQuery;
    appcamera.el = {
        $body  : $('body'),
        $title : $('#appp_cam_post_title'),
        $file  : $('#appp_cam_file'),
        $status : $('#cam_status')
    };
    
    appcamera.el.$body.on( 'change', '#appp_cam_file', function( event ){
        event.preventDefault();
        var $self = $(this);
        var title = appcamera.el.$title.val();

        if ( title && title.trim() )
            return;

        var val   = $self.val();
        var parts = val ? val.split('.') : false;

        if ( parts[0] ) {
            parts = parts[0].split('\\');
            val = parts[ parts.length - 1 ];
            appcamera.el.$title.val( val );
        }
    });
};

appcamera.capturePhoto = function() {
	
	isGallery = false;
    // Retrieve image file location from specified source
    window.navigator.camera.getPicture(
        appcamera.uploadPhoto,
        function(message) {
            /*alert('No photo was uploaded.');*/
            if ( typeof apppresser.log == 'function' ) {
                apppresser.log( 'No photo was taken from the camera.', 'appp-camera.js, line 35' );
            }
        },
        {
            quality         : 30,
            destinationType : window.navigator.camera.DestinationType.FILE_URI,
            correctOrientation: true,
             targetWidth: 1204,
			 targetHeight: 1204
        }
    );
};

appcamera.photoLibrary = function() {

	isGallery = true;
    // Retrieve image file location from specified source
    window.navigator.camera.getPicture(
        appcamera.uploadPhoto,
        function(message) {
            /*alert('No photo was uploaded.');*/
            if ( typeof apppresser.log == 'function' ) {
                apppresser.log( 'No photo was added from the library.', 'appp-camera.js, line 53' );
            }
        },
        {
            quality         : 30,
            destinationType : window.navigator.camera.DestinationType.FILE_URI,
            sourceType      : window.navigator.camera.PictureSourceType.PHOTOLIBRARY,
            correctOrientation: true,
            targetWidth: 1204,
			targetHeight: 1204
        }
    );
};

appcamera.statusDom = function() {
    appcamera.statusDomEl = appcamera.statusDomEl ? appcamera.statusDomEl : document.getElementById('cam-status');
    return appcamera.statusDomEl;
};

appcamera.uploadPhoto = function(imageURI) {

	var image = imageURI.substr( imageURI.lastIndexOf('/') + 1 );
	
	var name = image.split("?")[0];
	var number = image.split("?")[1];
		
	if( 'Android' === device.platform && isGallery ) {
		image = number + '.jpg';
	}

	console.log(image);

    var options      = new FileUploadOptions();
    options.fileKey  = 'appp_cam_file';
    options.fileName = imageURI ? image : '';
    options.mimeType = 'image/jpeg';

    console.log(options);

    var params = {};
    var form_fields = [];
    var form_values = [];
    var iterator;
    var form_elements = document.appp_camera_form.elements;

    for( iterator = 0; iterator < form_elements.length; iterator++ ){
        form_fields[iterator] = form_elements[iterator].name;
        form_values[iterator] = form_elements[iterator].value;
    }

    params.form_fields = JSON.stringify(form_fields);
    params.form_values = JSON.stringify(form_values);

    document.getElementById('appp_cam_post_title').value = '';
    options.params = params;

    var ft = new FileTransfer();

    ft.upload( imageURI, encodeURI(document.URL), appcamera.win, appcamera.fail, options);
    
    ft.onprogress = function(progressEvent) {
        if ( progressEvent.lengthComputable ) {
            //appcamera.statusProgress().innerHTML = '<progress id="progress" value="1" max="100"></progress>';
            jQuery('#cam-progress').css('visibility', 'visible');
            var perc = Math.floor(progressEvent.loaded / progressEvent.total * 100);
            document.getElementById('progress').value = perc;
        } else {
            if ( appcamera.statusDom().innerHTML == '' ) {
                appcamera.statusDom().innerHTML = appcamera.msg.loading;
            } else {
                appcamera.statusDom().innerHTML += '.';
            }
        }
    };

};

appcamera.win = function(r) {

    //console.log('Code = ' + r.responseCode);
    //console.log('Response = ' + r.response);
    //console.log('Sent = ' + r.bytesSent);

    var msg = appcamera.msg.moderation;
    var action = document.getElementById('appp_action').value;

    if ( ! appcamera.moderation_on ) {

        // var type = jQuery('#appp_post_type_label').val();
        // type = type ? type : appcamera.msg.default_type;

        msg = appcamera.msg.success;
    }

    jQuery('#cam-status').html('<p>'+ msg +'</p>');
    jQuery('#cam-progress').css('visibility', 'hidden');

};

appcamera.fail = function(error) {
    // alert('An error has occurred: Code = ' + error.code);
    console.log('upload error source ' + error.source);
    console.log('upload error target ' + error.target);
    jQuery('#cam-status').html('<p>'+ appcamera.msg.error +'= '+ error.code +'</p>');
    jQuery('#cam-progress').css('visibility', 'hidden');
};

appcamera.attachPhoto = function() {
    // Retrieve image file location from specified source
    window.navigator.camera.getPicture(
        appcamera.uploadAttachPhoto,
        function(message) {
            /*alert('No photo was uploaded.');*/
            if ( typeof apppresser.log == 'function' ) {
                apppresser.log( 'No photo was added from the library.', 'appp-camera.js, line 53' );
            }
        },
        {
            quality         : 30,
            destinationType : window.navigator.camera.DestinationType.FILE_URI,
            correctOrientation: true,
            targetWidth: 1204,
			targetHeight: 1204
        }
    );
};

appcamera.attachLibrary = function() {
    // Retrieve image file location from specified source
    window.navigator.camera.getPicture(
        appcamera.uploadAttachPhoto,
        function(message) {
            /*alert('No photo was uploaded.');*/
            if ( typeof apppresser.log == 'function' ) {
                apppresser.log( 'No photo was added from the library.', 'appp-camera.js, line 53' );
            }
        },
        {
            quality         : 30,
            destinationType : window.navigator.camera.DestinationType.FILE_URI,
            sourceType      : window.navigator.camera.PictureSourceType.PHOTOLIBRARY,
            correctOrientation: true,
            targetWidth: 1204,
			targetHeight: 1204
        }
    );
};

appcamera.uploadAttachPhoto = function(imageURI) {

	var image = imageURI.substr( imageURI.lastIndexOf('/') + 1 );
	
	var name = image.split("?")[0];
	var number = image.split("?")[1];
	var time = Math.floor( Date.now() / 1000 );
	
	imagenew = time + '-' + name;
		
	if( 'Android' === device.platform ) {
		imagenew = number + time + '.jpg';
	}

	console.log(imagenew);

    var options      = new FileUploadOptions();
    options.fileKey  = 'appp_cam_file';
    options.fileName = imageURI ? imagenew : '';
    options.mimeType = 'image/jpeg';

    var params = {};
    params.action = 'upload_image';
    params.nonce = document.getElementById('apppcamera-upload-image').value;

    options.params = params;

    var ft = new FileTransfer();

    ft.upload( imageURI, ajaxurl, appcamera.attachWin, appcamera.fail, options);

    ft.onprogress = function(progressEvent) {
        if ( progressEvent.lengthComputable ) {
            //appcamera.statusDom().innerHTML = '<progress id="progress" value="1" max="100"></progress>';
            jQuery('#cam-progress').css('visibility', 'visible');
            var perc = Math.floor(progressEvent.loaded / progressEvent.total * 100);
            document.getElementById('progress').value = perc;
        } else {
            if ( appcamera.statusDom().innerHTML == '') {
                appcamera.statusDom().innerHTML = appcamera.msg.loading;
            } else {
                appcamera.statusDom().innerHTML += '.';
            }
        }
    };

};

appcamera.attachWin = function(r) {

    console.log('Code = ' + r.responseCode);
    console.log('Response = ' + r.response);
    console.log('Sent = ' + r.bytesSent);

    var action = document.getElementById('appp_action').value;

    if ( action == 'appbuddy' ) {
        msg = 'Image attached';
    }

    jQuery('#cam-status').html('<p>'+ msg +'</p>');

    document.getElementById('attach-image').value = JSON.parse(r.response);
    
    jQuery('#attach-image-sheet').removeClass('active').addClass('hide');
    jQuery('#image-status').html('<img src="'+ JSON.parse(r.response) +'">');
    jQuery('#cam-progress').css('visibility', 'hidden');
    jQuery('#cam-status').html('');

};


jQuery(document).ready( appcamera.init ).bind( 'load_ajax_content_done', appcamera.init );