<table cellpadding="3" cellspacing="1" border="0">
<tr><td style="background-color:#dddddd">Keywords</td><td style="background-color:#dddddd">Searches</td><td style="background-color:#dddddd"><div align="center">Related Posts</div></td><td style="background-color:#dddddd"><div align="center">Searched From</div></td>
</tr>
<?php
$altclass   = '';
$pwp_pg     = $_GET['pwp_pg'] + 1;
$row_count  = 0;
$loop_count = 0;
$start_no   = (($pwp_pg-1) * $this->pwp_rows_to_show) + 1;

// Arrange in Descending Order according to no. of searches
$se_terms_array   = array();
$se_terms_cnt_arr = array();
foreach ( $se_terms_arr as $term => $term_arr ) { 
	$se_terms_cnt_arr[$term] = count($term_arr);
}
arsort($se_terms_cnt_arr);
reset($se_terms_cnt_arr);
foreach ( $se_terms_cnt_arr as $term => $search_count ) { 
	$se_terms_array[$term] = $se_terms_arr[$term];
}

foreach ( $se_terms_array as $term => $term_arr ) { 
	$loop_count++;
	$_term = htmlspecialchars($term);
	$_host          = '';
	$_referrer      = '';
	$_searches      = 0;
	$_related_posts = 0;
	foreach ( (array) $term_arr as $details_arr ) {
		$_searches++;
		$_host          .= ','.$details_arr[0];
		$_related_posts  = $details_arr[3];
		$_referrer      .= ','.$details_arr[4];
	}
	$_host     = trim($_host,',');
	$_referrer = trim($_referrer,',');
	$se_icons  = $this->pwpDisplayIcon($_host,$_referrer);
	ob_start();
	?>
	<tr class="<?php echo $altclass;?>">
	 <td><a href="<?php echo $preview_url;?>pwp_preview=<?php echo $term;?>" title="Preview" target="_blank"><?php echo $term;?></a></td>
	 <td><div align="center"><?php echo $_searches;?></div></td>
	 <td><div align="center"><?php echo $_related_posts;?>&nbsp;
	 <a href="<?php echo $preview_url;?>pwp_preview=<?php echo $term;?>" title="Preview" target="_blank" style="border-bottom:0px"><img src="<?php echo $this->pwp_fullpath;?>include/images/preview.gif" align="absmiddle" border="0" /></a>
	 </div></td>
	 <td><div align="center"><?php echo $se_icons;?></div></td>
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
	 <td colspan="4"><div align="right"><a href="<?php echo $this->pwp_siteurl;?>/wp-admin/index.php?page=<?php echo $this->pwp_path;?>&pwp_pg=<?php echo $pwp_pg;?>&pwp_group=<?php echo $search_group;?>#<?php echo $pwp_anchor;?>">Next &raquo;</a></div></td>
	 <?php } ?>
	</tr>
	<?php
}
?>
</table>