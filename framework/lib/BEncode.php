<?php
class BEncode
{
	public static function decode($string)
	{
		static $pos = 0;
		if ($pos >= strlen($string) || empty($string))
		{
			return NULL;
		}
		switch ($string[$pos]) {
			case 'd'://正常的字典，第一个字符肯定是d
				$pos++;//位置移至1
				$result = array();
				while ($string[$pos] !== 'e')//下一位也不应该是e，如果是e就结束了字典，返回空数组
				{
					$key = self::decode($string);//递归调用自身继续查找整个内容字符串，这时pos为1，对应数据为8，应该到default
					$val = self::decode($string);//字典编码格式d<编码串><编码元素>e，上边得到了编码串，再一次获得编码元素，也就是值
					if ($key === NULL || $val === NULL)
					{
						trigger_error('字典缺少key或value', E_USER_ERROR);
						return FALSE;
						break;//返回NULL，只会是当前位置大于整个字符串长度的时候。不会到这里了，上边已经停掉
					}
					$result[$key] = $val;//将字段与字段的值存入结果数组
				}
				$result['isDict'] = TRUE;//标记这个结果是字典，在encode的时候需要，否则无法区分是字典还是列表
				$pos++;//当前位置再移一位
				return $result;
			case 'l':
				$pos++;
				$result = array();
				while ($string[$pos] !== 'e')
				{
					$val = self::decode($string);
					if ($val === NULL)
					{
						trigger_error('列表缺少编码值', E_USER_ERROR);
						return FALSE;
						break;//列表跟字典差不多，只是列表只有一个编码值，不像字典有编码串和值。其实二者就基本是枚举数组与索引数组的区别
					}
					$result[] = $val;
				}
				$pos++;
				return $result;
			case 'i':
				$pos++;
				$offset = strpos($string, 'e', $pos) - $pos;//对于整数，我们是找它的结束符e，而不是像字符串那样子找分割符:
				$val = round((float)substr($string, $pos, $offset));//使用float型而不是int型，前者能存的更大。结果应该都是整数，四舍五入意义不大
				$pos += $offset + 1;
				return $val;
			default://不是d、l、i这些开头结束标记的都到这里来，那么肯定是字符串了，其编码格式<字符串长度>:<字符串值>
				$offset = strpos($string, ':', $pos) - $pos;
				//从当前位置(这里是1)开始找这一段字符串的分割符:在整个字符串中的位置，再减去当前位置，得出它们中间隔了多少位，它们中间就是字符串的长度值(8)
				$len = (int)substr($string, $pos, $offset);//截取长度值的字符串，转化为整数，得到字符串长度(8)
				$pos += $offset + 1;//移动当前位置到该字符串长度值的右边(当前位置变成到8:的右边a的位置)
				$str = substr($string, $pos, $len);//有了长度值，截取具体的字符串值(这里得到announce)
				$pos += $len;//再将当前位置移动到字符值的右边（这里到了83的8位置，明显下一次还是到default这里，重复这个过程)
				return (string)$str;//返回字符串值，这才是我们想要的。字符串长度不是我们需要的
		}
	}

	public static function encode($data)
	{
		if(is_array($data))
		{
			$result = 'l';
			if(isset($data['isDict']) && $data['isDict'])
			{
				$result = 'd';
				$isDict = TRUE;
				ksort($data, SORT_STRING);//是字典，需要对字典的字段按原始字符串排好序，否则有些客户端程序会阻塞。
			}
			foreach ($data as $key=>$value)
			{
				if(isset($isDict) && $isDict)//是字典
				{
					if($key === 'isDict' || $key === 'size' || $key === 'filecount')
					{
						continue;//跳过我们自己添加的isDict字段
					}
					$result .= strlen($key).':'.$key;//先连接字典的编码串，编码值可能是字符串、整数、列表或者字典
				}
				if(is_int($value) || is_float($value))
				{
					$result .= "i{$value}e";//是整数
				}
				elseif(is_string($value))
				{
					$result .= strlen($value).':'.$value;//是字符串
				}
				else
				{
					$result .= self::encode($value);//是列表或者字典，递归
				}
			}
			return $result.'e';//结束符
		}
		elseif(is_int($data) || is_float($data))
		{
			return "i{$data}e";//对单个整数编码
		}
		elseif(is_string($data))
		{
			return strlen($data).':'.$data;//对单个字符串编码
		}
		else
		{
			return NULL;
		}
	}
	/**
	 * decode顺便求出文件数量与总大小
	 * @param unknown $string
	 * @return number
	 */
	public static function decode_getinfo($string)
	{
		$decode = self::decode($string);
		if(!empty($decode) && is_array($decode))
		{
			if(isset($decode['info']['files'])){ //multifile
				$decode['size'] = 0;
				$decode['filecount'] = 0;
				foreach($decode['info']['files'] as $file)
				{
					$decode['filecount']++;
					$decode['size']+=$file['length'];
				}
			}else{
				$decode['size'] = $decode['info']['length'];
				$decode["filecount"] = 1;
			}
		}
		return $decode;
	}

}