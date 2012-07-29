<section class="title">
	<h4><?php echo lang('memberships:memberships'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/memberships/delete');?>
	
	<?php if (!empty($memberships)): ?>

		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?php echo lang('memberships:id'); ?></th>
                    <th><?php echo lang('profile_display_name'); ?></th>
					<th><?php echo lang('roles:name'); ?></th>
                    <th><?php echo lang('memberships:start_date'); ?></th>
                    <th><?php echo lang('memberships:end_date'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($memberships as $mem): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $mem->id); ?></td>
                    <td><?php echo $mem->id; ?></td>
                    <td class="label flat <?php echo $mem->status_color; ?>"><?php echo $mem->profile->display_name; ?></td>
                    <td><?php echo $mem->role->name; ?></td>
					<td><?php echo $mem->start_date; ?></td>
					<td>
						<?php echo !isset($mem->end_date) ? '<i class="icon-remove"></i>' : ''; ?>
						<?php echo $mem->end_date; ?>
					</td>
					<td class="actions">
						<?php echo
						anchor('memberships/'.$mem->id, 'Report', 'class="button" target="_blank"').' '.
						anchor('admin/memberships/edit/'.$mem->id, lang('global:edit'), 'class="button"').' '.
						anchor('admin/memberships/delete/'.$mem->id, 	lang('global:delete'), array('class'=>'button')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('teams:no_items'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>
