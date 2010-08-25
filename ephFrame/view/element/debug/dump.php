<?php

if (Registry::get('DEBUG') <= DEBUG_PRODUCTION) return true;

if (class_exists('QueryHistory')) {
	$queriesTotal = QueryHistory::instance()->count();
	$queriesTime = QueryHistory::instance()->timeTotal(3);
} else {
	$queriesTotal = false;
	$queriesTime = false;
}

?>
<div id="debugDump" style="position: fixed; bottom: 0px;">
	<a href="javascript:void(0);" class="toggle" title="Show/Hide Debugging Console">console</a>
	<div id="debugDumpContent" style="display: none;">
		<h3>Stats</h3>
		<dl>
			<dt>Compile Time</dt>
			<dd>
				<?php echo ephFrame::compileTime(4) ?>s
			</dd>
			<dt>Memory Usage</dt>
			<dd>
				<?php echo ephFrame::memoryUsage(true) ?> (<?php echo ephFrame::memoryUsage() ?> Bytes)
			</dd>
			<?php if (!empty($queriesTotal)) { ?>
			<dt>SQL-Queries</dt>
			<dd>
				<?php echo $queriesTotal ?> (took <?php echo $queriesTime; ?>s)
			</dd>
			<?php } ?>
		</dl>
		<?php
		if (!empty($queriesTotal)) {
			echo $this->element('debug/sql');
		} ?>
	</div>
	<script type="text/javascript" charset="utf-8">
		(function($) {
			$("#debugDump a.toggle").toggle(
				function () {
					$('#debugDumpContent').slideDown();
				},
				function () {
					$('#debugDumpContent').slideUp();
				}
			);
		})(jQuery);
	</script>
</div>