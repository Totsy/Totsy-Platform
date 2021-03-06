var upload1;

window.onload = function() {
	upload1 = new SWFUpload({
		// Backend Settings
		upload_url: "/uploads/upload/event",

		// File Upload Settings
		file_size_limit : "102400",	// 100MB
		file_types : "*.jpg;*.png;*.avi;*.gif",
		file_types_description : "All Files",
		file_upload_limit : "4",
		file_queue_limit : "0",

		// Event Handler Settings (all my handlers are in the Handler.js file)
		file_dialog_start_handler : fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,

		// Button Settings
		button_image_url : "/img/XPButtonUploadText_61x22.png",
		button_placeholder_id : "spanButtonPlaceholder1",
		button_width: 61,
		button_height: 22,
		
		// Flash Settings
		flash_url : "/img/flash/swfupload.swf",
		

		custom_settings : {
			progressTarget : "fsUploadProgress1",
			cancelButtonId : "btnCancel1",
			fileTarget : "fileInfo"
		}
	});
};

