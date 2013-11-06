(function ( $ ) {
	"use strict";

	function listObjs() {

		bucket.listObjects({Prefix: bucket_prefix }, function(error,data) {
			$('#the-list').html('');
			if (error === null) {
				jQuery.each(data.Contents, function(index, obj) {
					if (obj.Key !== bucket_prefix ) {
						var filename = obj.Key.replace(bucket_prefix, '');
						filenames.push(filename);
						var link = cloudFrontURL + "/" + obj.Key;
						var row = $('<tr></tr>');
						if (index%2 === 0) {
							row.addClass('alternate');
						}
						row.append('<td>'+filename+'</td>');
						row.append('<td><a href="'+link+'" target="_blank">'+link+'</td>');
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
						Key: bucket_prefix + file.name,
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

	});

}(jQuery));