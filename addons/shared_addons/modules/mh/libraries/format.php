<?php
if( !defined('BASEPATH'))
   exit('No direct script access allowed');

// ------------------------------------------------------------------------
/**
 * this library contains commonly used function for formating
 * text, currency dates and so on
 *
 *
 *
 * @category	Libraries
 * @author		tobias.koch@mmstc.de.com
 */

class format
{

   function __construct()
   {
	  $this->CI = &get_instance();
   }

   /* --------------------------------------------------------------------
	*function formats values in two dimensional array. this function is intendet to applay on result array for display purpose.
	*
	* @access 	public	
	* @param 	array	result set
	* @param 	array	function to apply on column eg $_columnCallbacka['id'] = 'number_format';
	* @param 	array	additional parameters in case callback function needs more params than value given with _data. limitation: the first parameter of callback function must always be the data which is intendet to be formatted.
	* @return 	array	
	*
	*
	* example: 
	* $data['0']['VERBRAUCH'] = 188.000;
	* $data['0']['ARTIKELTEXT'] = 'rohrschelle';
	*
	* $data['1']['VERBRAUCH'] = 79.230;
	* $data['1']['ARTIKELTEXT'] = 'magnetventil';

	* $function['ARTIKELTEXT'] = 'strtoupper';
	*
	* $function['VERBRAUCH'] = 'number_format';
	* $param['VERBRAUCH'] = array( 2, ',', '.');
	*
	*  $formatted = $this->col_call($data, $function,$param);
	*
	*/

   public function col_call($_data, $_columnCallback, $_param = FALSE)
   {
	  if(count($_data) == 0)
		 {
			return FALSE;
		 }
	  if(is_array($_data))
		 {	  

			foreach($_data as $key => $innerArr)
			   {
				  $_arr[$key] = $this->format_row($innerArr, $_columnCallback, $_param);
			   }

			return $_arr;
		 }
   }
   // --------------------------------------------------------------------
   // loop thru row given in previous function 
   private function format_row($_row, $_function, $_param)
   {
	  foreach($_row as $key => $value)
		 {

			if( array_key_exists($key, $_function))
			   {

				  if(function_exists($_function[$key]) && !isset($_param[$key]))
					 {

						$_row[$key] =  call_user_func($_function[$key], $_row[$key], $_row) ;
					 }
				  elseif(function_exists($_function[$key]) && isset($_param[$key]))
					 {

						array_unshift($_param[$key], $_row[$key]);
						$_row[$key] =  call_user_func_array($_function[$key],$_param[$key]);
					 }
			   }
		 }		 

	  return  $_row;
   }

   public function copy_col($_data, $_col2Copy, $_newName)
   {


	  if(is_array($_data))
		 {	  
			foreach($_data as $key => $value)
			   {

				  $_data[$key][$_newName] = $_data[$key][$_col2Copy];

			   }
		 }

	  return $_data;

   }


   // --------------------------------------------------------------------

   /**
	* function formats currency for displaying
	*
	* @return  	string	formatted currency value
	* @param  	decimal	currency
	* @param 	string 	iso code for localization
	* @return 	string	formatted currency
	*/
   public function currency($_mnt, $_iso = 'de')
   {

	  $locale_format = strtolower($_iso) . '_' . strtoupper($_iso);
	  setlocale(LC_MONETARY, $locale_format);
	  return money_format('%.2n', $_mnt);
   }

   // --------------------------------------------------------------------
   /**
	* this function converts Y-m-d dates to d.m.Y date
	*
	* @access 	public
	* @param 	string	date Y-m-d
	* @return 	string	date d.m.Y
	*/
   public function date2german($_date = '')
   {

	  $parts = explode('-', $_date);

	  if(!stristr($_date, ':'))
		 {
            //nur datum
			$Ddate = $parts['2'] . '.' . $parts['1'] . '.' . $parts['0'];
		 }
	  else
		 {
            //datum und uhrzeit
			$dayTime = explode(' ', $parts['2']);
			$Ddate = $dayTime['0'] . '.' . $parts['1'] . '.' . $parts['0'] . ' ' . $dayTime['1'];
		 }

	  return $Ddate;
   }
   // --------------------------------------------------------------------

