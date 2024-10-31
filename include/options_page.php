<style type="text/css">
table, tbody, tfoot, thead, tr, th, td {
	padding: 1px;
}</style>
<div class="wrap">
<?php $this->pwpHeader();?>
<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper" <?php echo $pwp_block_style;?>>
  <fieldset id="ps-se"class="dbx-box">
   <!-- Box-1 -->
   <div class="dbx-h-andle-wrapper">
     <h3 class="dbx-handle">Related Posts for Visitors From Search Engines Options</h3>
   </div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<form method="post">
	<?php if (function_exists('wp_nonce_field')) {wp_nonce_field('psychic-search-options');}?>
	<p>
	<input type="checkbox" name="pwp[disable_related_posts]" value="1" <?php echo $disable_related_posts_chk;?> onclick="pwpShowHide('se_disable_div', '')" />
	<strong>Disable Related Posts For Visitors From Search Engines</strong>
	</p>
	<div id="se_disable_div" style="display:<?php echo $disable_related_posts_show;?>">
	<p>
	<strong>Number of Related Posts to Show: </strong>
	<input type="text" name="pwp[noof_related_posts]" value="<?php echo $this->pwp_option['noof_related_posts'];?>" size="2" />
	</p>
	<p>
	<strong>Position:</strong><br />
	<input type="radio" name="pwp[related_posts_position]" id="related_posts_position_top" value="top" <?php echo $related_posts_position_top_chk;?> /> Top of First Post <br />
	<input type="radio" name="pwp[related_posts_position]" id="related_posts_position_bottom" value="bottom" <?php echo $related_posts_position_bottom_chk;?> /> Bottom of Last Post <br />
	<input type="radio" name="pwp[related_posts_position]" id="related_posts_position_manual" value="manual" <?php echo $related_posts_position_manual_chk;?> /> Don't Show Related Posts Automatically, I'll Use the theme funciton or widget <br />
	</p>
	<p>
	<a style="cursor:hand;cursor:pointer;" onclick="pwpShowHide('hlp1_div', 'hlp1_img')"><img src="<?php echo $this->pwp_fullpath?>include/images/plus.gif" id="hlp1_img" border="0" /><strong>Theme Function For The Related Posts For Visitors From Search Engines:</strong></a>
	<div id="hlp1_div" style="background-color:#ffffff; display:none; padding:0 5px 0 5px;">
	You can add the "related posts for visitors from search engines" anywhere you like in the theme by using this function:<br />
	ps_related_posts($number_of_posts_to_show, '$before_title', '$after_title', '$before_post', '$after_post');<br /><br />
	<strong>Example:</strong><br />
	ps_related_posts(5, '&lt;li&gt;', '&lt;/li&gt;', 'Related Posts:&lt;ul&gt;', '&lt;/ul&gt;');<br /><br />
	</div>
	</p>
	<p>
	<a style="cursor:hand;cursor:pointer;" onclick="pwpShowHide('fpl_div', 'fpl_img')"><img src="<?php echo $this->pwp_fullpath?>include/images/plus.gif" id="fpl_img" border="0" /><strong>Format The Posts Listing</strong></a>
	<div id="fpl_div" style="background-color:#ffffff; display:none; padding:0 5px 0 5px;">
	<table>
	<tr><td>&nbsp;</td><td>[ Tags: %keyword% &nbsp;|&nbsp; %rss-url% ]</td></tr>
	<tr>
	<td width="250">Texts before the start of post listing: </td><td><input type="text" name="pwp[before_post]" value="<?php echo htmlspecialchars($this->pwp_option['before_post']);?>" size="45" /></td>
	</tr>
	<tr>
	<td>Link Separator Begin: </td><td><input type="text" name="pwp[before_title]" value="<?php echo $this->pwp_option['before_title'];?>" size="45" /></td>
	</tr>
	<tr>
	<td>Link Separator End: </td><td><input type="text" name="pwp[after_title]" value="<?php echo $this->pwp_option['after_title'];?>" size="45" /></td>
	</tr>
	<tr>
	<td>Texts after the end of post listing: </td><td><input type="text" name="pwp[after_post]" value="<?php echo $this->pwp_option['after_post'];?>" size="45" /></td>
	</tr>
	</table>
	<p>
	<strong>Text to show when no related posts found:</strong><br />
	<textarea rows="2" cols="58" name="pwp[no_related_posts_txt]"><?php echo htmlspecialchars($this->pwp_option['no_related_posts_txt']);?></textarea>
	</p>
	</div>
	</p>
	</div>
	<input type="submit" name="pwp[save_related_posts_options]" class="button" value="Save" />
	</form>
	</div>
   </div>
   <!-- Eof Box-1 -->
  </fieldset>
 </div>
</div><br>

<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper" <?php echo $pwp_block_style;?>>
  <fieldset id="ps-options"class="dbx-box">
   <!-- Box-2 -->
   <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">More Options</h3></div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<form method="post">
	<?php if (function_exists('wp_nonce_field')) {wp_nonce_field('psychic-search-options');}?>
	<p><input type="submit" class="button" name="pwp[pwp_reset]" value="Reset Statistics &raquo;" onclick="return confirm('You are about to delete all saved search statistics.\n  \'Cancel\' to stop, \'OK\' to delete.');" /></p>
	<p>
	<strong>Show the stats to: </strong>
	<select name="pwp[stats_access_level]">
	 <option value="10" <?php $this->pwp_option['stats_access_level']==10?print"selected":'';?>>Administrator</option>
	 <option value="7" <?php $this->pwp_option['stats_access_level']==7?print"selected":'';?>>Editor</option>
	 <option value="2" <?php $this->pwp_option['stats_access_level']==2?print"selected":'';?>>Author</option>
	 <option value="1" <?php $this->pwp_option['stats_access_level']==1?print"selected":'';?>>Contributor</option>
	 <option value="0" <?php $this->pwp_option['stats_access_level']==0?print"selected":'';?>>Subscriber</option>
	</select>
	</p>
	<p>
	<input type="checkbox" name="pwp[admin_search]" value="1" <?php echo $admin_search_chk;?> />
	<strong>Count searches from admin.</strong>
	</p>
	<p>
	<input type="checkbox" name="pwp[loggedin_user_search]" value="1" <?php echo $loggedin_user_search_chk;?> />
	<strong>Count searches from logged in user.</strong>
	</p>
	<input type="submit" name="pwp[save_more_options]" class="button" value="Save Settings" />
	</form>
	</div>
   </div>
   <!-- Eof Box-2 -->
  </fieldset>
 </div>
</div>

<?php $this->pwpFooter('options');?>
</div>