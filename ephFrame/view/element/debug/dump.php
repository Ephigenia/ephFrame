<?php
/**
 * 
 */
if (Registry::get('DEBUG') < DEBUG_VERBOSE && get_class($this) !== 'HTMLView') continue;

$compileTime = ephFrame::compileTime(4);
if (class_exists('QueryHistory')) {
	$queriesTotal = QueryHistory::getInstance()->count();
	$queriesTime = QueryHistory::getInstance()->timeTotal(3);
} else {
	$queriesTotal = false;
	$queriesTime = false;
}

?>
<div id="debugDump">
	<h3>Stats</h3>
	<dl>
		<dt>Compile Time<dt>
		<dd>
			<?php echo $compileTime ?>s
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