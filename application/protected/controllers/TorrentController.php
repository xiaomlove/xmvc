<?php
class TorrentController extends CommonController
{
	public $layout = 'tinypt';
	
	public function actionList()
	{
		$this->setPageTitle('种子列表');
		echo $this->render('torrent');
	}
	
	public function actionDetail()
	{
		$this->setPageTitle('种子详情');
		echo $this->render('detail');
	}
	
	public function actionUpload()
	{
		$this->setPageTitle('发布种子');
		if(App::ins()->request->isPost())
		{
			echo '<pre/>';
			var_dump($_POST);
			var_dump($_FILES);
			echo '<hr/>';
			$model = TorrentModel::model();
			if($model->validate($_POST))
			{
				if(isset($_FILES['torrentFile']) && is_uploaded_file($_FILES['torrentFile']['tmp_name']))
				{
					$uploadFile = $_FILES['torrentFile'];
					$ext = pathinfo($uploadFile['name'], PATHINFO_EXTENSION);
					if(strtolower($ext) === 'torrent')
					{
						$decode = BEncode::decode_getinfo(file_get_contents($uploadFile['tmp_name']));
						if(!empty($decode))
						{
							$userId = App::ins()->user->getId();
							$userInfo = UserModel::model()->findByPk($userId, 'passkey');
							$passkey = $userInfo['passkey'];
							$decode['announce'] .= '?passkey='.$passkey;
							$decode['comment'] = 'come from xiaomlove.com';
							$encode = BEncode::encode($decode);
							if(!empty($encode))
							{
								$torrentPath = App::getPathOfAlias(App::getConfig('torrentSavePath'));
								if(!is_dir($torrentPath))
								{
									$mkdir = mkdir($torrentPath, 0777, TRUE);
									if(!$mkdir)
									{
										$model->setError('torrentFile', '种子保存目录无法创建');
										goto A;
									}
								}
								$torrent = file_put_contents($torrentPath.$uploadFile['name'], $encode);
								if($torrent === FALSE)
								{
									$model->setError('torrentFile', '生成种子错误！');
									goto A;
								}
								$info_hash = sha1(BEncode::encode($decode['info']));
								if(empty($info_hash) || strlen($info_hash) !== 40)
								{
									$model->setError('torrentFile', '获取种子info_hash出错');
									goto A;
								}
								$sql = "INSERT INTO torrent (main_title, slave_title, info_hash, name, introduce, size, file_count, user_id, add_time) VALUES ('{$_POST['main_title']}', '{$_POST['slave_title']}', '$info_hash', '{$uploadFile['name']}', '{$_POST['introduce']}', {$decode['size']}, {$decode['filecount']}, $userId ,".time().")";
								$insert = $model->execute($sql);
								if($insert === 0 || $insert === FALSE)
								{
									$model->setError('torrentFile', '添加种子记录出错！');
								}							
							}
							else 
							{
								$model->setError('torrentFile', 'encode为种子文件出错');
							}
						}
						else 
						{
							$model->setError('torrentFile', 'decode种子出错！');
						}
						
					}
					else 
					{
						$model->setError('torrentFile', '确保上传的是种子,后缀为.torrent!');
					}
				}
				else
				{
					$model->setError('torrentFile', '没有上传种子文件！');
				}
			}
			A:
			$errors = $model->getError();
			if(empty($errors))
			{
				echo '发布成功！';
			}
			else 
			{
				var_dump($errors);
			}
			
			exit;
		}
		else
		{
			echo $this->render('upload');
		}
		
	}
}