<?php
abstract class Model
{
	private static $_db = NULL;
	private static $_models = array();
	private $_tableName = NULL;//每个模型都不一样，不应该静态化
	private $_fields = array();
	private $_pk = NULL;//主键
	private $_pv = NULL;//主键的值，活跃记录时候才有值，否则为NULL，unique唯一验证时候出错
	private $_noPk = FALSE;//是要取的字段是否含主键
	
	//链式写法部分
	private $distinct;
	private $field;
	private $table;
	private $join;
	private $where;
	private $bindParam = array();//绑定的数据
	private $group;
	private $having;
	private $order;
	private $limit;
	private $active = FALSE;
	private $chained = FALSE;//只有通过Model::model获得的对象才可以使用链式写法进行操作
	
	//Active Record部分
	private $changeProperties = array();
	private $data = array();
	private $isNew = TRUE;//是否新记录
	
	//验证部分
	private $scene = NULL;//场景，用于自动验证
	private $errors = array();//验证的错误信息
	
	//查询缓存
	private $cacheOptions = array();//缓存时间与路径
	private $doCache = FALSE;//是否生成查询缓存
	
	
	public function __construct()
	{
		if(self::$_db === NULL)//db只需要一份
		{
// 			echo '获得一次db对象<br/>';
			self::$_db = Db::getInstance();
		}
// 		echo '实例化一次父类<br/>';
		$className = get_class($this);
		if(isset(self::$_models[$className]))
		{
			$this->_fields = self::$_models[$className]->_fields;
			$this->_pk = self::$_models[$className]->_pk;
			$this->_tableName = self::$_models[$className]->_tableName;
		}
		else 
		{
			$this->_tableName = '`'.self::$_db->prefix.$this->tableName().'`';//每次都是一个对象，用self只会存一份
			$this->_getFields();
		}
		
	}
	
	public function __set($name, $value)
	{
		if(in_array($name, $this->_fields))
		{
			$this->changeProperties[$name] = $value;
		}elseif($name === 'scene'){
			$this->scene = $value;
		}
	}
	
	public function __unset($name)
	{
		if(in_array($name, $this->_fields))
		{
			$this->changeProperties[$name] = NULL;
		}
	}
	
	public function __get($name)
	{
		if(isset($this->data[$name]))
		{
			return $this->data[$name];
		}
		$access = array('_pk', '_pv', '_tableName', '_fields');
		if(in_array($name, $access) && isset($this->$name))
		{
			return $this->$name;
		}
		return '';
	}
	
	public function __call($funcName, $params)
	{
		//通过::model()获得的对象可以执行的链式方法
		$chainedList = array('distinct', 'field', 'group', 'having', 'join', 'limit', 'order', 'table', 'where', 'active', 
							'findByPk', 'findBySql', 'select', 'deleteByPk', 'delete', 'updateByPk', 'update', 'insert', 'execute',
							'beginTransaction', 'commit', 'rollBack', 'count', 'validate', 'getError', 'setError', 'hasError', 'cache',
							'setData', 'getData',
						);
		//活跃对象可以执行的方法
		$activeList = array('delete', 'save');
		if(($this->chained === TRUE) && (in_array($funcName, $chainedList)))
		{
			return call_user_func_array(array('Model', $funcName), $params);
		}
		elseif(($this->chained === FALSE) && (in_array($funcName, $activeList)))
		{
			return call_user_func_array(array('Model', $funcName), $params);
		}
		else
		{
			trigger_error('对象'.get_class($this).'不能执行该方法:'.$funcName, E_USER_ERROR);exit;
		}
	}
	
	/**
	 * 子类必须实现，返回该模型对应的表名
	 */
	abstract protected function tableName();//抽象方法不能有{}包起来
	
	/**
	 * 返回模型对象，通过子类的类名
	 * @param unknown $className
	 * @return multitype:
	 */
	public static function getModel($className)
	{
		if(isset(self::$_models[$className]))
		{
			return self::$_models[$className];
		}
		$model = self::$_models[$className] = new $className;
		$model->chained = TRUE;
		return $model;
	}
	
