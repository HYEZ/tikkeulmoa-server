<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Measure extends CI_Model {

	function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

   	/* 측정하기 */
   	public function insert($argu) {
   		error_reporting(0);
   		if(0) {

			return array(
				'status' => API_FAILURE, 
				'message' => 'Fail',
				'data' => null
			);
   		} else {
   			$this->error_log("[models/Measure/insert] ENTER");

   			$weather = $this->get_weather();
   			// file upload
   			$file = $argu['video'];
   			$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/video';
   			$tmp_name = $file["tmp_name"];
			$name = date("YmdHis").'_'.$file["name"];
			move_uploaded_file($tmp_name , "$uploadDir/$name");


   			$this->db->set('period', $argu['period']);


			$this->error_log("1");

			$this->db->set('hb', 0);
			$this->error_log("1");

			$this->db->set('user_idx', $argu['user_idx']);
			$this->error_log("2");

			$this->db->set('date', date("y/m/d"));
			$this->error_log("3");

			$this->db->set('temperature', $weather['temp']);
			$this->error_log("4");

			$this->db->set('humidity', $weather['reh']);
			$this->error_log("5");


			$this->error_log('period:'.$argu['period']);
			$this->error_log('user_idx:'.$argu['user_idx']);
			$this->error_log('user_idx:'. $weather['temp']);
			$this->error_log('user_idx:'.$weather['reh']);


			$this->db->insert("measure");

			$this->error_log("6");




			$this->error_log("[models/Measure/insert] EXIT");

			$data = array(
				'hb' => 0,
				'date' => date("y/m/d")
			);

			return array(
				'status' => API_SUCCESS, 
				'message' => 'Success',
				'data' => $data
			);
   		}
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

    /* 측정하기 버튼 클릭 */
    public function flag($argu) {
      $this->error_log("[models/Measure/flag] ENTER");
      if(empty($argu['user_idx'])) {
        return array(
			'status' => API_FAILURE, 
			'message' => 'Fail'        
        );
      } else {
        
		$this->error_log($argu['user_idx']);
		$this->error_log($argu['flag']);

		if(!$this->check_flag($argu)) {
			$this->db->set('user_idx', $argu['user_idx']);
			$this->db->set('flag', $argu['flag']);
			$this->db->insert("measure_flag");
		} else {
			$this->db->set('flag', $argu['flag']);
			$this->db->where('user_idx', $argu['user_idx']);
			$this->db->update("measure_flag");
		}

		return array(
			'status' => API_SUCCESS, 
			'message' => 'SUCCESS'
		);
        
        
      }
    }

    /* 측정한 경험이 있는지 */
    private function check_flag($argu) {
		$this->db->where('user_idx', $argu['user_idx']);
		$this->db->select("*");
		$this->db->from("measure_flag");
		$result = $this->db->get();
		return $result->num_rows();
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