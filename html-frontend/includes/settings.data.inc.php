<?php
class DataSettings{

	private $conn;

	public $scheduler_preview_period;
	public $scheduler_preview_items;
	public $scheduler_settings_page_per_view;
    public $search_for_field;
    public $result;

	public function __construct($db){
		$this->conn = $db;
	}


	function readAll(){
		$query = "SELECT
			settings.scheduler_preview_period AS scheduler_preview_period,
			settings.scheduler_preview_items AS scheduler_preview_items,
			settings.scheduler_settings_page_per_view AS scheduler_settings_page_per_view
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
        $query = "UPDATE settings SET
            scheduler_preview_period = :scheduler_preview_period,
            scheduler_preview_items = :scheduler_preview_items,
            scheduler_settings_page_per_view = :scheduler_settings_page_per_view";
		$stmt = $this->conn->prepare($query);

		if($stmt->execute(array(
            'scheduler_preview_period' => $this->scheduler_preview_period,
            'scheduler_preview_items' => $this->scheduler_preview_items,
            'scheduler_settings_page_per_view' => $this->scheduler_settings_page_per_view))) {
			return true;
		}else{
			return false;
		}
	}

}
?>
