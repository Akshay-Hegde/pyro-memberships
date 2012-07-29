<section class="title">
	<h4><?php echo lang('roles:roles'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/roles/delete');?>
	
	<?php if (!empty($roles)): ?>
	
		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?php echo lang('roles:id'); ?></th>
					<th><?php echo lang('roles:name'); ?></th>
					<th><?php echo lang('roles:model'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?
				    // Loop through each team here.
				    foreach( $roles as $role ):
			    ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $role->id); ?></td>
					<td><?php echo $role->id; ?></td>
					<td><?php echo $role->name; ?></td>
					<td><?php echo $role->model; ?></td>
					<td class="actions">
						<?php echo
						anchor('memberships/roles/'.$role->id, lang('global:view'), 'class="button" target="_blank"').' '.
						anchor('admin/memberships/roles/edit/'.$role->id, lang('global:edit'), 'class="button"').' '.
						anchor('admin/memberships/roles/delete/'.$role->id, 	lang('global:delete'), array('class'=>'button')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('leagues:no_items'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>
