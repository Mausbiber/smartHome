<?php
class DataClients{

	private $conn;

	public $id;
	public $title;
	public $ip;
	public $description;

	public function __construct($db){
		$this->conn = $db;
	}


	function create(){

		$query = "INSERT INTO clients values('',?,?,?)";
		$stmt = $this->conn->prepare($query);

		$this->title = filter_var($this->title);
		$this->description = filter_var($this->description);

		if ($stmt->execute(array($this->title, $this->ip, $this->description))) {
			$tmp = $this->conn->prepare( "SELECT id FROM clients ORDER BY id DESC LIMIT 1" );
			$tmp->execute();
			$row = $tmp->fetch(PDO::FETCH_ASSOC);
			return $row['id'];
		} else {
			return false;
		}

	}


	function readAll($page_clients, $frn, $rpp){
		$query = "SELECT
			clients.id AS clients_id,
			clients.title AS clients_title,
			clients.ip AS clients_ip,
			clients.description AS clients_description
			FROM clients
			ORDER BY clients.title ASC LIMIT {$frn}, {$rpp}";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}


	public function countAll(){
		$stmt = $this->conn->prepare( "SELECT id FROM clients" );
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}


	function readOne(){
		$query = "SELECT
			clients.title,
			clients.ip,
			clients.description
			FROM clients
			WHERE clients.id = ? LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->title = $row['title'];
		$this->ip = $row['ip'];
		$this->description = $row['description'];
	}


	function update(){
        $query = "UPDATE clients SET title = :title, ip = :ip, description = :description WHERE id = :id";
		$stmt = $this->conn->prepare($query);

		$this->title = filter_var($this->title);
		$this->description = filter_var($this->description);

		if($stmt->execute(array('title' => $this->title, 'ip' => $this->ip, 'description' => $this->description, 'id' => $this->id))) {
			return true;
		}else{
			return false;
		}
	}


	function delete(){
		$stmt = $this->conn->prepare( "DELETE FROM clients WHERE id = ?" );
  		if($result = $stmt->execute(array($this->id))){
   			return true;
  		}else{
   			return false;
  		}
 	}

}
?>
