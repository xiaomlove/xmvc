<?php
namespace framework\lib\captcha;

class Captcha
{
	const TYPE_NUM = 1;
	
	const TYPE_LETTER = 2;
	
	const TYPE_NUM_LETTER = 3;
	
	const TYPE_CALC = 4;
	
	private static $length = 4;//字符个数
	
	private static $type = self::TYPE_NUM_LETTER;//类型，1数字，2数字+字母，3字母，4算术
	
	private static $width = 120;//宽度
	
	private static $height = 40;//高度
	
	private static $points = 400;//干扰点
	
	private static $lines = 10;//干扰线
	
	private static $size = 20;//字体大小
	
	private static $font = 't1.ttf';//字体
	
	private static $img = NULL;//画布资源句柄
	
	public function __construct(array $options = array())
	{
		foreach ($options as $key => $value)
		{
			if ($key === 'type')
			{
				if ($value == self::TYPE_CALC || $value == self::TYPE_LETTER || $value == self::TYPE_NUM || $value == self::TYPE_NUM_LETTER)
				{
					self::$type = $value;
				}
				continue;
			}
			if (isset(self::$$key) && $key !== 'img')
			{
				self::$$key = intval($value);
			}
		}
	}
	
	public function build()
	{
		self::createBackground();
		if (self::$points > 0)
		{
			self::createPoints();
		}
		if (self::$lines > 0)
		{
			self::createLines();
		}
		$code = self::createCode();
		if ($code === FALSE)
		{
			return FALSE;
		}
		header('Content-Type:image/png');
		imagepng(self::$img);
		imagedestroy(self::$img);
		return $code;
	}
	
	private static function createBackground()
	{
		self::$img = imagecreatetruecolor(self::$width, self::$height);
		$bgColor = imagecolorallocate(self::$img, 125, 125, 125);//这应该是一个比较浅的颜色
		imagefill(self::$img, 0, 0, $bgColor);
	}
	
	private static function createPoints()
	{
		if (self::$points > 0)
		{
			$len = self::$points;
			$img = self::$img;
			$w = self::$width;
			$h = self::$height;
			for ($i = 0; $i < $len; $i++)
			{
				//一个比背景深点的随机颜色
				$color = imagecolorallocate($img, mt_rand(50, 120), mt_rand(50, 120), mt_rand(50, 120));
				imagesetpixel($img, mt_rand(1, $w), mt_rand(1, $h), $color);//随机设置干扰点
			}
		}
	}
	
	private static function createLines()
	{
		if (self::$lines > 0)
		{
			$len = self::$lines;
			$img = self::$img;
			$w = self::$width;
			$h = self::$height;
			for ($i = 0; $i < $len; $i++)
			{
				//更深一点？浅一点
				$color = imagecolorallocate($img, mt_rand(20, 80), mt_rand(20, 80), mt_rand(20, 80));
				//随机设置干扰线，这是直线
				imageline($img, mt_rand(0, 5), mt_rand(1, $h), mt_rand($w - 5, $w), mt_rand(1, $h), $color);
			}
		}
	}
	
	private static function createCode()
	{
		switch (self::$type)
		{
			case self::TYPE_LETTER:
				$codeArr = self::getLetters();
				$code = implode('', $codeArr);
				break;
			case self::TYPE_NUM:
				$codeArr = self::getNumbers();
				$code = implode('', $codeArr);
				break;
			case self::TYPE_NUM_LETTER:
				$codeArr = self::getNumberAndLetter();
				$code = implode('', $codeArr);
				break;
			case self::TYPE_CALC:
				$result = self::getCalcString();
				$code = $result['code'];
				$codeArr = $result['codeArr'];
				break;
			default:
				return FALSE;
		}
		self::drawCode($codeArr);
		return $code;
	}
	
	private static function drawCode(array $codes)
	{
		$img = self::$img;
		$w = max(self::$width, 20);
		$h = max(self::$height, 10);
		$size = self::$size;
		$fontFile = __DIR__.DIRECTORY_SEPARATOR.trim(self::$font, './');//字体跟类目录一致
		$d = ($w - 10)/self::$length;//水平位移公差
		foreach ($codes as $k => $code)
		{
			//较深的一个颜色
			$color = imagecolorallocate($img, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
			imagettftext($img, $size, mt_rand(-30, 30), ($k*$d + $d/2 - $size/2), ($h/2 + $size/2), $color, $fontFile, $code);
		}
	}
	
	private static function getNumbers()
	{
		$numbers = array();
		$len = self::$length;
		for ($i = 0; $i < $len; $i++)
		{
			$numbers[] = mt_rand(0, 9);
		}
		return $numbers;
	}
	
	private static function getLetters()
	{
		$allLetter = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWZYZ';//去掉l、L,o、O
		$count = strlen($allLetter);
		$len = self::$length;
		$letters = array();
		for ($i = 0; $i < $len; $i++)
		{
			$letters[] = $allLetter[mt_rand(0, $count)];
		}
		return $letters;
	}
	
	private static function getNumberAndLetter()
	{
		$lowercase = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y');
		$toUpperCase = array_map('strtoupper', $lowercase);
		$number = array('2', '3', '4', '5', '6', '7', '8', '9');
		$numberAndLetter = array_merge($lowercase, $toUpperCase, $number);
		shuffle($numberAndLetter);
		return array_slice($numberAndLetter, 0, self::$length);
	}
	
	private static function getCalcString()
	{
		$a = mt_rand(21, 99);
		$b = mt_rand(1, 20);
		$icon = mt_rand(1, 10);
		$codeArr = array();
		if ($icon > 5)
		{
			//加法
			$codeArr[] = strval($b);
			$codeArr[] = '+';
			$codeArr[] = '?';
			$codeArr[] = '=';
			$codeArr[] = strval($a);
			self::$length = count($codeArr);
			$code = strval($a - $b);
		}
		else 
		{
			$codeArr[] = strval($a);
			$codeArr[] = '—';
			$codeArr[] = strval($b);
			$codeArr[] = '=';
			$codeArr[] = '?';
			self::$length = count($codeArr);
			$code = strval($a - $b);
		}
		
		return array('code' => $code, 'codeArr' => $codeArr);
	}
}