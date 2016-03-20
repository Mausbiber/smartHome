<?php
class DataScheduler{

	private $conn;

	public $id;
	public $title;
	public $switchesID;
	public $switchCommand;
	public $strDateStart;
	public $strDateStop;
	public $strTimeStart;
	public $strTimeStop;
	public $dateStartOn;
	public $dateStartOff;
	public $dateStop;
	public $dateStopOn;
	public $dateStopOff;
	public $duration;
	public $intervalNumber;
	public $intervalUnit;
	public $weeklyMonday;
	public $weeklyTuesday;
	public $weeklyWednesday;
	public $weeklyThursday;
	public $weeklyFriday;
	public $weeklySaturday;
	public $weeklySunday;
	
	public $events;
	
	public function __construct($db){
		$this->conn = $db;
	}


	function create(){
		$stmt = $this->conn->prepare( "SELECT switches.id FROM switches WHERE switches.title = ? " );
		$stmt->execute((array($this->switchCommand)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->switchesID = $row['id'];

		$query = "INSERT INTO schedulers values('',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $this->conn->prepare($query);
		
		$this->dateStartOn = date("Y-m-d H:i:s", strtotime(($this->strDateStart)." ".($this->strTimeStart)));
		$tmpHourOn = date("H", strtotime($this->strTimeStart));
		$tmpHourOff = date("H", strtotime($this->strTimeStop));
		$tmpMinuteOn = date("i", strtotime($this->strTimeStart));
		$tmpMinuteOff = date("i", strtotime($this->strTimeStop));
		if (($tmpHourOff < $tmpHourOn) or (($tmpHourOff == $tmpHourOn) and ($tmpMinuteOff < $tmpMinuteOn))) {
			$this->dateStartOff = date("Y-m-d H:i:s", strtotime("+1 day", strtotime(($this->strDateStart)." ".($this->strTimeStop))));
		} else {
			$this->dateStartOff = date("Y-m-d H:i:s", strtotime(($this->strDateStart)." ".($this->strTimeStop)));
		}
		
		if (!$this->strDateStop) {
			$this->dateStop = 0;
		} else {
			$this->dateStop = 1;
			$this->dateStopOn = date("Y-m-d H:i:s", strtotime(($this->strDateStop)." ".($this->strTimeStart)));
			if (($tmpHourOff < $tmpHourOn) or (($tmpHourOff == $tmpHourOn) and ($tmpMinuteOff < $tmpMinuteOn))) {
				$this->dateStopOff = date("Y-m-d H:i:s", strtotime("+1 day", strtotime(($this->strDateStop)." ".($this->strTimeStop))));
			} else {
				$this->dateStopOff = date("Y-m-d H:i:s", strtotime(($this->strDateStop)." ".($this->strTimeStop)));
			}
		}
		
		switch ($this->duration) {
			case 'einmalig':
				$this->intervalNumber = 0;
				$this->intervalUnit = '';
				$this->weeklyMonday = 0;
				$this->weeklyTuesday = 0;
				$this->weeklyWednesday = 0;
				$this->weeklyThursday = 0;
				$this->weeklyFriday = 0;
				$this->weeklySaturday = 0;
				$this->weeklySunday = 0;
				break;
			case 'intervall':
				if (!$this->intervalNumber) {$this->intervalNumber = 0;}
				$this->weeklyMonday = 0;
				$this->weeklyTuesday = 0;
				$this->weeklyWednesday = 0;
				$this->weeklyThursday = 0;
				$this->weeklyFriday = 0;
				$this->weeklySaturday = 0;
				$this->weeklySunday = 0;
				break;
			case 'wochentag':
				$this->intervalNumber = 0;
				$this->intervalUnit = '';
				if ($this->weeklyMonday == true) {$this->weeklyMonday=1;
					} else {$this->weeklyMonday=0;}
				if ($this->weeklyTuesday == true) {$this->weeklyTuesday=1;
					} else {$this->weeklyTuesday=0;}
				if ($this->weeklyWednesday==true) {$this->weeklyWednesday=1;
					} else {$this->weeklyWednesday=0;}
				if ($this->weeklyThursday==true) {$this->weeklyThursday=1;
					} else {$this->weeklyThursday=0;}
				if ($this->weeklyFriday==true) {$this->weeklyFriday=1;
					} else {$this->weeklyFriday=0;}
				if ($this->weeklySaturday==true) {$this->weeklySaturday=1;
					} else {$this->weeklySaturday=0;}
				if ($this->weeklySunday==true) {$this->weeklySunday=1;
					} else {$this->weeklySunday=0;}
				break;
		}
		
		$this->title = filter_var($this->title);
		if ($stmt->execute(array($this->title, 
								 $this->switchesID, 
								 $this->dateStartOn, 
								 $this->dateStartOff, 
								 $this->dateStop, 
								 $this->dateStopOn, 
								 $this->dateStopOff, 
								 $this->duration, 
								 $this->intervalNumber, 
								 $this->intervalUnit, 
								 $this->weeklyMonday, 
								 $this->weeklyTuesday, 
								 $this->weeklyWednesday, 
								 $this->weeklyThursday, 
								 $this->weeklyFriday, 
								 $this->weeklySaturday, 
								 $this->weeklySunday))){

			$stmt = $this->conn->prepare( "SELECT id FROM schedulers ORDER BY id DESC LIMIT 1" );
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row['id'];
			
		} else {
			
			return false;
		}
	
	}


	function readAll($page, $from_record_num, $records_per_page){
		$query = "SELECT 
			schedulers.id AS scheduler_id, 
			schedulers.title AS scheduler_title, 
			schedulers.switches_id,
			schedulers.date_start_on,
			schedulers.date_start_off,
			schedulers.date_stop,
			schedulers.date_stop_on,
			schedulers.date_stop_off,
			schedulers.duration,
			schedulers.interval_number,
			schedulers.interval_unit,
			schedulers.weekly_monday,
			schedulers.weekly_tuesday,
			schedulers.weekly_wednesday,
			schedulers.weekly_thursday,
			schedulers.weekly_friday,
			schedulers.weekly_saturday,
			schedulers.weekly_sunday,
			switches.title AS switch_title,
			switch_types.icon AS switch_icon
			FROM schedulers, switches, switch_types
			WHERE switches.id = schedulers.switches_id AND switches.switch_types_id = switch_types.id
			ORDER BY schedulers.title ASC LIMIT {$from_record_num}, {$records_per_page}";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		return $stmt;
	}


	function scheduled_events_list($vorschau){
		$query = "SELECT 
			schedulers.title AS scheduler_title, 
			schedulers.switches_id,
			schedulers.date_start_on,
			schedulers.date_start_off,
			schedulers.date_stop,
			schedulers.date_stop_on,
			schedulers.date_stop_off,
			schedulers.duration,
			schedulers.interval_number,
			schedulers.interval_unit,
			schedulers.weekly_monday,
			schedulers.weekly_tuesday,
			schedulers.weekly_wednesday,
			schedulers.weekly_thursday,
			schedulers.weekly_friday,
			schedulers.weekly_saturday,
			schedulers.weekly_sunday,
			switch_types.icon AS switch_icon
			FROM schedulers, switches, switch_types
			WHERE switches.id = schedulers.switches_id AND switches.switch_types_id = switch_types.id";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
	
		$index = 0;
		date_default_timezone_set('Europe/Berlin');
		$datum_now = new DateTime();
		$datum_vorschau = new DateTime();
		$datum_vorschau->modify($vorschau);
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			switch ($row['duration']) {
				
				case "einmalig":
					$datum_scheduler = new DateTime($row['date_start_on']);
					if (($datum_scheduler >= $datum_now) and ($datum_scheduler <= $datum_vorschau)) {
						$this->scheduled_events_enter($index, $datum_scheduler, $row['scheduler_title'], $row['icon'], "on");
					}
					$datum_scheduler = new DateTime($row['date_start_off']);
					if (($datum_scheduler >= $datum_now) and ($datum_scheduler <= $datum_vorschau)){
						$this->scheduled_events_enter($index, $datum_scheduler, $row['scheduler_title'], $row['icon'], "off");
					}
					break;
					
				case "intervall":
					$aUnits =  array('Minuten' => 'minutes', 'Stunden' => 'hours', 'Tage' => 'days', 'Wochen' => 'weeks');	
					$time_to_add = '+ ' . $row['interval_number'] . ' ' . $aUnits[$row['interval_unit']];  
					
					$datum_scheduler = new DateTime($row['date_start_on']);
					$datum_scheduler_stop = new DateTime($row['date_stop_on']);
					while ($datum_scheduler < $datum_now) {
						$datum_scheduler->modify($time_to_add);
					}
					while ($datum_scheduler <= $datum_vorschau) {
						if ((($row['date_stop']) and ($datum_scheduler <= $datum_scheduler_stop)) or ($row['date_stop']<>true)){
							$this->scheduled_events_enter($index, $datum_scheduler, $row['scheduler_title'], $row['icon'], "on");
						}
						$datum_scheduler->modify($time_to_add);
					}
					
					$datum_scheduler = new DateTime($row['date_start_off']);
					$datum_scheduler_stop = new DateTime($row['date_stop_off']);
					while ($datum_scheduler < $datum_now) {
						$datum_scheduler->modify($time_to_add);
					}
					while ($datum_scheduler <= $datum_vorschau) {
						if ((($row['date_stop']) and ($datum_scheduler <= $datum_scheduler_stop)) or ($row['date_stop']<>true)){
							$this->scheduled_events_enter ($index, $datum_scheduler, $row['scheduler_title'], $row['icon'], "off");
						}
						$datum_scheduler->modify($time_to_add);
					}
					break;
					
				case "wochentag":
					$datum_scheduler = new DateTime($row['date_start_on']);
					$wochentag_start = $datum_scheduler->format('w');
					$aWeekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');  
					foreach($aWeekdays as $weekday) {
						if ($row['weekly_'.$weekday]) {
							$this->scheduled_events_weekdays($datum_now, $datum_vorschau, $index, $row['date_start_on'],$row['date_stop_on'],$row['date_stop'],$this->calcDaysDiff($datum_scheduler, $weekday),$row['scheduler_title'], $row['icon'], "on");
							$this->scheduled_events_weekdays($datum_now, $datum_vorschau, $index, $row['date_start_off'],$row['date_stop_off'],$row['date_stop'],$this->calcDaysDiff($datum_scheduler, $weekday),$row['scheduler_title'], $row['icon'], "off");
						}
					}  
					break;

			}
		}
	
		foreach ($this->events as $nr => $inhalt)
		{
			$datum[$nr]  = strtolower( $inhalt['sort'] );
			$scheduler_title[$nr]   = strtolower( $inhalt['scheduler_title'] );
		}
		array_multisort($datum, SORT_ASC, $this->events);
		
		return $this->events;
		
	}


	function scheduled_events_weekdays($now, $vorschau, &$index, $date_on, $date_off, $stop, $days, $title, $icon, $status) {
		$datum_scheduler = new DateTime($date_on);
		$datum_scheduler_stop = new DateTime($date_off);
		$datum_scheduler->modify("+".$days." days");
		while (($datum_scheduler) < $now) {
			$datum_scheduler->modify("+7 days");
		}
		while ($datum_scheduler <= $vorschau) {
			if ((($stop) and ($datum_scheduler <= $datum_scheduler_stop)) or ($stop<>true)){
				$this->scheduled_events_enter($index, $datum_scheduler, $title, $icon, $status);
			}
			$datum_scheduler->modify("+7 days");
		}
	}
	
	
	function scheduled_events_enter(&$index, $datum, $title, $icon, $status) {
		$index++;
        if (get_lang_id()=="en") {
            $this->events[$index]['datum'] = $datum->format("m-d-Y");
        } else {
            $this->events[$index]['datum'] = $datum->format("d.m.Y");
        }
        $this->events[$index]['sort'] = $datum->format("YYYY-mm-dd-HH-ii-ss");
		$this->events[$index]['wochentag'] = $datum->format("w");
		$this->events[$index]['uhrzeit'] = $datum->format("H:i");
		$this->events[$index]['scheduler_title'] = $title;
		$this->events[$index]['status'] = $status;
	}


	function calcDaysDiff($oDateTime, $nextWeekdayName) {
		$oDateTime2 = clone $oDateTime;
		$oDateTime3 = clone $oDateTime;
		$oDateTime2->modify('next '.$nextWeekdayName.' '.$oDateTime3->format('H:i:s'));
		$diffDays = $oDateTime->diff($oDateTime2)->days;
		return ($diffDays == 7) ? 0 : $diffDays; 
	}


	public function countAll(){
		$stmt = $this->conn->prepare( "SELECT id FROM schedulers" );
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}


	function readSwitches(){
		$stmt = $this->conn->prepare( "SELECT id, title FROM switches ORDER BY title ASC" );
		$stmt->execute();
		return $stmt;
	}


	function readOne(){
		$query = "SELECT 
			schedulers.title AS scheduler_title, 
			schedulers.switches_id,
			schedulers.date_start_on,
			schedulers.date_start_off,
			schedulers.date_stop,
			schedulers.date_stop_on,
			schedulers.date_stop_off,
			schedulers.duration,
			schedulers.interval_number,
			schedulers.interval_unit,
			schedulers.weekly_monday,
			schedulers.weekly_tuesday,
			schedulers.weekly_wednesday,
			schedulers.weekly_thursday,
			schedulers.weekly_friday,
			schedulers.weekly_saturday,
			schedulers.weekly_sunday,
			switches.title AS switch_title
			FROM schedulers, switches 
			WHERE switches.id = schedulers.switches_id 
			AND schedulers.id = ? LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->execute(array($this->id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$this->title = $row['scheduler_title'];
		$this->switchesID = $row['switches_id'];
		$this->switchCommand = $row['switch_title'];
		$this->dateStartOn = $row['date_start_on'];
		$this->dateStartOff = $row['date_start_off'];
		$this->dateStop = $row['date_stop'];
		$this->dateStopOn = $row['date_stop_on'];
		$this->dateStopOff = $row['date_stop_off'];
		$this->duration = $row['duration'];
		$this->intervalNumber = $row['interval_number'];
		$this->intervalUnit = $row['interval_unit'];
		$this->weeklyMonday = $row['weekly_monday'];
		$this->weeklyTuesday = $row['weekly_tuesday'];
		$this->weeklyWednesday = $row['weekly_wednesday'];
		$this->weeklyThursday = $row['weekly_thursday'];
		$this->weeklyFriday = $row['weekly_friday'];
		$this->weeklySaturday = $row['weekly_saturday'];
		$this->weeklySunday = $row['weekly_sunday'];
		$this->strDateStart = date("d.m.Y", strtotime($this->dateStartOn));
		$this->strTimeStart = date("H:i", strtotime($this->dateStartOn));
		$this->strTimeStop = date("H:i", strtotime($this->dateStartOff));
		if ($this->dateStop) {
			$this->strDateStop = date("d.m.Y", strtotime($this->dateStopOn));
		}
	}


	function update(){ 
		$stmt = $this->conn->prepare( "SELECT id FROM switches WHERE title = ? " );
		$stmt->execute(array($this->switchCommand));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->switchesID = $row['id'];

		$query = "UPDATE schedulers	SET 
				 title = :title, 
				 switches_id = :switchesID, 
				 date_start_on = :dateStartOn, 
				 date_start_off = :dateStartOff,
				 date_stop = :dateStop,
				 date_stop_on = :dateStopOn,
				 date_stop_off = :dateStopOff,
				 duration = :duration,
				 interval_number = :intervalNumber,
				 interval_unit = :intervalUnit,
				 weekly_monday = :weeklyMonday,
				 weekly_tuesday = :weeklyTuesday,
				 weekly_wednesday = :weeklyWednesday,
				 weekly_thursday = :weeklyThursday,
				 weekly_friday = :weeklyFriday,
				 weekly_saturday = :weeklySaturday,
				 weekly_sunday = :weeklySunday
				WHERE
				 id = :id";
		$stmt = $this->conn->prepare($query);
		
		$this->dateStartOn = date("Y-m-d H:i:s", strtotime(($this->strDateStart)." ".($this->strTimeStart)));
		$tmpHourOn = date("H", strtotime($this->strTimeStart));
		$tmpHourOff = date("H", strtotime($this->strTimeStop));
		$tmpMinuteOn = date("i", strtotime($this->strTimeStart));
		$tmpMinuteOff = date("i", strtotime($this->strTimeStop));
		if (($tmpHourOff < $tmpHourOn) or (($tmpHourOff == $tmpHourOn) and ($tmpMinuteOff < $tmpMinuteOn))) {
			$this->dateStartOff = date("Y-m-d H:i:s", strtotime("+1 day", strtotime(($this->strDateStart)." ".($this->strTimeStop))));
		} else {
			$this->dateStartOff = date("Y-m-d H:i:s", strtotime(($this->strDateStart)." ".($this->strTimeStop)));
		}
		if (!$this->strDateStop) {
			$this->dateStop = 0;
		} else {
			$this->dateStop = 1;
			$this->dateStopOn = date("Y-m-d H:i:s", strtotime(($this->strDateStop)." ".($this->strTimeStart)));
			if (($tmpHourOff < $tmpHourOn) or (($tmpHourOff == $tmpHourOn) and ($tmpMinuteOff < $tmpMinuteOn))) {
				$this->dateStopOff = date("Y-m-d H:i:s", strtotime("+1 day", strtotime(($this->strDateStop)." ".($this->strTimeStop))));
			} else {
				$this->dateStopOff = date("Y-m-d H:i:s", strtotime(($this->strDateStop)." ".($this->strTimeStop)));
			}
		}

		switch ($this->duration) {
			case 'einmalig':
				$this->intervalNumber = 0;
				$this->intervalUnit = '';
				$this->weeklyMonday = 0;
				$this->weeklyTuesday = 0;
				$this->weeklyWednesday = 0;
				$this->weeklyThursday = 0;
				$this->weeklyFriday = 0;
				$this->weeklySaturday = 0;
				$this->weeklySunday = 0;
				break;
			case 'intervall':
				if (!$this->intervalNumber) {$this->intervalNumber = 0;}
				$this->weeklyMonday = 0;
				$this->weeklyTuesday = 0;
				$this->weeklyWednesday = 0;
				$this->weeklyThursday = 0;
				$this->weeklyFriday = 0;
				$this->weeklySaturday = 0;
				$this->weeklySunday = 0;
				break;
			case 'wochentag':
				$this->intervalNumber = 0;
				$this->intervalUnit = '';
				if ($this->weeklyMonday == true) {$this->weeklyMonday=1;
					} else {$this->weeklyMonday=0;}
				if ($this->weeklyTuesday == true) {$this->weeklyTuesday=1;
					} else {$this->weeklyTuesday=0;}
				if ($this->weeklyWednesday==true) {$this->weeklyWednesday=1;
					} else {$this->weeklyWednesday=0;}
				if ($this->weeklyThursday==true) {$this->weeklyThursday=1;
					} else {$this->weeklyThursday=0;}
				if ($this->weeklyFriday==true) {$this->weeklyFriday=1;
					} else {$this->weeklyFriday=0;}
				if ($this->weeklySaturday==true) {$this->weeklySaturday=1;
					} else {$this->weeklySaturday=0;}
				if ($this->weeklySunday==true) {$this->weeklySunday=1;
					} else {$this->weeklySunday=0;}
				break;
		}
		
		$this->title = filter_var($this->title);

		if($stmt->execute(array('title' => $this->title, 
								'switchesID' => $this->switchesID, 
								'dateStartOn' => $this->dateStartOn, 
								'dateStartOff' => $this->dateStartOff, 
								'dateStop' => $this->dateStop, 
								'dateStopOn' => $this->dateStopOn, 
								'dateStopOff' => $this->dateStopOff, 
								'duration' => $this->duration, 
								'intervalNumber' => $this->intervalNumber, 
								'intervalUnit' => $this->intervalUnit, 
								'weeklyMonday' => $this->weeklyMonday, 
								'weeklyTuesday' => $this->weeklyTuesday, 
								'weeklyWednesday' => $this->weeklyWednesday, 
								'weeklyThursday' => $this->weeklyThursday, 
								'weeklyFriday' => $this->weeklyFriday, 
								'weeklySaturday' => $this->weeklySaturday, 
								'weeklySunday' => $this->weeklySunday, 
								'id' => $this->id))){
			return true;
		}else{
			return false;
		}
	}


	function delete(){
		$stmt = $this->conn->prepare( "DELETE FROM schedulers WHERE id = ?" );
  		if($result = $stmt->execute(array($this->id))){
   			return true;
  		}else{
   			return false;
  		}
 	}
	
}
?>
