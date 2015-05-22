<?php
namespace application\protect\controllers;

use application\protect\models\TorrentModel;

use application\protect\models\OptionModel;

use framework\App as App;
use application\protect\models\UserModel as UserModel;

class UserController extends CommonController
{
	public $layout = 'tinypt';
	
	public function actionProfile()
	{
		if (!App::ins()->user->isLogin())
		{
			$this->goError();
		}
		$model = UserModel::model();
		$userId = App::ins()->user->getId();
		$userInfo = $model->findByPk($userId);
		$torrentList = $model->table('torrent')->where('user_id='.$userId)->order('add_time DESC')->select();
		$threadList = $model->table('forum_thread')->where('user_id='.$userId)->order('add_time DESC')->select();
		$html = $this->render('profile', array('userInfo' => $userInfo, 'torrentList'  => $torrentList, 'threadList' => $threadList));
		echo $html;
	}

	
}