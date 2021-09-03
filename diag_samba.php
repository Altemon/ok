<?php
/*
 * smb_diag.php
 *
 * Part of pf2ad (https://pf2ad.com)
 */

require_once('guiconfig.inc');
require_once('notices.inc');

# ------------------------------------------------------------------------------
# defines
# ------------------------------------------------------------------------------

# ------------------------------------------------------------------------------
# Requests
# ------------------------------------------------------------------------------
if ($_REQUEST['action']) {
	header("Content-type: text/javascript");
	echo diag_samba_AJAX_response( $_REQUEST['action'] );
	exit;
}

# ------------------------------------------------------------------------------
# Functions
# ------------------------------------------------------------------------------

function diag_samba_AJAX_response( $act ) {
	# Actions
	switch($act) {
		case 'domaininfo':
			$res = "<pre>";
			$res .= shell_exec('net ads info');
			$res .="</pre>";
			break;
		case 'winbindtest':
			$res = "<pre>";
			$res .= "Test connection: \n";
			$res .= shell_exec('wbinfo -p');
			$res .= "-------------------------------------\nList users: \n";
			$res .= shell_exec('wbinfo -u');
			$res .= "-------------------------------------\nList groups: \n";
			$res .= shell_exec('wbinfo -g');
			$res .= "</pre>";
			break;
	}

	return $res;
}

# ------------------------------------------------------------------------------
# HTML Page
# ------------------------------------------------------------------------------

$pgtitle = array(gettext("Package"), gettext("Samba"), gettext("Diagnostics"));
include("head.inc");
$tab_array = array();
$tab_array[] = array(gettext("General settings"), false, "/pkg_edit.php?xml=samba.xml&amp;id=0");
$tab_array[] = array(gettext("Diagnostics"), true,  $selfpath);
display_top_tabs($tab_array);
?>
<div class="panel panel-default">
	<div class="panel-heading"><h2 class="panel-title"><?=gettext("Diagnostics Tools"); ?></h2></div>
	<div class="panel-body">
		<div class="table-responsive">
			<form action="sg_log.php" method="post">
			<input type="hidden" id="reptype" val="">
			<input type="hidden" id="offset"  val="0">
			<table class="table table-hover table-condensed">
				<thead>
				<tr>
					<th class="text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-xs btn-default" id="hd_domaininfo" name="hd_domaininfo" onclick="getactivity('domaininfo');">
								<i class="fa fa-info-circle"></i>
								<?= gettext("Domain Information") ?>
							</button>
							<button type="button" class="btn btn-xs btn-default" id="hd_winbindtest" name="hd_winbindtest" onclick="getactivity('winbindtest');">
								<i class="fa fa-cogs"></i>
								<?= gettext("Test Winbind Connection") ?>
							</button>
						</div>
					</th>
				</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="5">
							<div id="reportarea" name="reportarea"><?= gettext("Select a tool above to view its contents."); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[

function getactivity(action) {
	$.ajax({
		url: './diag_samba.php?action=' + action,
		cache: false
	}).done(
		function(output) {
			$("#reportarea").html(output);
		}
	);

}

//]]>
</script>
<?php include("foot.inc"); ?>
