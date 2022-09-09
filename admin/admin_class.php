<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function login2(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = 3";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		if(!empty($_FILES['img']['tmp_name'])){
			$fname = strtotime(date("Y-m-d H:i"))."_".$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			if($move){
				$data .=", img_path = '$fname' ";
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO church_specialty set ".$data);
		}else{
			$save = $this->db->query("UPDATE church_specialty set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM church_specialty where id = ".$id);
		if($delete)
			return 1;
	}
	function c(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", name_pref = '$name_pref' ";
		$data .= ", church_address = '$church_address' ";
		$data .= ", contact = '$contact' ";
		$data .= ", email = '$email' ";
		if(!empty($_FILES['img']['tmp_name'])){
			$fname = strtotime(date("Y-m-d H:i"))."_".$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			if($move){
				$data .=", img_path = '$fname' ";
			}
		}
		$data .=" , specialty_ids = '[".implode(",",$specialty_ids)."]' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO priest_list set ".$data);
			$did= $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE priest_list set ".$data." where id=".$id);
		}
		if($save){
			$data = " username = '$email' ";
			if(!empty($password))
			$data .= ", password = '".$password."' ";
			$data .= ", name = 'DR.".$name.', '.$name_pref."' ";
			$data .= ", contact = '$contact' ";
			$data .= ", address = '$church_address' ";
			$data .= ", type = 2";
			if(empty($id)){
				$chk = $this->db->query("SELECT * FROM users where username = '$email ")->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
					$data .= ", priest_id = '$did'";

					$save = $this->db->query("INSERT INTO users set ".$data);
			}else{
				$chk = $this->db->query("SELECT * FROM users where username = '$email' and priest_id != ".$id)->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
					$data .= ", priest_id = '$id'";
				$chk2 = $this->db->query("SELECT * FROM users where priest_id = ".$id)->num_rows;
					if($chk2 > 0)
						$save = $this->db->query("UPDATE users set ".$data." where priest_id = ".$id);
					else
						$save = $this->db->query("INSERT INTO users set ".$data);
					

			}
			return 1;
		}
	}
	function delete_doctor(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM priest_list where id = ".$id);
		if($delete)
			return 1;
	}

	function save_schedule(){
		extract($_POST);
		foreach($days as $k => $val){
			$data = " priest_id = '$priest_id' ";
			$data .= ", day = '$days[$k]' ";
			$data .= ", time_from = '$time_from[$k]' ";
			$data .= ", time_to = '$time_to[$k]' ";
			if(isset($check[$k])){
				if($check[$k]>0)
				$save[] = $this->db->query("UPDATE priest_schedule set ".$data." where id =".$check[$k]);
			else
				$save[] = $this->db->query("INSERT INTO priest_schedule set ".$data);
			}
		}

			if(isset($save)){
				return 1;
			}
	}

	function set_appointment(){
		extract($_POST);
		$doc = $this->db->query("SELECT * FROM priest_list where id = ".$priest_id);
		// $event = $this->db->query("SELECT * FROM church_specialty where id = ".$event_id);
		$schedule = date('Y-m-d',strtotime($date)).' '.date('H:i',strtotime($time)).":00";
		$day = date('l',strtotime($date));
		$time = date('H:i',strtotime($time)).":00";
		$sched = date('H:i',strtotime($time));
		$doc_sched_check = $this->db->query("SELECT * FROM priest_schedule where priest_id = $priest_id and day = '$day' and ('$time' BETWEEN time_from and time_to )");
		if($doc_sched_check->num_rows <= 0){
			return json_encode(array('status'=>2,"msg"=>"Appointment schedule not valid for selected priest's schedule."));
			exit;
		}

		$data = " priest_id = '$priest_id' ";
		if(!isset($client_id))
		$data .= ", client_id = '".$_SESSION['login_id']."' ";
		else
		$data .= ", client_id = '$client_id' ";

		$data .= ", schedule = '$schedule' ";
	
		$data .= ", event_id = '$event_id' ";
		if(isset($status))
		$data .= ", status = '$status' ";
		if(isset($id) && !empty($id))
		$save = $this->db->query("UPDATE appointment_list set ".$data." where id = ".$id);
		else
		$save = $this->db->query("INSERT INTO appointment_list set ".$data);
		if($save){
			return json_encode(array('status'=>1));
		}
	}
	function delete_appointment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM appointment_list where id = ".$id);
		if($delete)
			return 1;
	}
}