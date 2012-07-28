<section class="title">
	<h4><?php echo lang('leagues:leagues'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/leagues/delete');?>
	
	<?php if (!empty($leagues)): ?>
	
		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?php echo lang('leagues:id'); ?></th>
					<th><?php echo lang('leagues:name'); ?></th>
					<th>Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?
				    // Loop through each team here.
				    foreach( $leagues as $league ):
			    ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $league->id); ?></td>
					<td><?php echo $league->id; ?></td>
					<td><?php echo $league->name; ?></td>
					<td class="actions">
						<?php echo
						anchor('leagues/'.$league->id, lang('global:view'), 'class="button" target="_blank"').' '.
						anchor('admin/leagues/edit/'.$league->id, lang('global:edit'), 'class="button"').' '.
						anchor('admin/leagues/delete/'.$league->id, 	lang('global:delete'), array('class'=>'button')); ?>
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
