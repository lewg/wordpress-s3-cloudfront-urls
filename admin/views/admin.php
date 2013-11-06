<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Lew Goettner <lew@goettner.net>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Lew Goettner
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="POST">
		<?php settings_fields($this->plugin_slug); ?>
		<?php do_settings_sections($this->plugin_slug); ?>
		<?php submit_button(); ?>
	</form>

	<h3>Useful Information</h3>
	<p>First, it's very important that these access keys are tied to an IAM account with limited access. Anyone you provide editor level access and above will be able to get the keys from the backend source, so you wouldn't want access to be any more then just the intended S3 bucket. To that end, here is a sample access policy:</p>
	<pre>
{
  "Version": "<?php echo date('Y-m-d'); ?>",
  "Statement": [
    {
      "Action": [
        "s3:ListBucket"
      ],
      "Sid": "Stmt<?php echo rand(1000000000000,2000000000000) ?>",
      "Resource": [
        "arn:aws:s3:::<?php echo get_option( $this->plugin_slug . '-bucket', 'YOUR_BUCKET_NAME' ); ?>"
      ],
      "Effect": "Allow"
    },
    {
      "Action": [
        "s3:GetObject",
        "s3:PutObject",
        "s3:PutObjectAcl"
      ],
      "Sid": "Stmt<?php echo rand(1000000000000,2000000000000) ?>",
      "Resource": [
        "arn:aws:s3:::<?php echo get_option( $this->plugin_slug . '-bucket', 'YOUR_BUCKET_NAME' ); ?>/<?php echo get_option($this->plugin_slug . '-bucket-prefix', ''); ?>*"
      ],
      "Effect": "Allow"
    }
  ]
}		
	</pre>
	<p>Additionally, you'll need to adjust the CORS settings on your bucket to allow the types of requests necessary. Here is a sample CORS xml file:</p>

</div>
