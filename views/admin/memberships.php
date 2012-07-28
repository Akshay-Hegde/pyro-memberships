<section class="title">
	<h4><?php echo lang('teams:teams'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/teams/delete');?>
	
	<?php if (!empty($teams)): ?>

		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><?php echo lang('teams:id'); ?></th>
					<th><?php echo lang('teams:name'); ?></th>
                    <th><?php echo lang('teams:slug'); ?></th>
                    <?php if (isset($extra_enabled['memberships'])) : ?>
                    <th><?php echo lang('teams:head-coach'); ?></th>
                    <?php endif; ?>
                    <th><?php echo lang('leagues:league'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($teams as $team ): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $team->id); ?></td>
                    <td><?php echo $team->id; ?></td>
					<td><?php echo $team->name; ?></td>
					<td><?php echo $team->slug; ?></td>
					<?php if (isset($extra_enabled['memberships'])) : ?>
					<td><?php echo $team->head_coach->display_name; ?></td>
					<?php endif; ?>
					<td><?php echo $team->league->name; ?></td>
					<td class="actions">
						<?php echo
						anchor('teams/'.$team->id, lang('global:view'), 'class="button" target="_blank"').' '.
						anchor('admin/teams/edit/'.$team->id, lang('global:edit'), 'class="button"').' '.
						anchor('admin/teams/delete/'.$team->id, 	lang('global:delete'), array('class'=>'button')); ?>
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
