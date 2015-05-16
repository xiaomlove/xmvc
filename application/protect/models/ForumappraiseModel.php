<?php
namespace application\protect\models;

class ForumappraiseModel extends \framework\core\Model
{
	
	public function tableName()
	{
		return 'forum_appraise';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
	//array(字段名， 验证规则， 错误信息，[[附加条件], [验证场景]])
	//前三个必填，如果规则中是in，range等需要相应的值的，则写到附加条件，否则附加条件不需要填写。
	//验证场景如果有添加，则该规则只有开始验证前由模型指定为场场景才应用此验证，否则不应用。为一个键值对，如'on'=>'edit'
	public function rules()
	{
		return array(
			array('content', 'required', '不能为空！'),
		);
	}
	
	
	
}