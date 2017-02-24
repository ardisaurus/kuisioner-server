<?php
require APPPATH . '/libraries/REST_Controller.php';
class kuisioner extends REST_Controller {

	function user_get(){
		$get_kuisioner = $this->db->query("SELECT p.id_kuisioner, p.nama_kuisioner, p.publish FROM kuisioner as p WHERE p.publish=1")->result();
		$this->response(array("status"=>"success","result" => $get_kuisioner));
	}

	function user_post() {
		$action = $this->post('action');
		$data_kuisioner = array(
		'id_kuisioner' => $this->post('id_kuisioner'),
		'id_pertanyaan' => $this->post('id_pertanyaan'),
		'jawaban' => $this->post('jawaban'),
		'username' => $this->post('username')
		);
		if ($action==='postjawaban'){
			$this->insertjawaban($data_kuisioner);
		}else if ($action==='getid'){
			$this->idkuisioner($data_kuisioner);
		}else if ($action==='getuserkuisioner'){
			$this->userkuisioner($data_kuisioner);
		}else if ($action==='getuserpertanyaan'){
			$this->userpertanyaan($data_kuisioner);
		}else{
			$this->response(array("status"=>"failed","message" => "action harus diisi"));
		}
	}

	function idkuisioner($data_kuisioner){
		if (empty($data_kuisioner['id_kuisioner'])){
			$this->response(array('status' => "failed", "message"=>"Id kuisioner harus diisi"));
		}else{
			$this->db->where('id_kuisioner', $data_kuisioner['id_kuisioner']);
            $get_kuisioner = $this->db->get('pertanyaan')->result();        
			$this->response(array("status"=>"success","result" => $get_kuisioner));
		}
	}

	function userkuisioner($data_kuisioner){
		if (empty($data_kuisioner['username'])){
			$this->response(array('status' => "failed", "message"=>"Username kuisioner harus diisi"));
		}else{
			$get_kuisioner = $this->db->query("SELECT p.id_kuisioner, p.nama_kuisioner, p.publish FROM kuisioner as p WHERE p.publish=1")->result();
			$this->response(array("status"=>"success","result" => $get_kuisioner));
		}
	}

	function userpertanyaan($data_kuisioner){
		if (empty($data_kuisioner['username'])){
			$this->response(array('status' => "failed", "message"=>"Username kuisioner harus diisi"));
		}else if(empty($data_kuisioner['id_kuisioner'])){
			$this->response(array('status' => "failed", "message"=>"id_kuisioner harus diisi"));		
		}else{
			$get_kuisioner = $this->db->query("SELECT `pertanyaan`.`id_kuisioner`, `pertanyaan`.`id_pertanyaan`, `pertanyaan`.`pertanyaan`, 1 as `status` FROM `pertanyaan` JOIN `jawaban` WHERE `jawaban`.`id_pertanyaan`=`pertanyaan`.`id_pertanyaan` AND `pertanyaan`.id_kuisioner=".$data_kuisioner['id_kuisioner']."
				UNION
				SELECT `pertanyaan`.`id_kuisioner`, `pertanyaan`.`id_pertanyaan`, `pertanyaan`.`pertanyaan`, 0 as `status` FROM `pertanyaan` JOIN `jawaban` WHERE `jawaban`.`id_pertanyaan`!=`pertanyaan`.`id_pertanyaan` AND `pertanyaan`.id_kuisioner=".$data_kuisioner['id_kuisioner'])->result();
			$this->response(array("status"=>"success","result" => $get_kuisioner));
		}
	}

	function insertjawaban($data_jawaban){		
		//cek validasi
		if (empty($data_jawaban['username'])){
			$this->response(array('status' => "failed", "message"=>"username harus diisi"));
		}else if (empty($data_jawaban['id_pertanyaan'])){
			$this->response(array('status' => "failed", "message"=>"id_pertanyaan harus diisi"));		
		}else if (empty($data_jawaban['jawaban'])){
			$this->response(array('status' => "failed", "message"=>"jawaban harus diisi"));
		}
		else{
			$get_jawaban_baseid = $this->db->query("SELECT * FROM jawaban as p WHERE p.username='".$data_jawaban['username']."' AND id_pertanyaan='".$data_jawaban['id_pertanyaan']."'")->result();
			if(empty($get_jawaban_baseid)){
				$insert= $this->db->query("INSERT INTO `jawaban` (`id_pertanyaan`, `jawaban`, `username`) VALUES ('".$data_jawaban['id_pertanyaan']."', '".$data_jawaban['jawaban']."', '".$data_jawaban['username']."')");
				if ($insert){
					$this->response(array('status'=>'success','result' => array($data_jawaban),"message"=>$insert));					
				}
			}else{
				//jika photo di kosong atau tidak di update eksekusi query
				$update= $this->db->query("Update jawaban Set jawaban ='".$data_jawaban['jawaban']."' Where id_pertanyaan ='".$data_jawaban['id_pertanyaan']."' AND username ='".$data_jawaban['username']."'");
				if ($update){
					$this->response(array('status'=>'success','result' => array($data_jawaban),"message"=>$update));
				}
			}
		}
	}
}