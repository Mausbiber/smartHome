<?php
class DataSwitches{

	private $conn;

	public $id;
	public $title;
	public $description;
	public $clientsID;
	public $clientTitle;
	public $switchTypesID;
	public $switchTypTitle;
	public $argA;
	public $argB;
	public $argC;
	public $argD;

	public function __construct($db){
		$this->conn = $db;
	}


	function create(){
		$stmt = $this->conn->prepare( "SELECT clients.id FROM clients WHERE clients.title = ? " );
		$stmt->execute(array(htmlentities($this->clientTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->clientsID = $row['id'];

		$stmt = $this->conn->prepare( "SELECT switch_types.id FROM switch_types WHERE switch_types.title = ? " );
		$stmt->execute(array(htmlentities($this->switchTypTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->switchTypesID = $row['id'];

		$query = "INSERT INTO switches values('',?,?,?,?,?,?,?,?)";
		$stmt = $this->conn->prepare($query);

		$this->title = filter_var($this->title);
		$this->description = filter_var($this->description);
		$this->argA = filter_var($this->argA);
		$this->argB = filter_var($this->argB);
		$this->argC = filter_var($this->argC);
		$this->argD = filter_var($this->argD);

		if ($stmt->execute(array($this->title,
								 $this->description,
								 $this->clientsID,
								 $this->switchTypesID,
								 $this->argA,
								 $this->argB,
								 $this->argC,
								 $this->argD))) {

			return true;

		} else {

			return false;
		}

	}


	function readAll($page_switches, $frn, $rpp){
		$query = "SELECT
			switches.id AS switches_id,
			switches.title AS switches_title,
			switches.description AS switches_description,
            switches.argA as switches_arg_a,
            switches.argB as switches_arg_b,
            switches.argC as switches_arg_c,
            switches.argD as switches_arg_d,
            switch_types.title as switch_types_title,
            clients.title as clients_title
			FROM switches , clients, switch_types
            WHERE switches.clients_id = clients.id AND switches.switch_types_id = switch_types.id
			LIMIT {$frn}, {$rpp}";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}


	function readClients(){
		$stmt = $this->conn->prepare( "SELECT id, title FROM clients ORDER BY title ASC" );
		$stmt->execute();
		return $stmt;
	}


    function readTypes(){
		$stmt = $this->conn->prepare( "SELECT id, title FROM switch_types ORDER BY title ASC" );
		$stmt->execute();
		return $stmt;
	}


	public function countAll(){
		$stmt = $this->conn->prepare( "SELECT id FROM switches" );
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}


	function readOne(){
		$query = "SELECT
            switches.id AS switches_id,
			switches.title AS switches_title,
			switches.description AS switches_description,
            switches.argA as switches_arg_a,
            switches.argB as switches_arg_b,
            switches.argC as switches_arg_c,
            switches.argD as switches_arg_d,
            switch_types.title as switch_types_title,
            clients.title as clients_title
			FROM switches , clients, switch_types
            WHERE switches.clients_id = clients.id AND switches.switch_types_id = switch_types.id AND switches.id = ?
			LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->title = $row['switches_title'];
		$this->description = $row['switches_description'];
		$this->clientTitle = $row['clients_title'];
		$this->switchTypTitle = $row['switch_types_title'];
		$this->argA = $row['switches_arg_a'];
		$this->argB = $row['switches_arg_b'];
		$this->argC = $row['switches_arg_c'];
		$this->argD = $row['switches_arg_d'];
	}


	function update(){
		$stmt = $this->conn->prepare( "SELECT clients.id FROM clients WHERE clients.title = ? " );
		$stmt->execute(array(htmlentities($this->clientTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->clientsID = $row['id'];

		$stmt = $this->conn->prepare( "SELECT switch_types.id FROM switch_types WHERE switch_types.title = ? " );
		$stmt->execute(array(htmlentities($this->switchTypTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->switchTypesID = $row['id'];

        $query = "UPDATE switches SET
            title = :title,
            description = :description,
            clients_id = :clients_id,
            switch_types_id = :switch_types_id,
            argA = :argA,
            argB = :argB,
            argC = :argC,
            argD = :argD
           WHERE id = :id";
		$stmt = $this->conn->prepare($query);

		$this->title = filter_var($this->title);
		$this->description = filter_var($this->description);
		$this->argA = filter_var($this->argA);
		$this->argB = filter_var($this->argB);
		$this->argC = filter_var($this->argC);
		$this->argD = filter_var($this->argD);

		if($stmt->execute(array('title' => $this->title,
                                'description' => $this->description,
                                'clients_id' => $this->clientsID,
                                'switch_types_id' => $this->switchTypesID,
                                'argA' => $this->argA,
                                'argB' => $this->argB,
                                'argC' => $this->argC,
                                'argD' => $this->argD,
                                'id' => $this->id))) {
			return true;
		}else{
			return false;
		}
	}


	function delete(){
		$stmt = $this->conn->prepare( "DELETE FROM switches WHERE id = ?" );
  		if($result = $stmt->execute(array($this->id))){
   			return true;
  		}else{
   			return false;
  		}
 	}

}
?>