	private  function _getFields()
	{
		
		$sql = 'DESC '.$this->_tableName;
		$result = self::$_db->getAllBySql($sql);
		
		foreach($result as $row)
		{
			$this->_fields[] = $row['Field'];
			if($row['Key'] === 'PRI')
			{
				$this->_pk = $row['Field'];
			}
		}
		
	}
	
	private function distinct()
	{
		if(empty($this->distinct))
		{
			$this->distinct = 'DINSTINCT';
		}
		return $this;
	}
	/**
	 * 添加字段信息，select时接收逗号分割的字段字符串，update时可以是逗号分割的字段与值或者数组
	 * @param string $field
	 * @param array $options
	 * @return Model
	 */
	private function field($field = '*', $options = array())//update时要绑定
	{
		if((is_string($field) && trim($field) !== '*') || is_array($field))
		{
			$field = $this->_parseParam($field);
		}
		if(empty($this->field))
		{
			$this->field = $field;
		}
		else 
		{
			$this->field .= ','.$field;
		}
		if(!empty($options))
		{
			$this->bindParam = array_merge($this->bindParam, $options);
		}
		return $this;
	}
	
	private function table($table)
	{
		if(is_string($table) && !empty($table) && empty($this->table))
		{
			$this->table = $table;
		}
		return $this;
	}
	
	private function join($join)
	{
// 		var_dump(get_object_vars($this));
		if(is_string($join) && !empty($join) && empty($this->join))
		{
			$this->join = $join;
		}
		return $this;
	}
	
	private function where($where, $options = array())
	{
		$where = $this->_parseParam($where, 'AND');
		if(empty($this->where))
		{
			$this->where = $where;
		}
		else 
		{
			$this->where .= ' AND '.$where;
		}
		if(!empty($options))
		{
			$this->bindParam = array_merge($this->bindParam, $options);
		}
		return $this;
	}
	
	private function group($group)
	{
		if(is_string($group) && !empty($group) && empty($this->group))
		{
			$this->group = $group;
		}
		return $this;
	}
	
	private function having($having, $options = array())
	{
		$having = $this->_parseParam($having, 'AND');
		if(empty($this->having))
		{
			$this->having = $having;
		}
		else
		{
			$this->having .= ' AND '.$having; 
		}
		if(!empty($options))
		{
			$this->bindParam = array_merge($this->bindParam, $options);
		}
		return $this;
	}
	
	private function order($order)
	{
		$order = $this->_parseParam($order);
		if(empty($this->order))
		{
			$this->order = $order;
		}
		else
		{
			$this->order .= ','.$order; 
		}
		return $this;
	}
	/**
	 * Enter description here ...
	 * @param  $limit limit后面的数字,字符串("0,5")或者整数（1）都可以
	 */
	private function limit($limit)
	{
		if(!empty($limit) && empty($this->limit))
		{
			$this->limit = $limit;
		}
		return $this;
	}
	
	private function active()
	{
		if($this->active === FALSE)
		{
			$this->active = TRUE;
		}
		return $this;	
	}
	
	private function reset()
	{
		$this->distinct= NULL;
		$this->field = NULL;
		$this->table = NULL;
		$this->join = NULL;
		$this->where = NULL;
		$this->group = NULL;
		$this->having = NULL;
		$this->bindParam = array();
		$this->order = NULL;
		$this->active = FALSE;
		$this->_noPk = FALSE;
		$this->doCache = FALSE;
		$this->cacheOptions = array();
	}
	
