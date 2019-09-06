<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Community extends CI_Model {

  function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /* 커뮤니티 생성 */
    public function insert($argu) {   
      $this->error_log("[models/Community/insert] ENTER");  
      if(empty($argu['groups_idx'])) {
          return array(
            'status' => API_FAILURE, 
            'message' => 'Fail'
          );
      } else {
        if($this->check($argu) == 0){
          $this->db->set('groups_idx', $argu['groups_idx']);
          $this->db->insert("community");

          return array(
            'status' => API_SUCCESS, 
            'message' => 'Success'
          );
        } else {
          return array(
            'status' => 433, 
            'message' => '이미 커뮤니티가 있습니다.'
          );
        }
      } 
    }

    /* 커뮤니티가 존재하는지 확인 */
    public function check($argu) {
      $result = $this->db->query("select * from community where groups_idx=".$argu['groups_idx']);
      return $result->num_rows();
    }

    /* 커뮤니티 생성 */
    public function insert_board($argu) {   
      $this->error_log("[models/Community/insert_board] ENTER");  
      if(empty($argu['user_idx']) || empty($argu['community_idx']) || empty($argu['content'])) {
        return array(
          'status' => API_FAILURE, 
          'message' => 'Fail'
        );
      } else {
        $this->db->set('is_notice', $argu['is_notice']);
        $this->db->set('user_idx', $argu['user_idx']);
        $this->db->set('community_idx', $argu['community_idx']);
        $this->db->set('content', $argu['content']);
        $this->db->set('date', date("Y-m-d H:i:s"));
        $this->db->insert("boards");

        return array(
          'status' => API_SUCCESS, 
          'message' => 'Success'
        );
        
      } 
    }

    /* 글목록 */
    public function list_search($argu) {
      $query = "select * from boards where community_idx=".$argu['community_idx'];

      $result = $this->db->query($query);

      $data = [];
      if($result->num_rows()) {
        foreach( $result->result() as $row )
        {
          $row->idx = (int)$row->idx;
          $row->is_notice = (int)$row->is_notice;
          $row->user_idx = (int)$row->user_idx;
          $row->community_idx = (int)$row->community_idx;
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