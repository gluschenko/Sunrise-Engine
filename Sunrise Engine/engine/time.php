<?php
//Alexander Gluschenko (15-07-2015)

/** Возвращает время полуночи */
function GetMidnight($days = 0)//Ноль выдает минувшую полночь
{
	//return strtotime("midnight") + ($days * 24 * 3600);
	
	$day = GetDay(time());
	$month = GetMonth(time());
	$year = GetYear(time());
	
	return TimeByDate($day, $month, $year) + ($days * 24 * 3600);
}

/** Возвращает массив полуночей */
function GetMidnights($number)
{
	$midtime = GetMidnight(0); //strtotime("midnight");
	$mids = "";
	
	for($i = 0; $i < $number; $i++)
	{
		$ftime = $midtime - ($i*(3600*24));
		$mids[$i] = $ftime;
	}
	
	return $mids;
}

/** Возвращает unixtime даты */
function TimeByDate($day = 0, $month = 0, $year = 0, $hours = 0, $minutes = 0, $seconds = 0)
{
	//return strtotime($day."-".$month."-".$year);
	return mktime($hours, $minutes, $seconds, $month, $day, $year);
}

/** Возвращает дату-время (адаптированные) */
function GetTime($sec, $utc = -1000)
{
	if($utc == -1000)
	{
		$engine_cfg = GetEngineConfig();
		$utc = $engine_cfg['settings']['timezone'];
	}
	//
	$ctime = time();
	///
	$d = gmdate('d', $sec + ($utc*3600));
	$m = gmdate('m', $sec + ($utc*3600));
	$y = gmdate('Y', $sec + ($utc*3600));
	$h = gmdate('H', $sec + ($utc*3600));
	$i = gmdate('i', $sec + ($utc*3600));
	$s = gmdate('s', $sec + ($utc*3600));
	
	$cd = gmdate('d', $ctime + ($utc*3600));
	$cm = gmdate('m', $ctime + ($utc*3600));
	$cy = gmdate('Y', $ctime + ($utc*3600));
	///
	if($m == '01')$mname = 'янв';
	if($m == '02')$mname = 'фев';
	if($m == '03')$mname = 'мар';
	if($m == '04')$mname = 'апр';
	if($m == '05')$mname = 'мая';
	if($m == '06')$mname = 'июн';
	if($m == '07')$mname = 'июл';
	if($m == '08')$mname = 'авг';
	if($m == '09')$mname = 'сен';
	if($m == '10')$mname = 'окт';
	if($m == '11')$mname = 'ноя';
	if($m == '12')$mname = 'дек';
	///
	if(substr($d, 0, 1) == '0')$d = substr($d, -1);//Вырезаем 0, если он есть
	///
	if($y != $cy)
	{
		$time = $d." ".$mname." ".$y." в ".$h.":".$i.":".$s;
	}
	else
	{
		if($d == $cd && $m == $cm)
		{
			$time = "сегодня в ".$h.":".$i.":".$s;
		}
		elseif($d == $cd - 1 && $m == $cm)
		{
			$time = "вчера в ".$h.":".$i.":".$s;
		}
		else
		{
			$time = $d." ".$mname." в ".$h.":".$i.":".$s;
		}
	}
	return $time;
}

/** Укороченные дата-время */
function GetShortTime($sec, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$ctime = time();
	
	$now_year = GetYear(time(), $utc);
	$year = GetYear($sec, $utc);
	
	$year_out = "";
	if($now_year != $year)$year_out = " ".$year;
	///
	$d = gmdate('d', $sec + ($utc*3600));
	$m = gmdate('m', $sec + ($utc*3600));
	///
	if($m == '01')$mname = 'янв';
	if($m == '02')$mname = 'фев';
	if($m == '03')$mname = 'мар';
	if($m == '04')$mname = 'апр';
	if($m == '05')$mname = 'мая';
	if($m == '06')$mname = 'июн';
	if($m == '07')$mname = 'июл';
	if($m == '08')$mname = 'авг';
	if($m == '09')$mname = 'сен';
	if($m == '10')$mname = 'окт';
	if($m == '11')$mname = 'ноя';
	if($m == '12')$mname = 'дек';
	///
	if(substr($d, 0, 1) == '0')$d = substr($d, -1);
	///
	$time = $d." ".$mname.$year_out;
	return $time;
}

