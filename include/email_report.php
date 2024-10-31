<table cellpadding="3" cellspacing="1" border="0" bgcolor="#fcfcfc" style="border:1px solid #eeeeee">
<tr bgcolor="#cccccc"><td colspan="5"><b>Searches Without Any Result (Within Your Blog)</b></td></tr>
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
</table><br />
<table cellpadding="3" cellspacing="1" border="0" bgcolor="#fcfcfc" style="border:1px solid #eeeeee">
<tr bgcolor="#cccccc"><td colspan="5"><b>Searches Within Your Blog</b></td></tr>
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
</table><br />
<table cellpadding="3" cellspacing="1" border="0" bgcolor="#fcfcfc" style="border:1px solid #eeeeee">
<tr bgcolor="#cccccc"><td colspan="5"><b>Searches From Search Engines</b></td></tr>
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