	private function _parseParam($param, $connectStr = '')
	{
		
		if(is_string($param))
		{
			if(strpos($param, ',') !==FALSE && !empty($connectStr))
			{
				//id=5,name='小明',email like '%aa%'
				if(stripos($param, ' or ') === FALSE)
				{
					$result = str_replace(',', (' '.$connectStr.' '), $param);//没有or
			
					return $result;
				}
				else 
				{
					$paramArr = explode(',', $param);
					$result = '';
					foreach($paramArr as $paramOne)
					{
						if(stripos($paramOne, ' or ') === FALSE)
						{
							$paramOne = $paramOne.(' '.$connectStr.' ');
							$result .= $paramOne;
							
						}
						else 
						{
							$paramOne = '('.$paramOne.')'.(' '.$connectStr.' ');
							$result .= $paramOne;
							
						}
					}
					return trim($result, (' '.$connectStr.' '));
				}
							
			}
			else
			{
				return stripos($param, ' or') === FALSE? $param: '('.$param.')';//这里的or不怎么准，有的字段里面就有or，比如password
			}
		}
		elseif(is_array($param) && ArrayHelper::is_assoc($param))//数组必须是键和值连起是一个完整的条件，推荐使用逗号连接，也就是是索引数组
		{
			$result = '';
// 			array('id'=>'>5', 'name'=>'like "abc"')
			$joinStr = empty($connectStr) ? ',' : $connectStr;
			foreach($param as $key=>$value)
			{
				$paramOne = $key.' '.$value.' '.$joinStr.' ';
				
				$result .= $paramOne;
			}
			return trim($result, (' '.$joinStr.' '));
		}
		else
		{
			trigger_error('参数有误：条件必须是字符串或索引数组');exit;
		}
	}
	
	private function _buildSelectSql()
	{
		$sql = 'SELECT '
				.(empty($this->distinct)? '': $this->distinct.' ')
				.(empty($this->field)? '* ': $this->field.' ').' FROM '
				.(empty($this->table)? $this->_tableName.' ': $this->table.' ')
				.(empty($this->join)? '': $this->join.' ')
				.(empty($this->where)? 'WHERE 1 ': 'WHERE '.$this->where.' ')
				.(empty($this->group)? '': 'GROUP BY '.$this->group.' ')
				.(empty($this->having)? '': 'HAVING '.$this->having.' ')
				.(empty($this->order)? '': 'ORDER BY '.$this->order.' ')
				.(empty($this->limit)? '': 'LIMIT '.$this->limit);
		
		return $sql;
		
	}
	
	/**
	 * 通过主键查找一条记录
	 * @param string $pk
	 * @param mixed $fields，需要查询的字段，字符串或数组
	 * @return string
	 */
	private function findByPk($pk = '', $field = '*')
	{
		if(empty($pk))
		{
			return '';
		}
		if(empty($this->_pk))
		{
			trigger_error('没有主键！', E_USER_ERROR);
			return '';
		}
		$field = $field === '*'? '*': $this->_parseParam($field);
		$sql = 'SELECT '.$field.' FROM '.$this->_tableName.' WHERE '.$this->_pk.'=:pk LIMIT 1';
		$options = array(':pk'=>$pk);
		
		//检查缓存是否存在
		if(Cache::$_doQueryCache && !empty($this->cacheOptions))
		{
			$result = $this->checkQueryCache($sql, $options);
			if($result !== NULL)
			{
				return $result;
			}
			else
			{
				$this->doCache = TRUE;
			}
		}
		$result = self::$_db->getOneBySql($sql, $options, PDO::FETCH_ASSOC);
		if($this->active)
		{
			$result = $this->_addActiveObject($result);
		}
		if($this->doCache)
		{
			Cache::setQueryCache($this->cacheOptions['sql'], $result);
		}
		$this->reset();
		return $result;
		
	}
	/**
	 * 通过链式写法查找所有记录
	 */
	private function select()
	{
		if($this->active)
		{
			//活跃记录必须把主键字段的值取回来
			if(trim($this->field !== '*'))
			{
				$fieldArr = preg_split('/,[\s]*/', trim($this->field));
				if(!in_array($this->_pk, $fieldArr))
				{
					$this->field($this->_pk);
					$this->_noPk = TRUE;
				}
			}
		}
		$sql = $this->_buildSelectSql();
		$options = $this->bindParam;
		
		//检查缓存是否存在
		if(Cache::$_doQueryCache && !empty($this->cacheOptions))
		{
			$result = $this->checkQueryCache($sql, $options);
			if($result !== NULL)
			{
				return $result;
			}
			else
			{
				$this->doCache = TRUE;
			}
		}
		
		$result = self::$_db->getAllBySql($sql, $options, PDO::FETCH_ASSOC);
		if($this->active)
		{
			$result = $this->_addActiveObject($result);
		}
		if($this->doCache)
		{
			Cache::setQueryCache($this->cacheOptions['sql'], $result);
		}
		$this->reset();
		return $result;
	}
	/**
	 * 通过sql语句查找所有记录
	 * @param unknown $sql
	 * @param unknown $options
	 * @param string $active
	 * @return string
	 */
	private function findBySql($sql, $options = array())
	{
		if(empty($sql))
		{
			return '';
		}
		if(!is_string($sql) || !is_array($options))
		{
			trigger_error('findBySql()参数必须是sql语句和绑定数据的数组', E_USER_NOTICE);
			return '';
		}
		//如何分析sql语句是否含了主键与否？？？
		if($this->active)
		{
			preg_match('/select[\s]+(.*)[\s]+from(.*)/i', trim($sql), $match);
			$fieldStr = trim($match[1]);
			$sqlLeft = $match[2];
			if($fieldStr !== '*')
			{
				$fieldArr = preg_split('/,[\s]*/', $fieldStr);
				if(!in_array($this->_pk, $fieldArr))
				{
					$sql = 'SELECT '.$fieldStr.','.$this->_pk.' FROM'.$sqlLeft;
					$this->_noPk = TRUE;
				}
			}
			
		}
		//检查缓存是否存在
		if(Cache::$_doQueryCache && !empty($this->cacheOptions))
		{
			$result = $this->checkQueryCache($sql, $options);
			if($result !== NULL)
			{
				return $result;
			}
			else
			{
				$this->doCache = TRUE;
			}
		}
		
		$result = self::$_db->getAllBySql($sql, $options, PDO::FETCH_ASSOC);
		if($this->active)
		{
			$result = $this->_addActiveObject($result);
		}
		if($this->doCache)
		{
			Cache::setQueryCache($this->cacheOptions['sql'], $result);
		}
		$this->reset();
		return $result;
	}
	
