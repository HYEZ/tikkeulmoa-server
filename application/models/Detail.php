<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends CI_Model {

	function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

   	/* 입출금 */
   	public function insert($argu) {   
   		$this->error_log("[models/Detail/insert] ENTER");	
   		if(empty($argu['user_idx']) || empty($argu['groups_idx']) || empty($argu['price']) || empty($argu['memo'])) {
	        return array(
	          'status' => API_FAILURE, 
	          'message' => 'Fail'
	        );
	    } else {
        $result = $this->db->query("select price from groups where idx=".$argu['groups_idx']);
        $data = $result->result();
        $price = $data[0]->price;
        if($argu['is_in'] == 1) {  
          $balance = $price + $argu['price'];
          // 입금
          $this->db->query("update groups set price=price+".$argu['price']." where idx=".$argu['groups_idx']);
        } else { 
          // 출금
          $balance = $price - $argu['price'];
          if($balance > 0) {
            $this->db->query("update groups set price=price-".$argu['price']." where idx=".$argu['groups_idx']);
          } else {
            return array(
              'status' => 433, 
              'message' => '잔액이 부족합니다'
            );
          }
        }

        $this->db->set('user_idx', $argu['user_idx']);
        $this->db->set('groups_idx', $argu['groups_idx']);
        $this->db->set('balance', $balance);
        $this->db->set('price', $argu['price']);
        $this->db->set('memo', $argu['memo']);
        $this->db->set('is_in', $argu['is_in']);
        $this->db->set('date', date("y.m.d"));
        $this->db->insert("details");
  			
  			$this->error_log("[models/Detail/insert] EXIT");

  			return array(
  				'status' => API_SUCCESS, 
  				'message' => 'Success'
    		);
  		}	
   	}

    /* 입출금 내역 리스트 */
    public function list_search($argu) {
      $query = "select * from details where groups_idx=".$argu['groups_idx'];

      $result = $this->db->query($query);

        $data = [];
        if($result->num_rows()) {
          foreach( $result->result() as $row )
          {

            $temp = array(
              'idx' => (int)$row->idx,
              'user_idx' => (int)$row->user_idx,
              'groups_idx' => (int)$row->groups_idx,
              'price' => $row->price,
              'balance' => $row->balance,
              'is_in' => $row->is_in,
              'memo' => $row->memo,
              'date' => $row->date
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