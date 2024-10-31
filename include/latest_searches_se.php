<div class="wrap">
<?php $this->pwpHeader();?>
<?php $this->pwpStatsMenu();?>		
<br />	
<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper">
  <fieldset id="PSOptions"class="dbx-box">
   <!-- Box-1 -->
   <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Last 100 Searches From Search Engines:</h3></div>
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
		 <td style="background-color:#dddddd; padding:4px;">Related Posts</td>
		 <td style="background-color:#dddddd; padding:4px;">Searched From</td>
		</tr>
		<?php
		foreach ( $se_terms_arr as $term_arr ) { 
			$i++;
			$bg_col = ($i % 2 == 0) ? '#ffffff' : '#f4f4f4';
			$_host          = $term_arr[0];
			$_term          = htmlspecialchars($term_arr[1]);
			$_datetime      = $term_arr[2];
			$_related_posts = $term_arr[3];
			$_referrer      = $term_arr[4];
			$_ip            = $term_arr[5];
			$_searches      = $term_arr[6];
			$datetime_parts     = explode(' ', $_datetime);
			$date_parts         = explode('-', $datetime_parts[0]);
			$formatted_datetime = $date_parts[1].'-'.$date_parts[2].'-'.$date_parts[0].' '.$datetime_parts[1];
			$se_icons           = $this->pwpDisplayIcon($_host,$_referrer);
			?>
			<tr valign="top">
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><?php echo $formatted_datetime;?></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><?php echo $_ip;?></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><a href="#" onclick="window.open('<?php echo $preview_url;?>pwp_preview=<?php echo $_term;?>');" title="Preview"><?php echo htmlspecialchars($_term);?></a></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><div align="center"><?php echo $_related_posts;?></div></td>
			<td style="background-color:<?php echo $bg_col ?>; padding:4px;"><div align="center"><?php echo $se_icons;?></div></td>
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