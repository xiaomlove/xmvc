<?php
namespace application\protect\modules\api\controllers;

use framework\App;
use application\protect\models\TorrentModel;

class TorrentController extends APIController
{
	public function actionList()
	{
		if (App::ins()->request->isGet())
		{
			$model = TorrentModel::model();
			$result = $model->getList($_GET);
		}
	}
}