	private function _buildUpdateSql()
	{
// 		echo '<br/>field:',$this->field;
// 		echo '<br/>where:',$this->where;exit;
		$sql = 'UPDATE '
				.(empty($this->table)? $this->_tableName: $this->table).' SET '
				.$this->field.' '
				.(empty($this->where)? ' WHERE 1 ': ' WHERE '.$this->where);
		
		return $sql;
	}
	/**
	 * 通过主键更新
	 * @param pk string 主键
	 * @param field array 要更新的字段和值信息  索引数组 field=>value
	 */
	private function updateByPk($pk, array $fieldArr)
	{
		if(empty($pk) || empty($fieldArr))
		{
			return false;
		}
		if(empty($this->_pk))
		{
			trigger_error('没有主键！', E_USER_ERROR);
			return false;
		}
// 		$field = $this->_parseParam($field);
		$field = '';
		foreach ($fieldArr as $key => $value)
		{
			$field .= "$key='$value',";
		}
		$field = rtrim($field, ',');
		
		$sql = 'UPDATE '.$this->_tableName.' SET '.$field.' WHERE '.$this->_pk.'='.$pk;
		return self::$_db->execute($sql);
	}
	/**
	 * 通过链式写法更新
	 */
	private function update()
	{
		$sql = $this->_buildUpdateSql();
		$options = $this->bindParam;
		$this->reset();
		return self::$_db->execute($sql, $options);
	}
	
