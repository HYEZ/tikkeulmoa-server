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
			$acount_number = $this->acount_number();
			$this->db->set('name', $argu['name']);

			$this->db->set('acount_number', $acount_number);
			$this->db->set('pw', $argu['pw']);
			$this->db->set('master_idx', $argu['user_idx']);
			$this->db->set('photo_url', $photo_url);
			$this->db->set('date', date("Y-m-d H:i:s"));
			$this->db->insert("groups");
			
			$temp = array(
				'groups_idx' => $this->db->insert_id(),
				'user_idx' => $argu['user_idx'],
				'is_master' => 1
			);
			$this->insert_member($temp);

			$this->error_log("[models/Group/insert] EXIT");

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success'
			);
		}	
   	}

   	public function acount_number() {
   		$first = "1000";
   		$second = "12";
   		$third = (string)mt_rand(1, 999999);
   		if(strlen($third) < 6) {
   			$temp = "";
   			for($i = 0; $i < 6-strlen($third); $i++) {
   				$temp = $temp."0";
   			}
   			$third = $temp.$third;
   		}
   		return $first."-".$second."-".$third;
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
          $res->idx = (int)$res->idx;
          $res->master_idx = (int)$res->master_idx;
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

    /* 커뮤니티 유무 */
   	public function exist_community($idx) {
		$query = "select * from community where groups_idx=".$idx."";

		$result = $this->db->query($query);
		return $result->num_rows() > 0 ? 1 : 0;
        // return array(
        //   'status' => API_SUCCESS, 
        //   'message' => 'Success',
        //   'exist' => $result->num_rows() > 0 ? 1 : 0
        // );
    }

    /* 모임 리스트 */
    public function list_search($argu) {
    	$query = "select idx, name, master_idx, price, photo_url, acount_number from groups where idx in (select groups_idx from user_groups where user_idx=".$argu['user_idx'].")";

    	$result = $this->db->query($query);

        $data = [];
        if($result->num_rows()) {
        	foreach( $result->result() as $row )
	        {
	        	$temp = array(
	        		'idx' => (int)$row->idx,
	        		'name' => $row->name,
	        		'master_idx' => (int)$row->master_idx,
	        		'price' => $row->price,
	        		'photo_url' => $row->photo_url,
	        		'acount_number' => $row->acount_number,
	        		'exist' => $this->exist_community($row->idx)
	        	);
		        array_push($data, $temp);
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

    /* 모임원 추가 */
    public function insert_member($argu) {
    	$this->error_log("[models/Group/insert] ENTER");	
   		if(empty($argu['user_idx']) || empty($argu['groups_idx'])) {
	        return array(
	          'status' => API_FAILURE, 
	          'message' => 'Fail'
	        );
	    } else {
			$this->db->set('user_idx', $argu['user_idx']);
			$this->db->set('groups_idx', $argu['groups_idx']);
			$this->db->set('is_master', $argu['is_master']);
			$this->db->insert("user_groups");
			
			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success'
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