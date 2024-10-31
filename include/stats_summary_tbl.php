<table cellpadding="3" cellspacing="1" border="0">
<tr><td style="background-color:#dddddd">Keywords</td><td style="background-color:#dddddd">Searches</td>
<?php if ( $all_searches ) { 
 $pwp_anchor = 'r';
 ?><td style="background-color:#dddddd">Results</td>
 <?php } else { 
 $pwp_anchor = 'nr';
} ?>
</tr>
<?php
$altclass   = '';
$pwp_pg     = $_GET['pwp_pg'] + 1;
$row_count  = 0;
$loop_count = 0;
$start_no   = (($pwp_pg-1) * $this->pwp_rows_to_show) + 1;
foreach ($results as $result) { 
	$loop_count++;
	$the_term = htmlspecialchars($result->terms);
	ob_start();
	?>
	<tr class="<?php echo $altclass;?>">
	 <td><a href="<?php echo $this->pwp_siteurl.'/wp-admin/edit.php?s='.urlencode($result->terms).'&submit=Search' ?>"><?php echo $the_term;?></a></td>
	 <td><div align="center"><?php echo $result->totcount;?></div></td>
	 <?php if ( $all_searches ) { ?>
	 <td><div align="center"><?php echo $result->hits ?></div></td>
	 <?php } ?>
	</tr>
	<?php
	$the_rows = ob_get_contents();
	ob_end_clean();
	
	if ( $search_group == $_GET['pwp_group'] && $loop_count >= $start_no && $row_count < $this->pwp_rows_to_show ) {
		$row_count++;
		echo $the_rows;
		$altclass = ($altclass == '' ? 'alternate' : '');
	}
	else if ( $search_group != $_GET['pwp_group'] && $row_count < $this->pwp_rows_to_show ) {
		$row_count++;
		echo $the_rows;
		$altclass = ($altclass == '' ? 'alternate' : '');
	}
} 
if ( $this->pwp_pagination == 1 ) {
	?>
	<tr>
	 <?php if ( $pwp_pg > 1 && $search_group == $_GET['pwp_group'] ) { ?>
	 <td><div align="left"><a href="<?php echo $this->pwp_siteurl;?>/wp-admin/index.php?page=<?php echo $this->pwp_path;?>&pwp_pg=<?php echo ($pwp_pg-2);?>&pwp_group=<?php echo $search_group;?>#<?php echo $pwp_anchor;?>">&laquo; Prev</a></div></td>
	 <?php } ?>
	 <?php if ( $total_rows > $this->pwp_rows_to_show && ( ($total_rows-$start_no) >= $this->pwp_rows_to_show || $search_group != $_GET['pwp_group'] ) ) { 
	 if ( $search_group != $_GET['pwp_group'] ) $pwp_pg=1;
	 ?>
	 <td colspan="3"><div align="right"><a href="<?php echo $this->pwp_siteurl;?>/wp-admin/index.php?page=<?php echo $this->pwp_path;?>&pwp_pg=<?php echo $pwp_pg;?>&pwp_group=<?php echo $search_group;?>#<?php echo $pwp_anchor;?>">Next &raquo;</a></div></td>
	 <?php } ?>
	</tr>
	<?php
}
?>
</table>