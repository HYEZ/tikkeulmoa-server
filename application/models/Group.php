<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Model {

	function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

   	/* 모임생성(통장개설) */
   	public function insert($argu) {   
   		$this->error_log("[models/Group/insert] ENTER");	
   		if(empty($argu['user_idx']) || empty($argu['pw']) || empty($argu['name'])) {
	        return array(
	          'status' => API_FAILURE, 
	          'message' => 'Fail'
	        );
	    } else {
			$photo_url = $this->file_upload($argu['photo']);

			$this->db->set('name', $argu['name']);
			$this->db->set('pw', $argu['pw']);
			$this->db->set('master_idx', $argu['user_idx']);
			$this->db->set('photo_url', $photo_url);
			$this->db->set('date', date("Y-m-d H:i:s"));
			$this->db->insert("groups");
			
			$this->error_log("[models/Group/insert] EXIT");

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success'
			);
		}	
   	}

   	/* 파일 업로드 */
   	public function file_upload($file) {
   		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/photo';
		$tmp_name = $file["tmp_name"];
		$name = date("YmdHis").'_'.$file["name"];
		move_uploaded_file($tmp_name , "$uploadDir/$name");
		return "/upload/photo/".$name;
   	}


   	/* 모임 정보 */
   	public function get($argu) {
      $this->error_log("[models/Group/get] ENTER");
      if(empty($argu['groups_idx'])) {
        return array(
          'status' => API_FAILURE, 
          'message' => 'Fail',
          'data' => null
        );
      } else {
        $this->db->where('idx', $argu['groups_idx']);
        $this->db->select("idx, name, master_idx, price, photo_url");
        $this->db->from("groups");
        $result = $this->db->get();
        if($result->num_rows()) {
          $res = $result->result()[0];
          return array(
            'status' => API_SUCCESS, 
            'message' => 'Success',
            'data' => $res
          );
        }
        return array(
          'status' => API_FAILURE, 
          'message' => 'Fail',
          'data' => null
        );
      }
    }

    /* 모임 리스트 */
    public function list_search($argu) {
    	$query = "select idx, name, master_idx, price, photo_url from groups where idx in (select groups_idx from user_groups where user_idx=".$argu['user_idx'].")";

    	$result = $this->db->query($query);

        $data = [];
        if($result->num_rows()) {
        	foreach( $result->result() as $row )
	        {
		        array_push($data, $row);
	        }	 

	        return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => $data
			);
        } else {
        	return array(
	          'status' => API_FAILURE, 
	          'message' => 'Fail',
	          'data' => null
	        );
        }
    }


    /* 로그 */
    public function error_log($msg)
    {
		$log_filename = "{$_SERVER['DOCUMENT_ROOT']}/logs/error_log";
		$now        = getdate();
		$today      = $now['year']."/".$now['mon']."/".$now['mday'];
		$now_time   = $now['hours'].":".$now['minutes'].":".$now['seconds'];
		$now        = $today." ".$now_time;
		$filep = fopen($log_filename, "a");
		if(!$filep) {
		die("can't open log file : ". $log_filename);
		}
		fputs($filep, "{$now} : {$msg}\n\r");
		fclose($filep);
    }

}