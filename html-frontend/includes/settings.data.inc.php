<?php
class DataSettings{

	private $conn;

	public $scheduler_preview_period;
	public $scheduler_preview_items;
	public $scheduler_settings_page_per_view;
	public $show_seconds;
    public $search_for_field;
    public $result;

	public function __construct($db){
		$this->conn = $db;
	}


	function readAll(){
		$query = "SELECT
			settings.scheduler_preview_period AS scheduler_preview_period,
			settings.scheduler_preview_items AS scheduler_preview_items,
			settings.scheduler_settings_page_per_view AS scheduler_settings_page_per_view,
			settings.show_seconds AS show_seconds
			FROM settings";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
	}


	function readOne(){
		$query = "SELECT ? FROM settings";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->search_for_field));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->result = $row[$this->search_for_field];
	}


	function update(){
		if (!isset($this->show_seconds))
		{
			$this->show_seconds=0;
		}
        $query = "UPDATE settings SET
            scheduler_preview_period = :scheduler_preview_period,
            scheduler_preview_items = :scheduler_preview_items,
            scheduler_settings_page_per_view = :scheduler_settings_page_per_view,
			show_seconds = :show_seconds";
		$stmt = $this->conn->prepare($query);

		if($stmt->execute(array(
            'scheduler_preview_period' => $this->scheduler_preview_period,
            'scheduler_preview_items' => $this->scheduler_preview_items,
            'scheduler_settings_page_per_view' => $this->scheduler_settings_page_per_view,
			'show_seconds' => $this->show_seconds))) {
			return true;
		}else{
			return false;
		}
	}

}
?>