   function dateDe2en($_german,$yearPreFx = '')
   {


	  $parts = explode('.',$_german);
	  $year = $parts['2'];

	  if($yearPreFx != '' && strlen($parts['2']) == 2)
		 {
			$year = $yearPreFx.$parts['2'];
		 } 

	  $en = $year.'-'.$parts['1'].'-'.$parts['0'];

	  return $en;
   }
   // --------------------------------------------------------------------
   /**
	* Function gives exact timestamp from given date and time
	*
	* @access	public			   
	* @param 	string 		date Y-m-d
	* @param 	string 		hour
	* @param 	string 		minute
	* @param 	string 		second
	* @return	string		timestamp
	*/
   public function getTimeFromDate($_date, $H = 0, $i = 0, $s = 0)
   {	  
	  $dateParts = explode('-',$_date);
	  $timespamp = mktime($H,$i,$s,$dateParts['1'],$dateParts['2'],$dateParts['0']);

	  return $timespamp;
   }

   public function make_time($_date)
   {	  
	  $date = date_parse_from_format('Y-m-d H:i:s', $_date); 
	  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

	  return $start_tStamp;
   }

   // --------------------------------------------------------------------
   /**
	*
	*/
   public function compTime($_time)
   {	  
	  return	  str_replace(':','',$_time);

   }


   /** --------------------------------------------------------------------
    *********************************************************************
    |GELDBETRÄGE FÜR AUSGABE FORMATIEREN   
    -------------------------------------------------------------------- */

   function displCurr($_curr,$_currSign = FALSE)
   {
	  if($_curr == '')
		 {
    		$_curr = 0;
		 }
	  $retVal =  number_format(str_replace(",",".",$_curr), 2, ',', '.').'&#160;&euro;';

	  if($_currSign == FALSE)
		 {
    		$retVal =  number_format(str_replace(",",".",$_curr), 2, ',', '.');
		 }

	  return $retVal;

   } 
   /* --------------------------------------------------------------------
    *********************************************************************
    Geldbeträge im DE Format in DEC für DB umwandeln
    --------------------------------------------------------------------*/
    

   function curr2Dec($_curr, $_fract = 2)
   {
	  $_currDec = str_replace(',','.',$_curr);

	  $firstPoint = strpos($_currDec,'.');
	  $lastPoint = strrpos($_currDec,'.');
	  if($firstPoint != $lastPoint)
		 {
    		$_currDec = substr_replace  ($_currDec,'',$firstPoint,1);
		 }

	  $retVal =  number_format($_currDec, $_fract);

	  return $retVal;

   } 

   // --------------------------------------------------------------------
   /**
	* TA-Nummer auf zehn stellen mit führendern nullen bringen
	*
	* @access 	public	
	* @param 	integer	
	* @return 	integer	
	*/
   public function ta_nr($_ta_nr)
   {
	  if (strlen($_ta_nr) < 10)
		 {
			$_ta_nr = str_pad($_ta_nr, 10, 0, STR_PAD_LEFT);
		 }

	  return $_ta_nr;
   }
   // --------------------------------------------------------------------
   /**
	* funktion gibt array mit monaten zurück
	* 
	* @access 	public	
	* @param 	voide	
	* @return 	array	monat als zahl mit fuehrender 0
	*/
   public function getMonths($_mode = 'm')
   {
	  $retVal = array();
	  $_months = range ('01','12');

	  foreach($_months as $numMonth)
		 {

			$_M = str_pad($numMonth, 2, 0, STR_PAD_LEFT);

			$_t_stamp_displ = $this->getTimeFromDate(date('Y',time()) . '-' . $_M . '-01');

			$_displ_M = date($_mode,$_t_stamp_displ);

			$retVal[$_M] = $_displ_M; 
		 }
	  return $retVal;
   }


   // --------------------------------------------------------------------
   /**
	* funktion gibt array mit jahren von heute bis param 1 plus oder minus zurück
	* 
	* @access 	public	
	* @param 	integer	offset range jahre die angezeigt werden ausgehend von diesem jahr
	* @param 	string	offset zukunft oder vergangenheit
	*/
   public function getYears($_rageOffset = '8', $_offsetDirection = '-')
   {

	  $currYear = date('Y', time());
	  if($_offsetDirection == '-')
		 {
			$startYear = $currYear - $_rageOffset;
		 }
	  else
		 {
			$startYear = $currYear + $_rageOffset;
		 }
	  $yearTmp = range($startYear, $currYear);
	  $flippedYears =  array_flip($yearTmp);

	  foreach($flippedYears as $_year => $key4Year)
		 {
			$years[$_year] = $yearTmp[$key4Year];
		 } 

	  return $years; 
   }

