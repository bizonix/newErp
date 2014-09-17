<?php
// In our case, primarily to manage logging users in and out 
// add by xiaojinhua

class Session {
	
	private $logged_in=false;
	public $user_id;
	public $message;
	
	function __construct() {
		session_start();
		$this->check_login();
    if($this->logged_in) {
      // actions to take right away if user is logged in
    } else {
        redirect_to(WEB_URL."index.php?mod=public&act=login"); // 跳转到登陆
    }

	}
	
  public function is_logged_in() {
    return $this->logged_in;
  }

	public function login($user) {
    // database should find user based on username/password
    if($user){
      $this->user_id = $_SESSION['user_id'] = $user->id;
      $this->logged_in = true;
    }
  }
  
  public function logout() {
    unset($_SESSION['user_id']);
    unset($this->user_id);
    $this->logged_in = false;
  }

	private function check_login() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->logged_in = true;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }
  
	
}

?>