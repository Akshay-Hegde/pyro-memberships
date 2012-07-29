<section class="title">
	<!-- We'll use $this->method to switch between sample.create & sample.edit -->
	<h4><?php echo lang('leagues:'.$this->method); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
		
		<div class="form_inputs">
	
		<ul>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="name"><?php echo lang('roles:name'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('name', isset($name) ? $name : '', 'class="width-15"'); ?></div>
			</li>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="slug"><?php echo lang('roles:slug'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('slug', isset($slug) ? $slug : '', 'class="width-15"'); ?></div>
			</li>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="model"><?php echo lang('roles:model'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('model', isset($model) ? $model : '', 'class="width-15"'); ?></div>
			</li>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="key_field"><?php echo lang('roles:key_field'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('key_field', isset($key_field) ? $key_field : '', 'class="width-15"'); ?></div>
			</li>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="value_field"><?php echo lang('roles:value_field'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('value_field', isset($value_field) ? $value_field : '', 'class="width-15"'); ?></div>
			</li>
		</ul>
		
		</div>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		
	<?php echo form_close(); ?>

</section>