<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	/* 메인 페이지 */
	public function index()
	{
		$this->load->helper('url');
		$this->load->view('welcome_message');
	}

	/* 로그인 API */
	public function login() {
		$this->error_log("[/api/login] ENTER");
		$_POST = json_decode(file_get_contents('php://input'), true);

		$this->error_log($_POST['id']);
		$this->error_log($_POST['pw']);

		$this->load->model('User');
		$result = $this->User->login(array(
			'id' => $_POST['id'],
			'pw' => md5($_POST['pw'])
		));

		$this->error_log("[/api/login] EXIT");
		echo json_encode($result);
	}

	/* 회원정보 API */
	public function user() {
		$this->error_log("[/api/user] ENTER");

		$this->load->model('User');
		$result = $this->User->get(array(
			'user_idx' => $_GET['user_idx']
		));
		
		$this->error_log("[/api/user] EXIT");

		echo json_encode($result);
	}

	public function group() {
		$this->error_log("[/api/group] ENTER");
		if(isset($_POST['user_idx'])) {
			$this->p_group();
		} else if(isset($_GET['groups_idx'])) {
			$this->g_group();
		}
	}

	/* 모임생성(통장개설) API */
	public function p_group() {
		$this->error_log("[/api/p_group] ENTER");

		$this->load->model('Group');
		$result = $this->Group->insert(array(
			'user_idx' => $_POST['user_idx'],
			'name' => $_POST['name'],
			'pw' => md5($_POST['pw']),
			'photo' => $_FILES['photo']
		));
		$this->error_log("[/api/p_group] EXIT");
		echo json_encode($result);
	}

	/* 모임 정보 API */
	public function g_group() {
		$this->error_log("[/api/g_group] ENTER");

		$this->load->model('Group');
		$result = $this->Group->get(array(
			'groups_idx' => $_GET['groups_idx']
		));
		$this->error_log("[/api/g_group] EXIT");
		echo json_encode($result);
	}

	/* 모임 리스트 API */
	public function groups() {
		$this->error_log("[/api/groups] ENTER");

		$this->load->model('Group');
		$result = $this->Group->list_search(array(
			'user_idx' => $_GET['user_idx']
		));
		$this->error_log("[/api/groups] EXIT");
		echo json_encode($result);
	}

	/* 모임원 추가 API */
	public function member() {
		$this->error_log("[/api/member] ENTER");
		$_POST = json_decode(file_get_contents('php://input'), true);

		$this->load->model('Group');
		$result = $this->Group->insert_member(array(
			'id' => $_POST['id'],
			'groups_idx' => $_POST['groups_idx'],
		));
		$this->error_log("[/api/member] EXIT");
		echo json_encode($result);
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
