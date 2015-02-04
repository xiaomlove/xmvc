<?php
class CommonController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->checkLogin();
	}
	/**
	 * 这种限制要登陆才能访问一般怎么来？？
	 */
	private function checkLogin()
	{
		if((CONTROLLER === 'Index' && ACTION === 'Home') || CONTROLLER !== 'Index')
		{
			$isLogin = App::ins()->user->isLogin();
			if(!$isLogin)
			{
				$this->redirect('index/login');
			}
		}
	}
}