<?php
class CommentModel extends Model
{
	public function tableName()
	{
		return 'comment';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	
	
}