<div id="form">
	<div id="form_box">
			<form method="post">
			<input type="hidden" name="merchant_id" value="<?php echo $merchant_id ?>" />
			<!--
			Logistic:<br />
			<strong><?php echo $merchant_name;?></strong><br /><br />
			-->

			<?php print form_fieldset('Application Info'); ?>

			Scheme Name:<br />
			<input type="text" name="application_name" size="50" class="form" value="<?php echo set_value('application_name'); ?>" /><br /><?php echo form_error('application_name'); ?><br />

			Scheme Description:<br />
			<textarea name="application_description" cols="60" rows="10"><?php echo set_value('application_description'); ?></textarea><br />

			<?php print form_fieldset_close(); ?>

			<input type="submit" value="Add" name="add" />
			<?php
				if(isset($back_url)){
					print $back_url;
				}
			?>
			</form>
	</div>
</div>