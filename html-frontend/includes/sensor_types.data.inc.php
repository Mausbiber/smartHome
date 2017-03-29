<?php
class DataSensorTypes{

	private $conn;

	public $id;
	public $title;
	public $description;
	public $icon;
    public $icon_tmp;
    public $icon_size;

	public function __construct($db){
		$this->conn = $db;
	}


	function create(){

        $uploaddir  = "/var/www/http/smartHome/img/icons/";
        $uploaddir = $uploaddir . basename($this->icon);
        $uploadOk = 1;
        $imageFileType = pathinfo($uploaddir,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        $check = getimagesize($this->icon_tmp);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        // Check file size
        if ($this->icon_size > 500000) {
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "png") {
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($this->icon_tmp, $uploaddir)) {
                $query = "INSERT INTO sensor_types values('',?,?,?)";
                $stmt = $this->conn->prepare($query);

                $this->title = filter_var($this->title);
                $this->description = filter_var($this->description);

                if ($stmt->execute(array($this->title, $this->description, $this->icon))) {
                    $tmp = $this->conn->prepare( "SELECT id FROM sensor_types ORDER BY id DESC LIMIT 1" );
                    $tmp->execute();
                    $row = $tmp->fetch(PDO::FETCH_ASSOC);
                    return $row['id'];
                }
            }
        }

        return false;

	}


	function readAll($page_clients, $frn, $rpp){
		$query = "SELECT
			sensor_types.id AS sensor_types_id,
			sensor_types.title AS sensor_types_title,
			sensor_types.description AS sensor_types_description,
			sensor_types.icon AS sensor_types_icon
			FROM sensor_types
			ORDER BY sensor_types.title ASC LIMIT {$frn}, {$rpp}";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}


	public function countAll(){
		$stmt = $this->conn->prepare( "SELECT id FROM sensor_types" );
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}


	function readOne(){
		$query = "SELECT
			sensor_types.title,
			sensor_types.description,
			sensor_types.icon
			FROM sensor_types
			WHERE sensor_types.id = ? LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->title = $row['title'];
		$this->description = $row['description'];
		$this->icon = $row['icon'];
	}


	function update(){

        $uploaddir  = "/var/www/http/smartHome/img/icons/";
        $uploaddir = $uploaddir . basename($this->icon);
        $uploadOk = 1;
        $imageFileType = pathinfo($uploaddir,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        $check = getimagesize($this->icon_tmp);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        // Check file size
        if ($this->icon_size > 500000) {
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "png") {
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($this->icon_tmp, $uploaddir)) {
                $query = "UPDATE sensor_types SET title = :title, description = :description, icon = :icon WHERE id = :id";
                $stmt = $this->conn->prepare($query);

                $this->title = filter_var($this->title);
                $this->description = filter_var($this->description);

                if($stmt->execute(array('title' => $this->title, 'description' => $this->description, 'icon' => $this->icon, 'id' => $this->id))) {
                    return true;
                }
            }
        }

        return false;
	}


	function delete(){
		$stmt = $this->conn->prepare( "DELETE FROM sensor_types WHERE id = ?" );
  		if($result = $stmt->execute(array($this->id))){
   			return true;
  		}else{
   			return false;
  		}
 	}
}
?>
