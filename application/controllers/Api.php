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

	/* 측정하기 API */
	public function measure() {

		$this->error_log("[/api/measure] ENTER");
		// $_POST = json_decode(file_get_contents('php://input'), FILE_USE_INCLUDE_PATH);

		$this->load->model('Measure');
		$result = $this->Measure->insert(array(
			'user_idx' => $_POST['user_idx'],
			'period' => $_POST['period'],
			'video' => $_FILES['video']
		));
		
		$this->error_log("[/api/measure] EXIT");

		echo json_encode($result);
	}

	/* 측정리스트 API */
	public function measures() {
		$this->error_log("[/api/measures] ENTER");

		$this->load->model('Measure');
		$result = $this->Measure->list_search(array(
			'user_idx' => $_GET['user_idx']
		));
		
		$this->error_log("[/api/measures] EXIT");

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

    /* 측정하기 버튼 클릭 */
    public function measure_flag() {
    	$this->error_log("[/api/measure_flag] ENTER");
		$_POST = json_decode(file_get_contents('php://input'), true);

		$this->load->model('Measure');

		$result = $this->Measure->flag(array(
			'user_idx' => $_POST['user_idx'],
			'flag' => $_POST['flag']
		));

		$this->error_log("[/api/measure_flag] EXIT");
		echo json_encode($result);
    }

    /* 메인화면 API */
	public function main() {
		$this->error_log("[/api/main] ENTER");

		$this->load->model('User');
		$result = $this->User->main(array(
			'user_idx' => $_GET['user_idx']
		));
		
		$this->error_log("[/api/main] EXIT");

		echo json_encode($result);
	}

}