/** Возвращает календарь из 12 месяцев вперед или назад */
function GetMonths($sec, $dir = 1, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$y = GetYear($sec, $utc);
	$m = GetMonth($sec, $utc);
	$d = GetDay($sec, $utc);
	///
	$months_data;
	$today_midmight = strtotime("midnight");
	$current_days = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	$month_time = $today_midmight - (3600*24*($d-2)) + (($current_days-1) * 3600 * 24);
	
	for($i = 0; $i < 12; $i++)
	{
		$cur_year = $y;
		$cur_month = $m + ($i * $dir);

		if($cur_month == 0)
		{
			$cur_month = 12;
			$cur_year += $dir;
		}
		
		if($cur_month > 12)
		{
			$cur_month -= 12;
			$cur_year++;
		}
		
		if($cur_month < 0)
		{
			$cur_month += 12;
			$cur_year--;
		}
		
		//$mnum = ($mid - $i > 0)? $mid - $i : $mid - $i + 12; //Нужен кэп
		//$cur_year = ($mid - $i > 0)? $y : $y - 1;
		$month_days = cal_days_in_month(CAL_GREGORIAN, $cur_month, $cur_year);
		$month_name = MonthByNumber($cur_month);
		///
		$months_data[$i] = array(
			"number" => $cur_month, //Номер месяца
			"year" => $cur_year, //Год
			"days" => $month_days, //Число дней
			"unix" => 0, //Начало месяца в эре unix
			"name" => $month_name[0], //Полное название месяца
			"short_name" => $month_name[1], //Короткое название месяца
			"very_short_name" => $month_name[2], //Очень короткое название месяца
		);
	}
	
	for($i = 0; $i < 12; $i++)
	{
		$month_time += ($months_data[$i]['days']*3600*24) * $dir;
		$months_data[$i]['unix'] = $month_time;
	}
	
	return $months_data;
}

/** Возвращает год */
function GetYear($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$y = gmdate('Y', $time + ($utc*3600));
	return intval($y);
}

/** Возвращает месяц */
function GetMonth($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$m = gmdate('m', $time + ($utc*3600));
	
	return intval($m);
}

/** Возвращает день */
function GetDay($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$d = gmdate('d', $time + ($utc*3600));
	
	return intval($d);
}

/** Возвращает часы */
function GetHour($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$h = gmdate('G', $time + ($utc*3600));
	
	return intval($h);
}

/** Возвращает минуты */
function GetMinute($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$m = gmdate('i', $time + ($utc*3600));
	
	return intval($m);
}

/** Возвращает секунды */
function GetSecond($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$s = gmdate('s', $time + ($utc*3600));
	
	return intval($s);
}

/** Возвращает номер дня недели (понедельник = 0)*/
function GetDayOfWeek($time, $utc = -1000)
{
	if($utc == -1000)
	{
		$settings = GetSettings();
		$utc = $settings['timezone'];
	}
	//
	$w = gmdate('w', $time + ($utc*3600));
	
	$w_number = 0;
	if($w == 1)$w_number = 0; //Костыль костылёвский
	if($w == 2)$w_number = 1;
	if($w == 3)$w_number = 2;
	if($w == 4)$w_number = 3;
	if($w == 5)$w_number = 4;
	if($w == 6)$w_number = 5;
	if($w == 0)$w_number = 6;
	
	return $w_number;
}

/** Возвращает названия месяца по номеру*/
function MonthByNumber($number)
{
	if($number == 1)return array("Январь", "Янв", "Я");
	if($number == 2)return array("Февраль", "Фев", "Ф");
	if($number == 3)return array("Март", "Мар", "М");
	if($number == 4)return array("Апрель", "Апр", "А");
	if($number == 5)return array("Май", "Май", "М");
	if($number == 6)return array("Июнь", "Июн", "И");
	if($number == 7)return array("Июль", "Июл", "И");
	if($number == 8)return array("Август", "Авг", "А");
	if($number == 9)return array("Сентябрь", "Сен", "С");
	if($number == 10)return array("Октябрь", "Окт", "О");
	if($number == 11)return array("Ноябрь", "Ноя", "Н");
	if($number == 12)return array("Декабрь", "Дек", "Д");
}

/** Возвращает названия дня недели по номеру*/
function WeekDayByNumber($number)
{
	if($number == 0)return array("Понедельник", "Пн", "П");
	if($number == 1)return array("Вторник", "Вт", "В");
	if($number == 2)return array("Среда", "Ср", "С");
	if($number == 3)return array("Четверг", "Чт", "Ч");
	if($number == 4)return array("Пятница", "Пт", "П");
	if($number == 5)return array("Суббота", "Сб", "С");
	if($number == 6)return array("Воскресенье", "Вс", "В");
}

/** Возвращяет месяц в родительном падеже (1...12) */
function MonthByNumberGenitive($num)
{
	$months = array(
		"Января", "Февраля", "Марта", 
		"Апреля", "Мая", "Июня", 
		"Июля", "Августа", "Сентября", 
		"Октября", "Ноября", "Декабря", 
	);
	
	if(isset($months[$num - 1]))return $months[$num - 1];
	else return "Invalid month";
}

/**/

function isToday($time, $relative_time = 0)
{
	if($relative_time == 0) $relative_time = time();
	$RD = date("j", $relative_time);
	$RM = date("n", $relative_time);
	$RY = date("Y", $relative_time);
	
	$D = date("j", $time);
	$M = date("n", $time);
	$Y = date("Y", $time);
	
	return $RD == $D && $RM == $M && $RY == $Y;
}

function isYesterday($time, $relative_time = 0)
{
	if($relative_time == 0) $relative_time = time();
	return isToday($time, $relative_time - (24 * 60 * 60));
}

function isTomorrow($time, $relative_time = 0)
{
	if($relative_time == 0) $relative_time = time();
	return isToday($time, $relative_time + (24 * 60 * 60));
}

?>