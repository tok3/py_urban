<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar_week {


   /**
	  
	  $event->format = 'H:i';
	  $event->start = '12:00';
	  $event->text = 'essen';

	  $cal->set_event($event); 



	  $event->format = 'Y-m-d H:i';
	  $event->start = '2014-03-24 12:30';
	  $event->text = '<span style="color:red">2014-03-24 12:30</span>';
	  $cal->set_event($event); 

	  $event->format = 'Y-m-d H:i';
	  $event->start = '2014-03-26 15:30';
	  $event->end = '2014-03-26 18:30';
	  $event->text = '<span style="background-color:red; width:100px;">so&nbsp;&nbsp;&nbsp;</span>';
	  $cal->set_event($event); 

	  $event->format = 'Y-m-d H:i';
	  $event->start = '2014-03-24 9:30';
	  $event->end = '2014-03-24 10:30';
	  $event->text = '<span style="background-color:red; width:100px;">&nbsp;&nbsp;&nbsp;</span>';
	  $cal->set_event($event); 


	  $event->day = '6';
	  $event->text = '<span style="background-color:yellow; width:100px;">&nbsp;&nbsp;&nbsp;</span>';
	  $cal->set_event($event); 

	  $event->day = '0';
	  $event->text = '<span style="background-color:orange; width:100px;">&nbsp;&nbsp;&nbsp;</span>';
	  $cal->set_event($event); 


	  // jeden tag 12 - 13 h 
	  $event->format = 'H:i';
	  $event->start = '12:00';
	  $event->end = '13:00';

	  $event->text = '<span style="color:red">12 - 13 h</span>';
	  $cal->set_event($event); 


	  $event->format = 'H:i';
	  $event->start = '10:00';

	  $event->text = '<span style="color:red">10:10</span>';
	  $cal->set_event($event); 

	  $event->start = '1395923400';
	  $event->end = '1395930600';

	  $event->text = 'testen';
	  $cal->set_event($event); 


   */

   var $CI;
   var $lang;
   //var $local_time;
   var $template		= '';
   var $start_day		= 'sunday';
   var $month_type 	= 'long';
   var $day_type		= 'abr';
   var $weekdaysOnly	= FALSE;
   var $week_days = Array();
   var $date = '';
   var $url		= ''; 
   var $additionalSegments		= ''; 

   var $calendArr = array();
   var $calHeading = array();
   var $timeCol = '';
   var $fill = array();  
   var $setEvent = array();
   private $events = array();  
   private $day_view_start_hour = 8;
   private  $day_view_end_hour = 20;
   private  $slot_size = 0.5; // factor hour 0.5 slot = half  hour 
   private $dls_shift = 0; // wenn 1 dann ist in zeitumstellung in aktueller woche
   private $current_week; 
   private $addClass; 
   private $addAttributes; 

   /**
	* Constructor
	*
	* Loads the calendar language file and sets the default time reference
	*
	* @access	public
	*/
   function Calendar_week($config = array())
   {
	  $this->CI =& get_instance();
   $this->CI->load->library('ramps');
			$this->ramps = new $this->CI->ramps;		
	  if ( ! in_array('calendar_lang'.EXT, $this->CI->lang->is_loaded, TRUE))
		 {
			$this->CI->lang->load('calendar');
		 }
		
	  if (count($config) > 0)
		 {
			$this->initialize($config);
		 }
		
	  if ($this->date==null){
		 $this->date = date(mktime());
	  }
		
	  $this->set_week();
		
	  log_message('debug', "Calendar_week Class Initialized");
   }
	
   /**
	* Initialize the user preferences
	*
	* Accepts an associative array as input, containing display preferences
	*
	* @access	public
	* @param	array	config preferences
	* @return	void
	*/	
   function initialize($config = array())
   {

	  foreach ($config as $key => $val)
		 {
			if (isset($this->$key))
			   {
				  $this->$key = $val;
			   }
		 }

   }
	
   function set_range(){
	  switch ($this->start_day){
	  case 'sunday':
		 return range(0,6);
		 break;

	  case 'monday':
		 return range(1,7);
		 break; 
	  case 'tuesday':
		 return range(2,8);
		 break;

	  case 'wednesday':
		 return range(3,9);
		 break;

	  case 'thursday':
		 return range(4,10);
		 break;

	  case 'friday':
		 return range(5,11);
		 break;

	  case 'saturday':
		 return range(6,12);
		 break;				
	  }
	  return range(0,6);
   }
	
   function set_week()
   {
	  
	  $week_days = $this->set_range();
	  $week_day = date('w',$this->date);

	  foreach($week_days as $key=>$day)
		 {
			if($day == $week_day)
			   {
				  $week_days[$key] = $this->date;
			   } 
			elseif($day < $week_day)
			   {
				  $week_days[$key] = strtotime('-'.$week_day+$day.' day',$this->date);
			   } 
			elseif($day > $week_day)
			   {
				  $week_days[$key] = strtotime('+'.$day-$week_day.' day',$this->date);
			   }
		 }

	  $this->week_days = $week_days;

	  $before = strtotime('-4 day',$this->week_days[0]);
	  $after = strtotime('+1 day',$this->week_days[count($this->week_days)-1]);

	  if(date('I',$before)!= date('I',$after - 3600))
		 {
			$this->dls_shift = 1;
	  
			$this->day_view_start_hour -= 1;
			$this->day_view_end_hour-= 1; 
		 }
   }
	
   function get_week()
   {
	  return $this->week_days ;
   }
	

   // --------------------------------------------------------------------

   /**
	* Generate the calendar
	*
	* @access	public
	* @return	string
	*/
   function generate($data=''){
	  $days = $this->get_day_names();
	  $months = $this->get_month_name();
		
	  $tmpHeader = '';
	  $tmpContent = '';

	  $this->current_year = date('Y', $this->week_days[0]);
	  for ($i=0;$i<count($this->week_days);$i++)
		 {

			if ( ((date('l', $this->week_days[$i])=='Saturday') || (date('l', $this->week_days[$i])=='Sunday')) && $this->weekdaysOnly == TRUE ){
			   $tmpHeader .= '<td>'.$days[date('w', $this->week_days[$i])].' '.date('d', $this->week_days[$i]).' ' . $months[date('n', $this->week_days[$i])-1] .'</td>';
			   $tmpContent .= '<td><div class="container" style="background: #ccc;"></div></td>';				
			} 
			else 
			   {
				  $tmpHeader .= '<td class="contCol">'.$days[date('w', $this->week_days[$i])].' '. date('d', $this->week_days[$i]) .'. ' . $months[date('n', $this->week_days[$i])-1] .'</td>';
				  $tmpContent .= '<td class="contCol"><div class="container">'.$data[$this->week_days[$i]].'</div></td>';
			   }
		 }
		
	  $before = strtotime('-4 day',$this->week_days[0]);
	  $after = strtotime('+1 day',$this->week_days[count($this->week_days)-1]);
	  $this->current_week = date('W',$before);

	  $template = '
		<span id="displayCalendarBefore">

		<a class="fi-arrow-left  size-24" href="' . site_url($this->url . date('Y',$before).'/'.date('m',$before).'/'.date('d',$before) . $this->additionalSegments) . '"></a>
		</span><span>KW' . $this->current_week .' '. $this->current_year.  '</span> 

	<span id="displayCalendarAfter">
		<a class="fi-arrow-right  size-24" href="' . site_url($this->url . date('Y',$after).'/'.date('m',$after).'/'.date('d',$after) . $this->additionalSegments) . '"></a>
		</span>	

	<span id="calendar">
		<table class="calendarWeek" cellpadding="0" cellspacing="0">
		<tr><td  class="timeCol">Zeit</td>'.$tmpHeader.'</tr>
		<tr><td class="timeCol">' . $this->timeCol . '</td>'.$tmpContent.'</tr>
		</table>
		</span>
	';
	  return $template ;
   }	
	
   /**
	* Get Month Name
	*
	* Generates a textual month name based on the numeric
	* month provided.
	*
	* @access	public
	* @param	integer	the month
	* @return	string
	*/
   function get_month_name()
   {
	  if ($this->month_type == 'short')
		 {
			$month_names = array('01' => 'cal_jan', '02' => 'cal_feb', '03' => 'cal_mar', '04' => 'cal_apr', '05' => 'cal_may', '06' => 'cal_jun', '07' => 'cal_jul', '08' => 'cal_aug', '09' => 'cal_sep', '10' => 'cal_oct', '11' => 'cal_nov', '12' => 'cal_dec');
		 }
	  elseif($this->month_type == 'de')
		 {
			$month_names = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mrz', '04' => 'Apr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Dez');

		 }
	  else 
		 {
			$month_names = array('01' => 'cal_january', '02' => 'cal_february', '03' => 'cal_march', '04' => 'cal_april', '05' => 'cal_may', '06' => 'cal_june', '07' => 'cal_july', '08' => 'cal_august', '09' => 'cal_september', '10' => 'cal_october', '11' => 'cal_november', '12' => 'cal_december');
		 }
		
	  $months = array();
	  foreach ($month_names as $val)
		 {			
			$months[] = ($this->CI->lang->line($val) === FALSE) ? ucfirst($val) : $this->CI->lang->line($val);
		 }

	  return $months;
   }	
	
   /**
	* Get Day Names
	*
	* Returns an array of day names (Sunday, Monday, etc.) based
	* on the type.  Options: long, short, abrev
	*
	* @access	public
	* @param	string
	* @return	array
	*/
   function get_day_names($day_type = '')
   {
	  if ($day_type != '')
		 $this->day_type = $day_type;

	  if ($this->day_type == 'long')
		 {
			$day_names = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		 } 
	  elseif($this->day_type == 'short')
		 {
			$day_names = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
		 } 
	  elseif($this->day_type == 'de')
		 {
			$day_names = array('So,', 'Mo,', 'Di,', 'Mi,', 'Do,', 'Fr,', 'Sa,');
		 } 
	  else 
		 {
			$day_names = array('su', 'mo', 'tu', 'we', 'th', 'fr', 'sa');
		 }

	  $days = array();
	  foreach ($day_names as $val)
		 {			
			$days[] = ($this->CI->lang->line('cal_'.$val) === FALSE) ? ucfirst($val) : $this->CI->lang->line('cal_'.$val);
		 }
	  return $days;
   }		

   // --------------------------------------------------------------------
   /**
	* wochencalender mit zeitfenstern und columne mit zeit ausgeben 
	* 
	* @access 	public	
	* @param 	void	
	* @return 	string	calendar
	* 
	*/
   public function get_calendar()
   {

	  $week = $this->get_week();
	  $arr_Data = Array($week);
	  
	  for ($i=0;$i<count($week);$i++)
		 {

			$arr_Data[$week[$i]] = $this->getDaySegments($week[$i]);
		 }
	  return $this->generate($arr_Data);
   }


   // --------------------------------------------------------------------
   /**
	* setter events befüllen
	* 
	* @access 	public 		
	* @param  	object or array with objects		
	* @return 	void	
	* 
	*/
   function set_event($_event)
   {
	  $ev = clone  $_event;
	  foreach ($_event as $key => $value) 
		 {
            unset($_event->$key);
		 }
	  unset($_event);
	  array_push($this->events, $ev);

   }

   // --------------------------------------------------------------------
   /**
	* get array with time slot of certain day
	* one array per day   
	*
	* @param array
	* @return array   
	**/

   function getDaySegments($_day, $_markStart = '', $_markEnd = '')
   {

	  $dayStartTime = $this->day_view_start_hour;
	  $dayEndTime = $this->day_view_end_hour;
	  $hour = 3600;
	  $slot = $hour * $this->slot_size; // slot halbe stunde
	  $retVal = array();

	  $tStampStart = $_day + ($dayStartTime * $hour);
	  $tStampEnd = $_day + ($dayEndTime * $hour);
      
	  if($this->dls_shift == 1) // anzeigekorrktur in woche mit zeitumstellung
		 {

			if(date('I',$tStampStart) ==0)
			   {
				  $tStampStart += 3600;
				  $tStampEnd += 3600;
			   }
		 }  
  
     
	  $i=$tStampStart;

	  $retVal = '';
	  $it = 1;
	  $this->timeCol = '';

	  while($i < $tStampEnd) 
		 { 
			if($it % 2 == 0)
			   {

				  $cl_eo = 'even';
			   }
			else
			   {
				  $cl_eo = 'odd';   
			   }
			

			$celContent = $this->addInfo($i);
			$addClass = ' ' . trim($this->addClass);
			$addAttribute = ' ' . trim($this->addAttributes);

			$retVal .= '<div class="timeSlot '. $cl_eo .  $addClass .'" data-slot_tstampStart="'.$i.'" id="' . $i .'" '. $addAttribute.'>'. $celContent .'&nbsp;</div>';

			// columne ganz links mit uhrzeit 
			$this->timeCol  .= '<div class="'. $cl_eo . '">' . date('H:i',$i) . '</div>';


            $i += $slot;
			++$it;
		 }
	  return $retVal ;
   }


   // --------------------------------------------------------------------
   //schauen ob info/text oder sonst ein eintrag für zelle existiert  
   function addInfo($_tStamp)
   {

  $slot = 3600 * $this->slot_size; 

	
	  $retVal = '';
	  $this->addClass = '';
	  $this->attributes = '';

	  foreach($this->events as $key => $item)
		 {

			if(isset($item->format)) //wenn datum formatiert übergeben wird format wie date bsp Y-m-D H:i:s
			   {
				  $current = date($item->format,$_tStamp);

				  $current = $this->get_timestamp($item->format,date($item->format, $_tStamp));


				  $current = $this->get_timestamp($item->format,date($item->format, $_tStamp));
			
				  $start_stamp = $this->get_timestamp($item->format,$item->start);

				  $slotEnd = ($current + $slot) - 1;
			   }
			else // wenn timestamp übergeben wird
			   {
				  $current = $_tStamp; 

				  if(isset($item->start))
					 {
						$start_stamp = $item->start;
					 }				  
		
			   }


			// --------------------------------------------------------------------
			/**
			 * ab hier unterscheidung der eintragsarten
			 * 
			 */

			// nur start eintrag in einem slot
			if(!isset($item->end) && !isset($item->day))
			   {
				  if(($start_stamp >= $current) && ($start_stamp <= $slotEnd))
					 {
						$retVal = $item->text;
						$this->additionals($item);
					 }
			   }


			
			// --------------------------------------------------------------------
			/**
			 * ganzer wochen tag wochentag 	  

			 $event->day = '0';
			 $event->text = '<span style="background-color:orange; width:100px;">&nbsp;&nbsp;&nbsp;</span>';
			 $cal->set_event($event); 
			 * 
			 */
			if(isset($item->day) &&  (date('w', $_tStamp) == $item->day))
			   {

				  $retVal = $item->text;
				  $this->additionals($item);

			   }

			
			// --------------------------------------------------------------------
			/** 
			 * 	zeitspanne jeden tag 

			 jeden tag 12 - 13 h 
			 $event->format = 'H:i';
			 $event->start = '12:00';
			 $event->end = '13:00';
			 $event->text = '<span style="color:red">12 - 13 h</span>';
			 $cal->set_event($event); 

			 * 
			 */

			if(isset($item->end))
			   {
				  if(isset($item->format)) // wg expliziten timestampt nochmal prüfen !!!
					 {
						$end_stamp = $this->get_timestamp($item->format,$item->end);
					 }
				  else
					 {
						$end_stamp = $item->end;
					 }
				  if(($start_stamp <= $current) && ($end_stamp > $current))
					 {
						$retVal = $item->text;
						$this->additionals($item);

					 }
			   }


			// mit start und ende wiederkehrend wochentag
			/**
			 *  	  
			 $event->format = 'w H:i';
			 $event->start = '12:00';
			 $event->end = '13:00';
			 $event->day_start = '0';
			 $event->day_end = '2	';
			 $event->text = '';

			 $cal->set_event($event);


			 * 
			 */
			if(isset($item->day_start) && isset($item->day_end))
			   {

				  if((date('w', $_tStamp) >= $item->day_start) && (date('w', $_tStamp) <= $item->day_end))
					 {
						$i_start =  $this->get_timestamp('H:i', $item->start);
						$i_end =  $this->get_timestamp('H:i', $item->end);

						$curr = $this->get_timestamp('H:i', date('H:i',$_tStamp));
						if($i_start <= $curr && $i_end > $curr)
						   {
							  $retVal = $item->text;

							  $this->time_start = $_tStamp;
							  $retVal .= $this->additionals($item);
						   }
					 }

			   }

 
		 }
  

	  return $retVal;
   }

   // --------------------------------------------------------------------
   /**
	* additionals to cell
	* 
	* @access 	private	
	* @param 	opject	
	* @return 	void	
	* 
	*/
   private function additionals($events)
   {
	  
	  /**
	   * additionals
	   * 
	   */
	  if(isset($events->class))
		 {
			$this->addClass = $events->class; // zusaetzliche css classe zuweisen
		 }
	  if(isset($events->add_class))
		 {
			$this->addClass .= ' ' . $events->add_class; // zusaetzliche css classe zuweisen verketten falls schon zusätzliche zugewiesen
		 }

	  if(isset($events->attributes))
		 {
			$this->addAttributes = $events->attributes; // zusaetzliche attribute z.b. html 4 data-
		 }
	  if(isset($events->ckeckAvail))
		 {

			// @parem $events->ckeckAvail == site_id // standort_id
			$this->ramps->is_disposable($events->ckeckAvail,$this->time_start);
			$slotInfo = $this->ramps->get_slot_info();

			if($slotInfo[0]['booked'] == 1)
			   {
				  $event->text = 'sadfd';
				  $this->addClass .= ' booked';
				  return  '<i class="fi-lock">&nbsp;</i>'; 			
			   }
		 }
   }

   function isValidTimeStamp($timestamp)
   {
	  return ((string) (int) $timestamp === $timestamp) 
		 && ($timestamp <= PHP_INT_MAX)
		 && ($timestamp >= ~PHP_INT_MAX);
   }
   // --------------------------------------------------------------------
   /**
	* timespamp aus formatiertem datum erzeugen 
	* 
	* @access 		
	* @param 		
	* @return 		
	* 
	*/
   function get_timestamp($_format, $_date)
   {

	  $date = date_parse_from_format($_format, $_date); 
	  $timestamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

	  return $timestamp;
   }
}

// END Calendar_week class

/* End of file Calendar_week.php */
/* Location: ./system/application/libraries/Calendar_week.php */