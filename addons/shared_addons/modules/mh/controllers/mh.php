<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage 	mh Module
 */
class mh extends Public_Controller
{
   public function __construct()
   {
	  parent::__construct();

	  $this->load->model('general_m');

	  // Load the required classes

	  $this->lang->load($this->router->fetch_module());


	  $this->template
		 ->append_css('module::' . $this->router->fetch_module() . '.css')
		 ->append_js('module::jquery.min.js')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::' . $this->router->fetch_module() . '.js');

	  Asset::add_path('mh', base_url(SHARED_ADDONPATH .  'modules/' .$this->router->fetch_module()) . '/') ;

   }

   /**
	* All items
	*/
   public function index($offset = 0)
   {
	  echo Asset::img('mh::weights_ico.png', 'imagename', array('width' => 50, 'height' => 50));

	  $this->getCountries();

	  $this->template
		 ->title($this->module_details['name'], 'the rest of the page title')
		 ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
		 ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
		 ->build('index')
		 ;
   }



   function getCountries()
   {

	  $this->db->select('mh_countries.*');
	  $this->db->from('mh_countries');
	  $this->db->join('mh_calc_factors', 'mh_countries.id = mh_calc_factors.country_id');
	  $this->db->order_by('de', 'asc');
	  $query = $this->db->get();
	  $result = $query->result();

	  $options[''] = 'Bitte w&auml;hlen';
	  foreach($result as $key => $item)
		 {
			$options[$item->iso3] = $item->de .  ' (' . $item->iso. ')';
			}
	  echo "<pre><code>";
	  print_r($options);
	  echo "</code></pre>";
	  
   }

}
