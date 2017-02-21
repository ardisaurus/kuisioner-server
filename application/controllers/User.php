<?php
require APPPATH . '/libraries/REST_Controller.php';
class user extends REST_Controller {

	function user_get(){          
		$get_user = $this->db->query("SELECT p.username, p.password, p.level, p.nama_pengguna FROM user as p")->result();
		$this->response(array("status"=>"success","result" => $get_user));
	}

	function user_post() {
		$action = $this->post('action');
		$data_user = array(
		'username' => $this->post('username'),
		'nama_pengguna' => $this->post('nama_pengguna'),
		'password' => md5($this->post('password'))
		);
		if ($action==='getid'){
			$this->idUser($data_user);
		}else if ($action==='password'){
			$this->updatePassword($data_user);
		}else{
			$this->response(array("status"=>"failed","message" => "action harus diisi"));
		}
	}

	function idUser($data_user){
		if (empty($data_user['username'])){
			$this->response(array('status' => "failed", "message"=>"username User harus diisi"));
		}
		else{
			$this->db->where('username', $data_user['username']);
            $get_user = $this->db->get('user')->result();
            if ($get_user) {
				$this->response(array("status"=>"success","result" => $get_user));
            }        
			$this->response(array("status"=>"failed","message"=>"username tidak ditemukan"));
		}
	}

	function updatePassword($data_user){
		if (empty($data_user['username'])){
			$this->response(array('status' => "failed", "message"=>"username User harus diisi"));
		}else if (empty($data_user['password'])){
			$this->response(array('status' => "failed", "message"=>"password harus diisi"));
		}else{
			$get_user_baseid = $this->db->query("SELECT * FROM user as p WHERE p.username='".$data_user['username']."'")->result();
			if(empty($get_user_baseid)){
				$x=$data_user['username'];
				$this->response(array('status' => "failed", "message"=>"id Tidak ada dalam database = $x"));
			}else{
				$update= $this->db->query("Update user Set password ='".$data_user['password']."' Where username ='".$data_user['username']."'");
				if ($update){
					$this->response(array('status'=>'success','result' => array($data_user),"message"=>$update));
				}
			}
		}
	}

}