   // --------------------------------------------------------------------
   /**
	* Anforderungsnummer für materialbeschaffung formatieren
	*
	* @access 	public	
	* @param 	integer	
	* @return 	integer	
	*/
   public function anforderung_NR($_id)
   {
	  $len = 5;
	  if (strlen($_id) < 10)
		 {
			$_id = str_pad($_id, $len, 0, STR_PAD_LEFT);
		 }

	  return $_id;
   }


   // --------------------------------------------------------------------
   /**
	* nur reinen text zurückgeben, alle special chars und html tags entfernen
	* 
	* @access 	public	
	* @param 	string	
	* @return 	string	
	*/
   public function strip_all($des)
   {

	  // Strip HTML Tags
	  $clear = strip_tags($des);
	  // Clean up things like &amp;
	  $clear = html_entity_decode($clear);
	  // Strip out any url-encoded stuff
	  $clear = urldecode($clear);
	  // Replace non-AlNum characters with space
	  $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
	  // Replace Multiple spaces with single space
	  $clear = preg_replace('/ +/', ' ', $clear);
	  // Trim the string of leading/trailing space
	  $clear = trim($clear);


	  return $clear;
   }

   // --------------------------------------------------------------------
   /**
	* pad array value // str_pad auf alle elemente eines arrays anwenden 
	*
	* @access 	public	
	* @param 	array	
	* @return 	array	
	*/
   public function array_val_pad($_arr,$pad_length, $pad_string = " " ,$pad_type = STR_PAD_LEFT)
   {

	  $retVal = array();
	  foreach ($_arr as $key => $value) 
		 {
			$retVal[$key] = str_pad($value,$pad_length, $pad_string ,$pad_type); 
		 }
		
	  return $retVal;
   }
   // --------------------------------------------------------------------
   /**
	* sonderzeichen ersetzen 
	* 
	*/
   function convert_spcialChars($string)
   {

	  $string = str_replace("�", "ae", $string);
	  $string = str_replace("ä", "ae", $string);
	  $string = str_replace("�", "Ae", $string);
	  $string = str_replace("Ä", "Ae", $string);
	  $string = str_replace("�", "oe", $string);
	  $string = str_replace("ö", "oe", $string);
	  $string = str_replace("�", "Oe", $string);
	  $string = str_replace("Ö", "Oe", $string);
	  $string = str_replace("�", "ue", $string);
	  $string = str_replace("ü", "ue", $string);
	  $string = str_replace("�", "Ue", $string);
	  $string = str_replace("Ü", "Ue", $string);
	  $string = str_replace("�", "ss", $string);
	  $string = str_replace("ß", "ss", $string);
	  $string = str_replace("�", "", $string);

	  return $string;
   }

   // --------------------------------------------------------------------
   /**
	* object in array convertieren
	* 
	*/
   function object_to_array($object_arr) {

	  $retVal = array();
	  foreach($object_arr as $key => $object)
		 {
			$retVal[$key] = (array) $object;
		 }
	  return $retVal;
   }


   // --------------------------------------------------------------------
   /**
	* dropdown für stunden und minuten ausgeben
	*  $this->timeSelect(date('H:i',time()), 'start', 'holidays['.$key.'][%%]');
	* @return void
	* @author
	**/
   public function timeSelect($_val = '8:00', $_name_sfx = "start", $nameArr = '')
   {

	  $timeParts = explode(':', $_val);
	  $data['hrs'] = $timeParts[0];
	  $data['min'] = $timeParts [1];

	  $hrs = range(0, 23);
	  $min = $this->array_val_pad(array_combine(range(0, 50, 10), range(0, 50, 10)), 2, '0');

	  $name_start = 'hour_' . $_name_sfx;
	  $id_start = $name_start;

	  $name_end = 'min_' . $_name_sfx;
	  $id_end = $name_end;

	  if ($nameArr !== '') 
		 {

			$name_start = str_replace('%%', $name_start, $nameArr);
			$id_start .= '_' . sha1($name_start);
			$id_start = '';

			$name_end = str_replace('%%', $name_end, $nameArr);
			$id_end .= '_' . sha1($name_end);
			$id_end = '';

		 }

	  $retVal = form_dropdown($name_start, $hrs, $data['hrs'], 'id="' . $id_start . '" class="time_select"') . ':' . form_dropdown($name_end, $min, $data['min'], 'id="' . $id_end . '" class="time_select"');

	  return $retVal;
   }

   // --------------------------------------------------------------------
   
}

/* End of file formats.php */
/* Location: ./application/libraries/formats.php */
