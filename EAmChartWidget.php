<?php

/**
 * Copyright (c) 2010 Lucas Gómez <luckas1984@gmail.com>
 * Copyright (c) 2014 UA2004 <ua2004@ukr.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * The minimal code needed to use EAmchartWidget is as follows:
 *
 * <pre>
 * <?php
 * //CONTROLLER
 * 
 * //*Using a CActiveDataProvider
 * 
 * //$dataProvider = new CActiveDataProvider('ChartData');
 * 
 * 
 * //*Using a CArrayDataProvider
 * 
 * 
 * //SQL Query
 * 		
 * 		$oDbConnection = Yii::app()->db; // Getting database connection (config/main.php has to set up database
 * 		// Here you will use your complex sql query using a string or other yii ways to create your query
 * 		$oCommand = $oDbConnection->createCommand('SELECT * FROM chart_data');
 * 		
 * 		$oCDbDataReader = $oCommand->queryAll(); // Run query and get all results in a CDbDataReader
 * 
 * // Set DataProvider
 * 		$dataProvider=new CArrayDataProvider($oCDbDataReader, array(
 * 				'keyField' => 'ID'
 * 		));
 * 
 * $this->render('AmChart',array('dataProvider'=>$dataProvider));
 * 
 * 
 * //VIEW
 * 
 * $this->widget('ext.amcharts.EAmChartWidget', 
 * 					array(
 * 						'width' => 700,
 * 						'height' => 400,
 * 						'options'=>array(
 * 									'dataProvider'=>$dataProvider,
 * 									'categoryField' => 'time',
 * 									'type' => 'serial',
 * 									'graphs'=>array(
 * 										array(
 * 											'valueField' => 'data',
 * 											'title'=>'Data graph',
 * 											'type' => 'column'
 * 										)),
 * 									),
 * 									'categoryAxis'=>array(
 * 										'title'=>'Time'
 * 										),
 * 									'valueAxes'=>array(
 * 										array(
 * 											'title'=>'Data'
 * 										)),
 * 						)
 * 					)
 * );
 * </pre>
 *
 *
 * This widget uses Amcharts (http://www.amcharts.com/)
 * to render graphs and charts for your web application.
 *
 * For installation and usage please visit the project home page:
 * http://code.google.com/p/yii-amchart-widget/
 */

class EAmChartWidget extends CWidget
{	
	
	/**
	 * @var String
	 * Width of Chart
	 */
	public $width=400;
	
	/**
	 * @var string
	 * Height of Chart
	 */
	public $height=400;
	
	/**
	 * @var array
	 * Collection of option to customize the chart
	 */
	public $options = array();
	
	
	/**
	* @var array 
	* Valid chart types
	*/
	private $_validChartTypes = array('column','bar','line','area','pie');
	
	
	/**
	 * @var array
	 * Default AmSerialChart Options
	 */
	private $_defaultsAmSerialChartOptions = array(
				'fontFamily'=>'Arial,Helvetica, Sans',
				'startDuration'=>'1',
				'dataProvider'=>array(),
				'categoryField'=> ''
			);
	
	
	/**
	 * @var array
	 * Default AmXYChart Options
	 */
	private $_defaultsAmXYChartOptions = array(
			'panEventsEnabled' => true,
			'marginRight' => 0,
			'marginTop' => 0,
			'startDuration' => 1
	);
	
	
	/**
	 * @var array
	 * Default AmPieChart Options
	 */
	private $_defaultsAmPieChartOptions = array(
			'fontFamily'=>'Arial,Helvetica, Sans',
			'startDuration'=>'1',
			'dataProvider'=>array(),
			'sequencedAnimation' => true,
			'startEffect' => 'elastic',
			'innerRadius' => '30%',
			'startDuration' => 2,
			'labelRadius' => 10,
			'radius' => '45%',
			'pullOutRadius' => 1,
	);

	
	/**
	 * @var array
	 * AmGraph Options
	 */
	private $_defaultsAmGraphOptions = array(
			'title'=>'',
			'lineColor'=>'#CCCCCC',
			'lineAlpha'=>'1',
			'lineThickness' => '1',
			'fillColors'=>'#CCCCCC',
			'fillAlphas'=>'1',
			'valueField'=> '',
			'type'=>'column'
	);
	
	
    /**
     * @var int
     */
    private static $count = 0;
	
	
	/**
	 * The initialization method
	 */
	public function init()
	{
		// ensuring that valid chart type is selected
		foreach ($this->options['graphs'] as $graph)
		{
			if(!in_array($graph['type'], $this->_validChartTypes))
			{
				throw new CException($graph['type'] . ' is an invalid chart type. Valid charts are ' . implode(',',$this->_validChartTypes));
			}
		}
		
		// checking if dataProvider is present
		if(empty($this->options['dataProvider']))
		{
			throw new CException('Please provide some dataProvider to render a display');
		}
		
		// formatting chart size
		if(is_numeric($this->width))
		{
			// if width is a number (not percentage) we attach 'px'
			$this->width .= 'px';
		}
		if(is_numeric($this->height))
		{
			$this->height .= 'px';
		}
		
		$this->_registerWidgetScripts();
		
		parent::init();
	}
	
	
	/**
	 * registerCoreScripts
	 */
	private function _registerWidgetScripts()
	{
		$cs=Yii::app()->getClientScript();
		//$cs->registerCoreScript('jquery');
		
		$basePath = Yii::getPathOfAlias('ext.amcharts.assets');
		$baseUrl = Yii::app()->getAssetManager()->publish($basePath);
		
		$cs->registerScriptFile($baseUrl.'/amcharts.js');
		
		// including necessary js file for current chart type
		$chartType = strtolower($this->options['type']);
		if(file_exists($basePath . DIRECTORY_SEPARATOR . $chartType . '.js'))
		{
			$cs->registerScriptFile($baseUrl . '/' . $chartType . '.js');
		}
		else
		{
			throw new CException($chartType . '.js file not found in ' . $basePath);
		}
		
		// including necessary theme file
		if(isset($this->options['theme']))
		{
			$theme = strtolower($this->options['theme']);
			if(file_exists($basePath . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $theme . '.js'))
			{
				$cs->registerScriptFile($baseUrl . '/themes/' . $theme . '.js');
			}
			else
			{
				throw new CException($theme . '.js file not found in ' . $basePath . DIRECTORY_SEPARATOR . 'themes');
			}
		}
		
		// including necessary language file
		if(isset($this->options['language']) && ($this->options['language'] != 'en'))
		{
			$language = strtolower($this->options['language']);
			if(file_exists($basePath . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $language . '.js'))
			{
				$cs->registerScriptFile($baseUrl . '/lang/' . $language . '.js');
			}
			else
			{
				throw new CException($language . '.js file not found in ' . $basePath . DIRECTORY_SEPARATOR . 'lang');
			}
		}
		
		// default path to images
		if(!isset($this->options['pathToImages']))
		{
			$this->options['pathToImages'] = $baseUrl . "/images/";
		}
		
		// including libs for chart export if necessary
		if(isset($this->options['exportConfig']) || isset($this->options['amExport']))
		{
			$cs->registerScriptFile($baseUrl.'/exporting/amexport.js');
			$cs->registerScriptFile($baseUrl.'/exporting/canvg.js');
			$cs->registerScriptFile($baseUrl.'/exporting/filesaver.js');
			$cs->registerScriptFile($baseUrl.'/exporting/jspdf.js');
			$cs->registerScriptFile($baseUrl.'/exporting/jspdf.plugin.addimage.js');
			$cs->registerScriptFile($baseUrl.'/exporting/rgbcolor.js');
		}
	}
	
