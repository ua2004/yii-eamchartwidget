<script type="text/javascript"><?php
		/* We will use timestamp to allow multiple instance of widget*/
		$currentTimestamp =  time() + rand(); // rand() is a hack to enable multiple chart instances
		// formatting JSON encoded text in a pretty way so that it doesn't take a one very long line
		$options = $this->prettyJSON(json_encode($options));
		// removing quotes from javascript function names
		$options = preg_replace('/"((?:label|balloon|category|categoryBalloon)Function)": "(.+?)"/', '"$1": $2', $options);
	?>var chart<?php echo $currentTimestamp; ?> = AmCharts.makeChart("chartdiv<?php echo $currentTimestamp; ?>", <?php echo $options; ?>);
			
			chart<?php echo $currentTimestamp; ?>.addListener("rendered", zoomChart);
			<?php
			if( isset($listeners) )
			{
				foreach($listeners as $event=>$listener)
				{
					echo "chart$currentTimestamp.addListener(\"$event\", $listener);\n";
				}
			}
			?>
			
			zoomChart();

			function zoomChart(){
			    chart<?php echo $currentTimestamp; ?>.zoomToIndexes(chart<?php echo $currentTimestamp; ?>.dataProvider.length - 40, chart<?php echo $currentTimestamp; ?>.dataProvider.length - 1);
			}
</script>

<div id="chartdiv<?php echo $currentTimestamp; ?>" style="width: <?php echo $width; ?>; height: <?php echo $height; ?>;"></div>
