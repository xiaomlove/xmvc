<?php
class BookmarkModel extends Model
{
	const BOOKMARK_TYPE_THREAD = 1;//论坛主题
	const BOOKMARK_TYPE_TORRENT = 2;//种子
	
	public function tableName()
	{
		return 'bookmark';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
}