<h2><?php echo $team->name . ' (' . $team->slug .')'; ?></h2>
<div id="base-details" class="one_third">
	<ul>
	<?php if (isset($extra_enabled['memberships'])) : ?>
		<li>
		<span class="label flat"><?php echo lang('teams:head-coach').': '?></span>
		{{ url:anchor segments="coaches/<?php echo $team->head_coach_id ?>" title="<?php echo $team->head_coach->display_name?>"}}
		</li>
	<?php endif; ?>
		<li>
			<span class="label flat"><?php echo lang('leagues:league').': '?></span>
			<?php echo lang('leagues:league').': '.$team->league->name; ?>
		</li>
		<li>
			<div class="label"><?php echo lang('teams:description'); ?></div>
			<div><?php echo $team->description; ?></div>
		</li>
	</ul>
</br>

</div>
<?php if (isset($extra_enabled['schedules'])) : ?>
<div id="training-times" class="two_third last">
	<h4><?php echo lang('teams:weekly-schedule'); ?></h4>
	<ul>
	<?php  foreach ($schedule as $day) : ?>
		<?php $loc = $locations[$day->location_id]; ?>
		<li>
				<?php echo $day->weekday ?>
			  : <?php echo $day->start_time ?>
			 to <?php echo $day->end_time ?>
			 at {{ url:anchor segments="locations/<?=$loc->id?>" title="<?php echo $loc->name ?>" }}
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>
<?php if (isset($extra_enabled['memberships']) and !empty($members) ) : ?>
<div class="one_full">
	<table>
		<tr>
			<td>
				<h4><?php echo lang('teams:players'); ?></h4>
				<ul>
					<?php foreach ($members as $member)
					{
						echo '<li>' . $member->display_name . '</li>';
					}
					?>
				</ul>
			</td>
		</tr>
	</table>
</div>
<?php endif; ?>
