<?php

if (!class_exists('QueryHistory')) return false;

?>
<div class="<?php echo $elementBaseName; ?>">
	<h3>SQL Queries</h3>
	<table>
		<thead>
			<tr>
				<th width="20">#</th>
				<th width="30">n</th>
				<th width="30">t</th>
				<th>Query</th>
			</tr>
		</thead>
		<tbody style="overflow-y: scroll; height: 300px;">
			<?php foreach(QueryHistory::getInstance()->data as $i => $Query) { ?>
			<tr class="pre">
				<td><?php echo $i+1; ?></td>
				<td><?php echo $Query['result']->numRows(); ?></td>
				<td><?php echo round($Query['timer']->render() * 1000) ?>ms</td>
				<td>
					<pre><?php echo (string) $Query['query'] ?></pre>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>