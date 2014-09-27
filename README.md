EAmChartWidget
==============

This widget for use with the Yii Framework uses [AmCharts] library to render graphs and charts for your web application.

Requirements
------------

Yii 1.1 or above

Usage
-----

The following code is a simple instance of Widget.

```php
// you can use any provider that implements IDataProvider or uses CActiveDataProvider
$dataProvider = new CActiveDataProvider('OlympicMedals');
 
 
// chart with bar & line graph
$this->widget('ext.amcharts.EAmChartWidget', array(
	'width' => '100%', // width of the chart
	'height' => 400, // height of the chart
	'options'=>array(
		'dataProvider'=>$dataProvider,
		'categoryField' => 'Date',  // field of the dataProvider to set on the X axis
		'type' => 'serial',
		'graphs'=>array(
			array(
				'valueField' => 'GoldMedals', // field of the dataProvider to set on the Y Axis
				'title'=>'Value', // graph title, used in the Legend
                'type' => 'bar' // graph type
			),
			array(
                'valueField' => 'SilverMedals',
                'title'=>'Value',
                'type' => 'line',
                'fillAlphas'=>0,
                'lineColor'=>'#EE2299',
                'bullet'=>'round'
            ),
		),
		'categoryAxis'=>array(
			'title'=>'Country',
		),
		'valueAxes'=>array(
			array(
				'title'=>'Medals',
			),
		),
	),
));
 
// chart with pie
$this->widget('ext.amcharts.EAmChartWidget',
    array(
        'width' => '100%',
        'height' => 400,
        'options'=>array(
            'dataProvider'=>$someData,
            'titleField' => 'Country',
            'valueField' => 'Medals',
            'type' => 'pie'
        )
    ));
```
            
You can create the following chart types: 
* Funnel
* Gauge
* Pie
* Radar
* Serial
* XY

Installation
------------

* Download the widget
* Place it under `/YourProject/protected/extensions/` directory

Resources
---------

* [Project page]
* [AmCharts documentation]

[AmCharts]:http://www.amcharts.com/
[Project page]:https://code.google.com/p/yii-amchart-widget/
[AmCharts documentation]:http://docs.amcharts.com/3/javascriptcharts
