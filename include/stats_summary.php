<script type="text/javascript">
function psValidateRptForm() {
	var rpt_email = document.getElementById('rpt_email');
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if ( rpt_email.value != '' && reg.test(rpt_email.value) == false ) {
		alert('Valid Email Required');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript" src="<?php echo $this->pwp_fullpath;?>include/tooltip.js"></script>
<link href="<?php echo $this->pwp_fullpath;?>include/tooltip.css" rel="stylesheet" type="text/css">
<style type="text/css">
table, tbody, tfoot, thead, tr, th, td {
	padding: 1px;
}</style>
<div class="wrap">
<?php $this->pwpHeader();?>
<?php $this->pwpStatsMenu();?>
<br />		
<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper">
  <fieldset id="ps-war"class="dbx-box">
   <!-- Box-1 -->
   <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><a name="nr"></a>Searches Without Any Result (Within Your Blog)</h3>
   </div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<table cellpadding="3" cellspacing="1" border="0" style="border:1px solid #dddddd; background-color:#f1f1f1; padding:0;">
	 <tr>
	  <td valign="top">
		<strong>Last 24 Hours</strong>
		<?php $this->pwpSummaryTable(1, false, 'nr1day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 7 Days</strong>
		<?php $this->pwpSummaryTable(7, false, 'nr7day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 30 Days</strong>
		<?php $this->pwpSummaryTable(30, false, 'nr30day'); ?>
	  </td>
	 </tr>
	</table>
	</div>
   </div>
   <!-- Eof Box-1 -->
  </fieldset>
 </div>
</div>

<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper">
  <fieldset id="ps-wyb"class="dbx-box">
   <!-- Box-2 -->
   <div class="dbx-h-andle-wrapper">
	 <h3 class="dbx-handle"><a name="r"></a>Searches Within Your Blog</h3>
  </div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<table cellpadding="3" cellspacing="1" border="0" style="border:1px solid #dddddd; background-color:#f1f1f1; padding:0;">
	 <tr>
	  <td valign="top">
		<strong>Last 24 Hours</strong>
		<?php $this->pwpSummaryTable(1, true, 'r1day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 7 Days</strong>
		<?php $this->pwpSummaryTable(7, true, 'r7day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 30 Days</strong>
		<?php $this->pwpSummaryTable(30, true, 'r30day'); ?>
	  </td>
	 </tr>
	</table>
	</div>
   </div>
   <!-- Eof Box-2 -->
  </fieldset>
 </div>
</div>

<div id="advancedstuff" class="dbx-group">
 <div class="dbx-b-ox-wrapper">
  <fieldset id="ps-fse"class="dbx-box">
   <!-- Box-3 -->
   <div class="dbx-h-andle-wrapper">
	 <h3 class="dbx-handle"><a name="se"></a>Searches From Search Engines</h3>
  </div>
   <div class="dbx-c-ontent-wrapper">
	<div class="dbx-content">
	<table cellpadding="3" cellspacing="1" border="0" style="border:1px solid #dddddd; background-color:#f1f1f1; padding:0;">
	 <tr>
	  <td valign="top" colspan="5">
	  <strong>Preview Page for Keyword: </strong>
	  <input type="text" name="pwp[se_preview_txt]" id="se_preview_txt" value="<?php echo htmlspecialchars($this->pwp_request['se_preview_txt']); ?>" size="20" />
	  <input type="button" name="pwp[se_preview]" id="se_preview" onclick="window.open('<?php echo $preview_url;?>pwp_preview='+document.getElementById('se_preview_txt').value);" value="Preview &raquo;" class="button" />&nbsp; 
	  <a href="#" onMouseover="tooltip('<?php echo $preview_tooltip;?>',320)" onMouseout="hidetooltip()" style="border-bottom:none;"><img src="<?php echo $this->pwp_fullpath;?>include/images/help.gif" border="0" align="absmiddle" /></a>
	  </td>
	 </tr>
	 <tr>
	  <td valign="top" colspan="5"><hr align="center" color="#cccccc" style="width:100%;height:1px" /></td>
	 </tr>
	 <tr>
	  <td valign="top">
		<strong>Last 24 Hours</strong>
		<?php $this->pwpSummaryTableSE(1, 'se1day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 7 Days</strong>
		<?php $this->pwpSummaryTableSE(7, 'se7day'); ?>
	  </td>
	  <td width="2%"></td>
	  <td valign="top"><strong>Last 30 Days</strong>
		<?php $this->pwpSummaryTableSE(30, 'se30day'); ?>
	  </td>
	 </tr>
	</table>
	</div>
   </div>
   <!-- Eof Box-3 -->
  </fieldset>
 </div>
</div>

<form name="rpt_email_form" method="post" action="" onsubmit="return psValidateRptForm()">
<h3><input type="checkbox" name="pwp[email_report]" value="1" <?php echo $email_report_chk;?> />
<a name="pwp_e" href="#pwp_e" onClick="pwpShowHide('pwp_e_dv','');"><strong>Email me the report</strong></a></h3>
<div id="pwp_e_dv" style="display:none">
<table width="100%" cellspacing="0" cellpadding="3" style="border:1px solid #dddddd; background-color:#f1f1f1; padding:2px;">
  <tr>
   <td width="100"><strong>My Email: </strong></td>
   <td><input type="text" name="pwp[rpt_email]" id="rpt_email" value="<?php echo $this->pwp_option['rpt_email'];?>" size="30" /></td>
  </tr>
  <tr>
   <td><strong>Send Report: </strong></td>
   <td>
   <select name="pwp[rpt_type]" id="rpt_type">
	<option value="daily" <?php if($this->pwp_option['rpt_type']=='daily'){print'selected';}?>>Daily</option>
	<option value="weekly" <?php if($this->pwp_option['rpt_type']=='weekly'){print'selected';}?>>Weekly</option>
	<option value="monthly" <?php if($this->pwp_option['rpt_type']=='monthly'){print'selected';}?>>Monthly</option>
   </select>
   </td>
  </tr>
</table><br />
</div>
<input type="submit" name="pwp[rpt_submit]" value="Save" class="button" /><br /><br />
</form>

<?php $this->pwpFooter('dashboard');?>
</div>
