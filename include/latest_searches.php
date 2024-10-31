<div class="wrap">
<?php $this->pwpHeader();?>
<?php $this->pwpStatsMenu();?>		
<br />	
<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper">
  <fieldset id="PSOptions"class="dbx-box">
   <!-- Box-1 -->
   <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Last 100 Searches:</h3></div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<?php
	if (count($results)) {
		?>
		<table cellpadding="3" cellspacing="1" border="0" style="border:1px solid #eeeeee; padding:0;">
		<tr>
		 <td style="background-color:#dddddd; padding:4px;" width="165">Date &amp; time</td>
		 <td style="background-color:#dddddd; padding:4px;" width="150">IP</td>
		 <td style="background-color:#dddddd; padding:4px;">Keywords</td>
		 <td style="background-color:#dddddd; padding:4px;">Results</td>
		</tr>
		<?php
		$i = 0;
		foreach ($results as $result) {
			$i++;
			$bg_col = ($i % 2 == 0) ? '#ffffff' : '#f4f4f4';
			$datetime           = $result->datetime;
			$datetime_parts     = explode(' ', $datetime);
			$date_parts         = explode('-', $datetime_parts[0]);
			$formatted_datetime = $date_parts[1].'-'.$date_parts[2].'-'.$date_parts[0].' '.$datetime_parts[1];
			?>
			<tr valign="top">
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><?php echo $formatted_datetime ?></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><?php echo $result->details ?></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><a href="<?php echo $this->pwp_siteurl.'/wp-admin/edit.php?s='.urlencode($result->terms).'&submit=Search' ?>"><?php echo htmlspecialchars($result->terms) ?></a></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><div align="center"><?php echo $result->hits ?></div></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	} else { ?>
		<p>No searches saved.</p>
	<?php } ?>
	</div>
   </div>
   <!-- Eof bigbox-1 -->
  </fieldset>
 </div>
</div>
<?php $this->pwpFooter('dashboard');?>
</div>