	private function _buildDeleteSql()
	{
		$sql = 'DELETE FROM '
				.(empty($this->table)? $this->_tableName: $this->table). ' '
				.$this->where;
		
		return $sql;
	}
	/**
	 * 通过主键删除
	 * @param unknown $pk
	 * @return boolean
	 */
	private function deleteByPk($pk)
	{
		if(empty($pk))
		{
			return false;
		}
		if(empty($this->_pk))
		{
			trigger_error('没有主键！', E_USER_ERROR);
			return false;
		}
		$sql = 'DELETE FROM '.$this->_tableName.' WHERE '.$this->_pk.'=:pk';
		return self::$_db->execute($sql, array(':pk'=>$pk));
	}
	/**
	 * 删除
	 */
	private function delete()
	{
		if($this->chained)
		{
			//链式写法的删除
			if(empty($this->where))
			{
				return FALSE;
			}
			$sql = $this->_buildDeleteSql();
			$options = $this->bindParam;
			$this->reset();
			$result = self::$_db->execute($sql, $options);
		}
		else 
		{
			//活跃对象的删除
			if(!empty($this->changeProperties[$this->_pk]))
			{
				$pk = $this->changeProperties[$this->_pk];
			}
			elseif(!empty($this->data[$this->_pk]))
			{
				$pk = $this->data[$this->_pk];
			}
			else 
			{
				trigger_error('找不主键的值，无法删除！', E_USER_NOTICE);
				return FALSE;
			}
			$result = $this->deleteByPk($pk);
		}
		if($result)
		{
			$this->data = array();
			$this->_pv = NULL;
		}
		return $result;
	}
	
	/**
	 * 执行insert,update,delete语句，select语句用findBySql()
	 * @param unknown $sql
	 * @param unknown $options
	 * @return string|Ambigous <boolean, number>
	 */
	private function execute($sql, $options = array())
	{
		if(empty($sql))
		{
			return FALSE;
		}
		if(!is_string($sql) || !is_array($options))
		{
			trigger_error('execute()参数必须是sql语句和绑定数据的数组', E_USER_NOTICE);
			return FALSE;
		}
		return self::$_db->execute($sql, $options);
	}
	/**
	 * 通过链式写法插入单条数据，成功返回最后插入的记录的id。多条自己拼sql语句使用execute()执行
	 * @param array $data 数据数组，array(1, '小明', '1998-12-23')或array('id'=>1, 'name'=>'小明', 'birthday'=>'...')
	 * @return mixed
	 */
	private function insert($data = array())
	{
		if(empty($data))
		{
			return FALSE;
		}
		if(ArrayHelper::is_assoc($data))
		{
			//是索引数组，含字段信息，不考虑$this->field
			$keys = array_keys($data);
			$field = implode(',', $keys);
		}
		else 
		{
			//非索引数组，字段看file或_fields
			$field = empty($this->field)? implode(',', $this->_fields): $this->field;
		}
		$valueStr = '';
		foreach($data as $value)
		{
			if($value === NULL)
			{
				$valueStr .= "NULL,"; 
			}
			else 
			{
				$valueStr .= "'".$value."',";
			}
			
		}
		$valueStr = rtrim($valueStr, ',');
// 		var_dump($valueStr);exit;
		$table = empty($this->table)? $this->_tableName: $this->table;
		$sql = 'INSERT INTO '.$table.' ('.$field.') VALUES ('.$valueStr.')';
		$this->reset();
		return self::$_db->execute($sql);
	}
	/**
	 * 根据条件求记录数
	 * @return int 
	 */
	private function count()
	{
		$sql = "SELECT count(*) as count FROM "
				.(empty($this->table)? $this->_tableName: $this->table)
				.(empty($this->where)? ' WHERE 1 ': ' WHERE '.$this->where);
		$options = $this->bindParam;
		$this->reset();
		return self::$_db->count($sql, $options);
	}
	/**
	 * 开启事务
	 */
	private function beginTransaction()
	{
		return self::$_db->beginTransaction();
	}
	
	private function commit()
	{
		return self::$_db->commit();
	}
	
	private function rollBack()
	{
		return self::$_db->rollBack();
	}
	
