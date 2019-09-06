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
        $this->error_log("모델 입성");

        $this->error_log($argu['id']);
        $this->error_log($argu['pw']);

        $this->db->where('id', $argu['id']);
        $this->db->where('pw', $argu['pw']);
        $this->db->select("*");
        $this->db->from("user");
        $result = $this->db->get();
        
        // $data = '';
        $data = null;

        // $this->error_log("모델 입성");

        if( $result->num_rows()) {
          foreach( $result->result() as $row )
          {
            $data = $row->idx;
          }
          // $data = null;
        
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

     /* User Join */
    public function insert($argu) {

      $this->error_log("모델 입성");
      if(empty($argu['id']) || empty($argu['pw']) || empty($argu['name']) || empty($argu['birth'])) {
        return array(
          'status' => API_FAILURE, 
          'message' => '회원가입 실패'        
        );
      } else {

        $this->error_log("드루왕");
        if(!$this->check_id($argu)) {
          $this->error_log($argu['id']);

          $this->db->set('id', $argu['id']);
          $this->db->set('pw', $argu['pw']);
          $this->db->set('name', $argu['name']);
          $this->db->set('gender', $argu['gender']);
          $this->db->set('birth', $argu['birth']);
          $this->db->insert("user");
          // $result = $this->db->get();


          $this->error_log("test");
        
          // $idx = $this->db->insert_id();

          return array(
            'status' => API_SUCCESS, 
            'message' => '로그인 성공'
          );
        } else {
          return array(
            'status' => API_INDEX_ERROR, 
            'message' => '이미 존재하는 ID입니다'        
          );
        }
        
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

    /* Main 화면 */
    public function main($argu) {
      $this->db->where('idx', $argu['user_idx']);
      $this->db->select("*");
      $this->db->from("user");
      $result = $this->db->get();
      $data = [];
      foreach( $result->result() as $row)
      {
        $temp = array(
          'name' => $row->name,
          'risk' => $this->risk()
        );
        array_push($data, $temp);
        
        return array(
          'status' => API_SUCCESS, 
          'message' => 'Success',
          'data' => $data
        );
      }

      return array(
          'status' => 400, 
          'message' => 'Fail',
          'data' => $data
        );
    }

    /* 빈혈 위험도 계산 */
    public function risk() {
      return 1;
    }

}