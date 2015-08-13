<?php
namespace application\protect\modules\manage\controllers;

use application\protect\models\NavModel;
class NavController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionShow()
	{
		$html = $this->render('nav');
		echo $html;
	}
	
	public function actionAdd()
	{
	    if (!IS_AJAX || !IS_POST)
	    {
	        $this->goError('非法访问');
	    }
	    if (empty($_POST['name']) || empty($_POST['url']) || empty($_POST['target']))
	    {
	        $this->goError('缺少必要参数');
	    }
	    if ($_POST['target'] !== '_self' && $_POST['target'] !== '_target')
	    {
	        $this->goError('target参数错误');
	    }
	    $_POST['level'] = 0;
	    $_POST['parent_id'] = 0;
	    $navModel = NavModel::model();
	    $add = $navModel->insert($_POST);
	    if (!$add)
	    {
	        $this->goError('添加失败');
	    }
	    else 
	    {
	        $_POST['id'] = $add;
	        echo json_encode(array('code' => 1, 'msg' => '添加成功', 'data' => $_POST));
	    }
	}
}