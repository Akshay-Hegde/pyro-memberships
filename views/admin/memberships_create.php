<section class="title">
	<!-- We'll use $this->method to switch between sample.create & sample.edit -->
	<h4><?php echo lang('memberships:'.$this->method); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
	<?php $dis = $this->method == 'edit'; ?>
		
		<div class="form_inputs">

		<ul>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="profile_id"><?php echo lang('memberships:profile'); ?> <span>*</span></label>
				<?php if (!$dis) : ?>
					<div class="input"><?php echo form_dropdown('profile_id', $profiles , isset($profile_id) ? $profile_id : 0, 'class="width-15" '.$dis); ?></div>
				<?php else : ?>
					<div class="input"><?php echo form_input('profile_id', set_value('profile_id', $profile->display_name) , 'class="width-15" disabled="disabled"'); ?></div>
				<?php endif ?>
			</li>

			<?php if ($dis) : ?>
			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="group_id"><?php echo lang('memberships:group_id'); ?> <span>*</span></label>
				<div class="input"><?php echo form_dropdown('group_id', $groups , isset($group_id) ? $group_id : 0, 'class="width-15" '.$dis); ?></div>
			</li>
			<?php endif ?>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="role_id"><?php echo lang('roles:role'); ?> <span>*</span></label>
				<?php if (!$dis) : ?>
					<div class="input"><?php echo form_dropdown('role_id', $roles , isset($role_id) ? $role_id : 0, 'class="width-15"'); ?></div>
				<?php else : ?>
					<div class="input"><?php echo form_input('role_id', set_value('role_id', $role->name) , 'class="width-15" disabled="disabled"'); ?></div>
				<?php endif ?>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="start_date"><?php echo lang('memberships:start_date'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('start_date', set_value('start_date', isset($start_date) ? $start_date : date('Y-m-d')), 'class="width-15" '.$dis); ?></div>
			</li>

			<li class="<?php echo alternator('', 'even'); ?>">
				<label for="end_date"><?php echo lang('memberships:end_date'); ?></label>
				<div class="input"><?php echo form_input('end_date', set_value('end_date', isset($end_date) ? $end_date : null), 'class="width-15"'); ?></div>
			</li>
		</ul>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		
	<?php echo form_close(); ?>

</section>
