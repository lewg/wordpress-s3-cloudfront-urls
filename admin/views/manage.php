<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   S3_CloudFront_URLs
 * @author    Lew Goettner <lew@goettner.net>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Lew Goettner
 */
?>
<div class="wrap">

	<?php screen_icon('upload'); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?>
		<!-- <a href="post-new.php?post_type=page" class="add-new-h2">Add New</a> -->
	</h2>

	<form id="upload_form">
	    <ul>
	        <li><label for="upload">New File<span> *</span>: </label>
		        <input type="file" id="upload">
		        <input class="button-primary" type="submit" name="Upload" value="Upload" />
			</li>    
	    </ul>
	</form>

	<!-- TODO: Provide markup for your options page here. -->
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
		<tr>
			<th scope="col" width="33%" class="manage-column column-title">
				<span>Filename</span>
			</th>
			<th scope="col" class="manage-column column-title">				
				<span>CloudFront URL</span>
			</th>
			<th scope="col" width="110" class="manage-column column-title">
				<span>Downloads</span>
			</th>
		</thead>

		<tfoot>
		<tr>
			<th scope="col" class="manage-column column-title">
				<span>Filename</span>
			</th>
			<th scope="col" class="manage-column column-title">				
				<span>CloudFront URL</span>
			</th>
			<th scope="col" class="manage-column column-title">
				<span>Downloads</span>
			</th>
		</tr>
		</tfoot>

		<tbody id="the-list">

		</tbody>
	</table>

</div>
