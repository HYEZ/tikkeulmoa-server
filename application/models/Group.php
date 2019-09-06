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


    /* 측정 리스트 */
    public function list_search($argu) {
    	$this->db->where('user_idx', $argu['user_idx']);
        $this->db->select("*");
        $this->db->from("measure");
        $this->db->order_by("idx", "desc");
        $result = $this->db->get();
        $data = [];
        if($result->num_rows()) {
        	foreach( $result->result() as $row )
	        {
	        	$temp = array(
	        		'idx' => $row->idx,
	        		'hb' => $row->hb,
	        		'period' => $row->period,
	        		'date' => $row->date
	        	);
	        	array_push($data, $temp);
	        }
	        return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => $data,
				'dataNum' => $result->num_rows()
			);
        } else {
        	return array(
				'status' => 204, 
				'message' => '측정결과가 존재하지 않습니다.',
				'data' => $data
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