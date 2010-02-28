<?php
class ChartControl{
	
	private static $chart_url = 'reports/generated/charts/';
	private $chart;
	private $title;
	private $height;
	private $width;
	private $dataSet;
	private $name;
	private $cell;
	private $worksheet;

	public function __construct($name , $title){
	
		$this->name = $name;
		$this->title = $title;
	}
	
	public function createVerticalBar($width,$height){
		$this->width = $width;
		$this->height = $height;
		$this->chart = new VerticalBarChart($this->width,$this->height);
	}
	
	public function setData($data){
		
		$dataSet = new XYDataSet();
		
		foreach($data as $info){
			
			$dataSet->addPoint(new Point($info[0], $info[1]));
		
		}
		
		$this->chart->setDataSet($dataSet);
	}
	
	public function render($worksheet, $cell){
		
		$img_url = $chart_url.$this->name.".png";
		
		$this->worksheet = $worksheet;
		$this->cell = $cell;
		
		$this->chart->setTitle($this->title);
		$this->chart->render($img_url);
		
		
		$this->attachChart($img_url);
	}
	
	private function attachChart($img_path){
		
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName($this->title);
		$objDrawing->setDescription($this->title.' graph');
		$objDrawing->setPath($img_path);
		$objDrawing->setCoordinates($this->cell);
		$objDrawing->setWorksheet($this->worksheet);
		
		$getRow = str_replace($this->cell[0] , 	"", $this->cell);
		$this->worksheet->getRowDimension($getRow)->setRowHeight($this->height);
	}

}


?>