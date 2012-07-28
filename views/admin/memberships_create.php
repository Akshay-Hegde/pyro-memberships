<section class="title">
	<!-- We'll use $this->method to switch between sample.create & sample.edit -->
	<h4><?php echo lang('teams:'.$this->method); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
		
		<div class="form_inputs">

		<ul>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="name"><?php echo lang('teams:name'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('name', set_value('name', $name), 'class="width-15"'); ?></div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="slug"><?php echo lang('teams:slug'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('slug', set_value('slug', $slug), 'class="width-15"'); ?></div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="league_id"><?php echo lang('leagues:league'); ?> <span>*</span></label>
				<div class="input"><?php echo form_dropdown('league_id', $league_list , isset($league_id) ? $league_id : 0, 'class="width-15"'); ?></div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="team_image"><?php echo lang('teams:photo'); ?></label>
				<div class="input">Photo feature WIP</div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="description"><?php echo lang('teams:description'); ?> </label>
				<div class="input"><?php echo form_textarea('description', set_value('description', isset($description) ? $description : ''), 'class="width-15 wysiwyg-simple"'); ?></div>
			</li>
		</ul>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		
	<?php echo form_close(); ?>

</section>
