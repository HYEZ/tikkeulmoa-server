<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
   	}

    /* User Login */
    public function login($argu) {
      if(empty($argu['id']) || empty($argu['pw'])) {
        return array(
          'status' => API_FAILURE, 
          'message' => '로그인 실패',
          'data' => null
        );
      } else {
        $this->error_log("[models/User/login] ENTER");

        $this->error_log($argu['id']);
        $this->error_log($argu['pw']);

        $this->db->where('id', $argu['id']);
        $this->db->where('pw', $argu['pw']);
        $this->db->select("*");
        $this->db->from("user");
        $result = $this->db->get();
        
        $data = null;

        if( $result->num_rows()) {
          foreach( $result->result() as $row )
          {
            $data = (int)$row->idx;
          }
        
          return array(
            'status' => API_SUCCESS, 
            'message' => '로그인 성공',
            'idx' => $data
          );
        } else {
          return array(
            'status' => 433, 
            'message' => '존재하지 않는 아이디 또는 패스워드입니다.',
            'idx' => $data
          );
        }
        
      }
    }

    public function get($argu) {
      $this->error_log("[models/User/get] ENTER");
      if(empty($argu['user_idx']) && empty($argu['id'])) {
        return array(
          'status' => API_FAILURE, 
          'message' => 'Fail',
          'data' => null
        );
      } else {
        if(isset($argu['user_idx'])) {
          $this->db->where('idx', $argu['user_idx']);
        } else {
          $this->db->where('id', $argu['id']);
        }
        
        $this->db->select("idx, id, name, photo_url");
        $this->db->from("user");
        $result = $this->db->get();
        
        if($result->num_rows()) {
          $res = $result->result()[0];
          $res->idx = (int)$res->idx;
          return array(
            'status' => API_SUCCESS, 
            'message' => '로그인 성공',
            'data' => $res
          );
        }
        return array(
          'status' => 433, 
          'message' => '존재하지 않는 사용자입니다.',
          'data' => null
        );
      }
    }
    
    private function check_id($argu) {
      $this->db->where('id', $argu['id']);
      $this->db->select("*");
      $this->db->from("user");
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