	private function _addActiveObject($result)
	{
		if(!empty($this->table) && ($this->table !== $this->_tableName))
		{
			trigger_error('Active Record不支持跨表操作，请实例化要操作的表的模型来进行！', E_USER_ERROR);exit;
		}
		$className = get_class($this);
		if(count($result) === count($result, 1))
		{
			//一维数组，即确定了查询只有一条结果
			$obj = new $className;
			$obj->_pv = $result[$this->_pk];
			if($this->_noPk)
			{
				//没有取主键的值
				unset($result[$this->_pk]);
			}
			$obj->data = $result;
			$obj->isNew = FALSE;
			
			return $obj;	
		}
		else
		{
			$out = array();
			foreach($result as $data)
			{
				$obj = new $className;
				$obj->_pv = $data[$this->_pk];
				if($this->_noPk)
				{
					unset($data[$this->_pk]);
				}
				$obj->data = $data;
				$obj->isNew = FALSE;
				$out[] = $obj;
			}
			return $out;
		}
	}
	
	private function save()
	{
		if(empty($this->changeProperties))
		{
			return FALSE;
		}
		
		if($this->isNew)
		{
			//新记录，插入
			$field = implode(',', array_keys($this->changeProperties));
			$valueStr = '';
			foreach($this->changeProperties as $value)
			{
				if($value === NULL)
				{
					$valueStr .= 'null,';
				}
				elseif($value === '')
				{
					$valueStr .= "'',";
				}
				else
				{
					$valueStr .= "'".$value."',";
				}
			}
			$valueStr = rtrim($valueStr, ',');
			$sql = 'INSERT INTO '.$this->_tableName.' ('.$field.') VALUES ('.$valueStr.')';
			$result = self::$_db->execute($sql);
			if($result)
			{
				$this->data = $this->changeProperties;
				$this->changeProperties = array();
				$this->data[$this->_pk] = $result;
				$this->_pv = $result;
				$this->isNew = FALSE;
			}
			return $result;
		}
		else
		{
			//更新记录
			//set name=?,class_id=?,array('xiaoming', 2)
			$setStr = implode('=?,', array_keys($this->changeProperties)).'=?';
// 			echo $setStr;exit;
			$options = array_merge(array_values($this->changeProperties), array($this->_pv));
			$sql = 'UPDATE '.$this->_tableName.' SET '.$setStr.' WHERE '.$this->_pk.'=?';
			$result = self::$_db->execute($sql, $options);
			if($result)
			{
				$this->data = array_merge($this->data, $this->changeProperties);
				$this->changeProperties = array();
			}
			return $result;
		}
	}
	/**
	 * 进行验证
	 * @param boolean $batch 是否批量验证，true全部验证完，false遇到错误即停止
	 * @param array $data 要验证的数据，不传递取$changeProperties的数据
	 */
	private function validate($data = array(), $batch = TRUE)
	{
		if(method_exists($this, 'rules'))
		{
			$rules = $this->rules();
			$data = empty($data)? $this->changeProperties: $data;
			if(!empty($rules) && !empty($data))
			{
				Validator::init($rules, $data);
				$flag = TRUE;
				foreach($rules as $rule)
				{
					$fields = $rule[0];
					$validator = trim($rule[1]);
					$msg = $rule[2];
					if(strpos($fields, ',') !== FALSE)
					{
						//多个字段一起传递
						$fieldArr = explode(',', $fields);
					}
					else 
					{
						$fieldArr = array($fields);
					}
					foreach($fieldArr as $field)
					{
						$field = trim($field);
						if(!isset($data[$field]))
						{
							//该字段的数据不存在，不验证吧。
							continue;
						}
						if(isset($rule['on']) && $this->scene !== $rule['on'])
						{
							//当前场景跟规则指定的应用场景不一致，不应用此验证
							continue 2;
						}
						if(method_exists('Validator', $validator))
						{
							$result = Validator::$validator($field, $rule);
						}
						elseif($validator === 'unique')
						{
							$result = $this->_unique($field, $data);
						}
						elseif(method_exists($this, $validator))
						{
							$result = call_user_func(array($this, $validator), $data[$field]);//把值传递到自定义函数检验
						}
						else 
						{
							$flag = FALSE;
							trigger_error('不存在该验证规则：'.$validator, E_USER_NOTICE);
							continue 2;	
						}
						
						if(!$result)
						{
							//有错误
							if(isset($this->errors[$field]))
							{
								$this->errors[$field] .= ','.$msg;
							}
							else
							{
								$this->errors[$field] = $msg;
								
							}
							$flag = FALSE;
							if(!$batch)
							{
								break 2;
							}
						}
					}
				}
				return $flag;
			}
			else 
			{
				trigger_error('没有验证规则或者数据', E_USER_WARNING);
				return TRUE;
			}
		}
		else 
		{
			trigger_error('没有定义验证规则', E_USER_WARNING);
			return TRUE;
		}
	}
	