	/*
	 * Array Obfuscation
	 * 
	 */
	private static function renameKeys(&$array, $replacement_keys)
	{
		$array = array_combine(array_keys($array), $replacement_keys);
	}
	
	/**
	 * Render the output
	 */
	public function run()
	{	
		$newArray = array();
		
		if($this->options['dataProvider'] instanceof CActiveDataProvider)
		{
			$this->options['dataProvider'] = $this->options['dataProvider']->getData();
			
			foreach ($this->options['dataProvider'] as $modelData)
				$newArray[] = $modelData->attributes;
		}
		else if($this->options['dataProvider'] instanceof IDataProvider)
		{
			$newArray=$this->options['dataProvider']->getData();
		}
		
			
		//foreach ($this->Graphs as $graph)
			//$graph['valueField'] = md5($graph['valueField']);
		
		$this->options['dataProvider'] = $newArray;
		
		
		foreach ($this->options['graphs'] as &$graph)
			$graph = array_merge($this->_defaultsAmGraphOptions, $graph);

		
		switch ($this->options['type'])
		{
			case 'pie':
				$this->render('visualizeSerial',
					array(
						'width'=>$this->width,
						'height'=>$this->height,
						'options'=>array_merge($this->_defaultsAmPieChartOptions, $this->options),
					),
					false
				);
				break;
			default:
				$this->render('visualizeSerial',
					array(
						'width'=>$this->width,
						'height'=>$this->height,
						'options'=>array_merge($this->_defaultsAmSerialChartOptions, $this->options),
					),
					false
				);
		}
	}
	
	/**
	 * Format JSON data in a pretty way - each element from new line,
	 * nested elements tabbed, e.g.
	 * {"key1":[1,2,3],"key2":"value"}
	 * becomes
	 * {
 	 * 		"key1": [
 	 * 			1,
 	 * 			2,
 	 * 			3,
 	 * 		],
 	 * 		"key2": "value"
	 * }
	 * 
	 * @param string $json JSON-encoded string
	 * @return string
	 */
	public function prettyJSON( $json )
	{
		$result = '';
		$level = 0;
		$in_quotes = false;
		$in_escape = false;
		$ends_line_level = NULL;
		$json_length = strlen( $json );
		
		for( $i = 0; $i < $json_length; $i++ ) {
		    $char = $json[$i];
		    $new_line_level = NULL;
		    $post = "";
		    if( $ends_line_level !== NULL ) {
		        $new_line_level = $ends_line_level;
		        $ends_line_level = NULL;
		    }
		    if ( $in_escape ) {
		        $in_escape = false;
		    } else if( $char === '"' ) {
		        $in_quotes = !$in_quotes;
		    } else if( ! $in_quotes ) {
		        switch( $char ) {
		            case '}': case ']':
		                $level--;
		                $ends_line_level = NULL;
		                $new_line_level = $level;
		                break;
		
		            case '{': case '[':
		                $level++;
		            case ',':
		                $ends_line_level = $level;
		                break;
		
		            case ':':
		                $post = " ";
		                break;
		
		            case " ": case "\t": case "\n": case "\r":
		                $char = "";
		                $ends_line_level = $new_line_level;
		                $new_line_level = NULL;
		                break;
		        }
		    } else if ( $char === '\\' ) {
		        $in_escape = true;
		    }
		    if( $new_line_level !== NULL ) {
		        $result .= "\n".str_repeat( "\t", $new_line_level );
		    }
		    $result .= $char.$post;
		}
		
		return $result;
	}
}
