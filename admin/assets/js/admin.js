(function ( $ ) {
	"use strict";

	function listObjs() {

		bucket.listObjects({Prefix: bucket_prefix + "/"}, function(error,data) {
			$('#file_list tbody').html('');
			if (error === null) {
				jQuery.each(data.Contents, function(index, obj) {
					if (obj.Key !== bucket_prefix + "/") {
						var filename = obj.Key.replace(bucket_prefix + "/", '');
						filenames.push(filename);
						var link = cloudFrontURL + "/" + obj.Key;
						row = $('<tr></tr>');
						row.append('<td>'+filename+'</td>');
						row.append('<td><a href="'+link+'" target="_blank">'+link+'</td>');
						$('#file_list tbody').append(row);
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

	});

}(jQuery));