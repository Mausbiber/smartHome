<?php
class DataSensors{

	private $conn;

	public $id;
	public $title;
	public $description;
	public $clientsID;
	public $clientTitle;
	public $sensorTypesID;
	public $sensorTypTitle;
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

		$stmt = $this->conn->prepare( "SELECT sensor_types.id FROM sensor_types WHERE sensor_types.title = ? " );
		$stmt->execute(array(htmlentities($this->sensorTypTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->sensorTypesID = $row['id'];

		$query = "INSERT INTO sensors values('',?,?,?,?,?,?,?,?)";
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
								 $this->sensorTypesID,
								 $this->argA,
								 $this->argB,
								 $this->argC,
								 $this->argD))) {

			return true;

		} else {

			return false;
		}

	}
    
	function read(){
		$query = "SELECT
			sensors.id AS sensors_id,
			sensors.title AS sensors_title,
			sensors.description AS sensors_description,
            sensors.argA as sensors_arg_a,
            sensors.argB as sensors_arg_b,
            sensors.argC as sensors_arg_c,
            sensors.argD as sensors_arg_d,
            sensor_types.title as sensor_types_title,
            clients.title as clients_title
			FROM sensors , clients, sensor_types
            WHERE sensors.clients_id = clients.id AND sensors.sensor_types_id = sensor_types.id";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}

	function readAll($page_sensors, $frn, $rpp){
		$query = "SELECT
			sensors.id AS sensors_id,
			sensors.title AS sensors_title,
			sensors.description AS sensors_description,
            sensors.argA as sensors_arg_a,
            sensors.argB as sensors_arg_b,
            sensors.argC as sensors_arg_c,
            sensors.argD as sensors_arg_d,
            sensor_types.title as sensor_types_title,
            clients.title as clients_title
			FROM sensors , clients, sensor_types
            WHERE sensors.clients_id = clients.id AND sensors.sensor_types_id = sensor_types.id
			LIMIT {$frn}, {$rpp}"; //TODO: Bobby Table
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
		$stmt = $this->conn->prepare( "SELECT id, title FROM sensor_types ORDER BY title ASC" );
		$stmt->execute();
		return $stmt;
	}


	public function countAll(){
		$stmt = $this->conn->prepare( "SELECT id FROM sensors" );
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}


	function readOne(){
		$query = "SELECT
            sensors.id AS sensors_id,
			sensors.title AS sensors_title,
			sensors.description AS sensors_description,
            sensors.argA as sensors_arg_a,
            sensors.argB as sensors_arg_b,
            sensors.argC as sensors_arg_c,
            sensors.argD as sensors_arg_d,
            sensor_types.title as sensor_types_title,
            clients.title as clients_title
			FROM sensors , clients, sensor_types
            WHERE sensors.clients_id = clients.id AND sensors.sensor_types_id = sensor_types.id AND sensors.id = ?
			LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->title = $row['sensors_title'];
		$this->description = $row['sensors_description'];
		$this->clientTitle = $row['clients_title'];
		$this->sensorTypTitle = $row['sensor_types_title'];
		$this->argA = $row['sensors_arg_a'];
		$this->argB = $row['sensors_arg_b'];
		$this->argC = $row['sensors_arg_c'];
		$this->argD = $row['sensors_arg_d'];
	}


	function update(){
		$stmt = $this->conn->prepare( "SELECT clients.id FROM clients WHERE clients.title = ? " );
		$stmt->execute(array(htmlentities($this->clientTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->clientsID = $row['id'];

		$stmt = $this->conn->prepare( "SELECT sensor_types.id FROM sensor_types WHERE sensor_types.title = ? " );
		$stmt->execute(array(htmlentities($this->sensorTypTitle)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->sensorTypesID = $row['id'];

        $query = "UPDATE sensors SET
            title = :title,
            description = :description,
            clients_id = :clients_id,
            sensor_types_id = :sensor_types_id,
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
                                'sensor_types_id' => $this->sensorTypesID,
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
		$stmt = $this->conn->prepare( "DELETE FROM sensors WHERE id = ?" );
  		if($result = $stmt->execute(array($this->id))){
   			return true;
  		}else{
   			return false;
  		}
 	}

}
?>
