(function ( $ ) {
	"use strict";

	AWS.config.update({
		accessKeyId: s3_cloudfront_settings.accessKeyId,
		secretAccessKey: s3_cloudfront_settings.secretAccessKey
	});
	var bucket = new AWS.S3({
		params: {Bucket: s3_cloudfront_settings.s3BucketName},
	});
	var filenames = [];

	function listObjs() {

		bucket.listObjects({Prefix: s3_cloudfront_settings.bucket_prefix }, function(error,data) {
			$('#the-list').html('');
			if (error === null) {
				jQuery.each(data.Contents, function(index, obj) {
					if (obj.Key !== s3_cloudfront_settings.bucket_prefix ) {
						var filename = obj.Key.replace(s3_cloudfront_settings.bucket_prefix, '');
						filenames.push(filename);
						var link = s3_cloudfront_settings.cloudFrontURL + "/" + obj.Key;
						var row = $('<tr></tr>');
						if (index%2 === 0) {
							row.addClass('alternate');
						}
						row.append('<td>'+filename+'</td>');
						row.append('<td><a href="'+link+'" target="_blank">'+link+'</td>');
						if (s3_cloudfront_settings.qs_setup) {
							row.append( $('<td></td>').append($("<button class='get_hits'>Get Downloads</button>").attr('value',obj.Key)) );
						}
						$('#the-list').append(row);
					}
				});
			} else {
				console.log(error);
			}
		});

	}



	$(function () {

		// Place your administration-specific JavaScript here
		listObjs();

		$('#upload_form').submit(function(){
			var file = document.getElementById('upload').files[0];
			if (file) {
				// Check name
				if (filenames.indexOf(file.name) >= 0) {
					alert("Filename already exists. Try renaming the file and uploading.");
					return(false);
				}
				$(this).find(":submit").prop('disabled','disabled').html('<em>Uploading</em>');
				bucket.putObject(
					{
						Key: s3_cloudfront_settings.bucket_prefix + file.name,
						ContentType: file.type,
						Body: file,
						ACL: "public-read"
					},
					function (err, data) {
						if (data !== null) {
							$('#upload_form').each(function(){
								this.reset();
							});
							listObjs();
						} else {
							alert("Upload failed!");
						}
						$('#upload_form').find(":submit").removeProp('disabled').html('Upload');
					}
				);
			}
			return(false);
		});

		$('body').delegate("button.get_hits", "click", function(){
			var filename = $(this).attr('value');
			$(this).prop('disabled','disabled');
			var hits = jQuery.post(
				s3_cloudfront_settings.ajax_url,
				{
					action: 'qloudstat_numbers',
					filename: filename,
					// send the nonce along with the request
					qs_nonce: s3_cloudfront_settings.qs_nonce,
				},
				function(response){
					console.log(response);
					if (response['hits'] !== undefined) {
						$('button[value="'+response['filename']+'"]').replaceWith('<p>'+response['hits']+'</p>');
					} else {
						alert('Error getting hits.');
					}
				}
			);
		});

	});

}(jQuery));