	private function getError($field = '')
	{
		if(!empty($field))
		{
			if(is_string($field))
			{
				if(isset($this->errors[$field]))
				{
					return $this->errors[$field];
				}
			}
			else
			{
				trigger_error('字段只会是字符串', E_USER_NOTICE);
			}
			return NULL;
		}
		else 
		{
			return $this->errors;
		}
		
	}
	/**
	 * 判断验证是否有错误，用于在表单输出错误
	 * Enter description here ...
	 * @param unknown_type $field
	 */
	private function hasError($field = '')
	{
		if(!empty($field) && is_string($field))
		{
			return self::getError($field) !== NULL;
//			var_dump(self::getError($field));
		}
		elseif (empty($field))
		{
			$errors = self::getError();
			return !empty($errors);
		}
		return TRUE;//有错误
	}
	
	private function setError($key, $value)
	{
		if(!empty($key) && is_string($key))
		{
			$this->errors[$key] = $value;
		}
	}
	/**
	 * 自动验证，唯一验证
	 * @param unknown $field
	 * @param unknown $value 表单传递过来的所有值（二维数组）包括主键的值
	 * @return boolean
	 */
	private function _unique($field, $data)
	{
		$value = $data[$field];
		if(!$this->chained || !isset($data[$this->_pk]))
		{
			//是新增操作，或者数据中没有主键的值也是新增操作。编辑操作传递过来的数组有主键的值
			return $this->where($field.'=:value', array(':value'=>$value))->count() == 0;
			
		}
		elseif(isset($data[$this->_pk]) || $this->_pv !== NULL)
		{
			//链式操作，是编辑保存
			//如何确定是否唯一，选择到的是否是它自己？？
			
			$sql = 'SELECT '.$this->_pk.' FROM '.$this->_tableName.' WHERE '.$field.'=:value';
			$result = self::$_db->getAllBySql($sql, array(':value'=>$value));
			$count = count($result);
			if($count > 1)
			{
				return FALSE;
			}
			elseif(($count == 1))
			{
				$pv = $this->_pv !== NULL ? $this->_pv : $data[$this->_pk];
				if (strval($result[0][$this->_pk]) === strval($pv))//这不太清楚什么时候是整型什么时候字符串真麻烦
				{
					return TRUE;
				}
				return FALSE;
			}
			
			return TRUE;
		}
		else 
		{
			trigger_error('编辑操作且非活跃对象且没有传递主键的值，无法使用unique验证', E_USER_ERROR);
			return FALSE;
		}
		
	}
	
	private function cache($expire = '', $path = '')
	{
		if(Cache::$_doQueryCache)
		{
			$this->cacheOptions = array('expire'=>$expire, 'path'=>$path);
		}
		return $this;
	}
	
	private function checkQueryCache($sql, $options = array())
	{
		$cache = NULL;
		if(isset($this->cacheOptions['expire']) && isset($this->cacheOptions['path']))
		{
			if(count($options))
			{
				foreach ($options as $key=>$value)
				{
					$sql .= '&'.$key.'='.$value;
				}
			}
			$cache = Cache::getQueryCache($sql, $this->cacheOptions['expire'], $this->cacheOptions['path']);
			if($cache === NULL)
			{
				$this->cacheOptions['sql'] = $sql;
			}
		}
		return $cache;
	}
	/**
	 * 手动为data赋值，在非查询情况下用于表单的数据通过getData获取。只有setData后getData才有值
	 * Enter description here ...
	 * @param array $data
	 */
	private function setData(array $data)
	{
		$this->data = $data;
	}
	
	private function getData($key)
	{
		if(!empty($key) && is_string($key) && isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		else
		{
			return NULL;
		}
	}
	
}