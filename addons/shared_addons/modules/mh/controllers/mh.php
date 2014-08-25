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
	  $this->load->model('portage_ref_m');

	  $this->load->library('Googlemaps');

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
	  $info = '';
	  $errors = '';

	  $this->load->library('form_validation');

	  $data = $this->input->post('data');

	  $validation_rules = array(
								array(
									  'field'   => 'formdata[weight]',
									  'label'   => 'Gewicht in KG',
									  'rules'   => 'required|integer'
									  ),
								array(
									  'field'   => 'formdata[country_from]',
									  'label'   => 'Land von',
									  'rules'   => 'required'
									  ),
								array(
									  'field'   => 'formdata[location_from]',
									  'label'   => 'Abgangsort / PLZ',
									  'rules'   => 'required'
									  ),
								array(
									  'field'   => 'formdata[country_to]',
									  'label'   => 'Land nach',
									  'rules'   => 'required'
									  ),
								array(
									  'field'   => 'formdata[location_to]',
									  'label'   => 'Empfangsort / PLZ',
									  'rules'   => 'required'
									  )
								);



	  $this->form_validation->set_rules($validation_rules);
	  if ($this->input->post('submit'))
		 {
	  if ($this->form_validation->run() == FALSE)
		 {

			$errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';

		 }
	  else
		 {

	  $postFields = $this->input->post('formdata') ;

	  $from_country = $postFields['country_from'];
	  $from_location = $postFields['location_from'];

	  $to_country = $postFields['country_to'];
	  $to_location = $postFields['location_to'];

	  $dist  = 	  $this->get_addr_info($from_country . ' ' .$this->format->convert_spcialChars($from_location), $to_country . ' ' .$this->format->convert_spcialChars($to_location));
	  

	  $dist->vc_from = $this->get_vc($dist->origin_addr);
	  $dist->vc_to = $this->get_vc($dist->dest_addr);
	  

	  	  	  $dist->price = $this->portage_ref_m->getPrice($dist->distance->value / 1000, $postFields['weight']);
			  			  

		  $dist->post_fields = $postFields;
		  		  
	  $info = $this->load->view('partials/price_info.php',$dist,TRUE);
	  


		 }
		 }
	  // --------------------------------------------------------------------
	  $this->template
		 ->title($this->module_details['name'], 'the rest of the page title')
		 ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
		 ->set('formfields',$this->get_formfields())
		 ->set('info',$info)
		 ->set('errors',$errors)
		 ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
		 ->build('index')
		 ;
   }


   // --------------------------------------------------------------------
   /**
	* adresse für ausgabe formatieren
	* 
	*/
   function   get_vc($_addr)
   {
	  	  $tmp = explode(', ',$_addr);
	  if(count($tmp) == 3)
		 {
			$line1 = $tmp[0];  
			$line2 = $tmp[1];  
			$line3 = $tmp[2];  
		 }
	  if(count($tmp) == 4)
		 {
			$line1 = $tmp[0] . ' ' .$tmp[1];  
			$line2 = $tmp[2];  
			$line3 = $tmp[3];  
		 }

	  $retVal = '<ul class="vcard">
				   <li class="">' . $line1 . '</li>
				   <li class="fn street-address">' . $line2 . '</li>
				   <li class="locality">' . $line3 . '</li>
				 </ul>';
return  $retVal;
   }


   // --------------------------------------------------------------------
   function get_addr_info($_from, $_to)
   {
	  /*
	   get_addr_info('DE 65185', 'DE 60323');
	  */	  
	  $dist = $this->googlemaps->get_dist($_from, $_to);

	  //  $dist = $this->googlemaps->get_dist('DE 65185', 'mailand');


	  if($dist->status != 'OK')
		 {
			return $dist;
		 }	   
	  $retVal->distance = $dist->rows[0]->elements[0]->distance;
	  $retVal->status = $dist->rows[0]->elements[0]->status;
	  $retVal->origin_addr = $dist->origin_addresses[0];
	  $retVal->dest_addr = $dist->destination_addresses[0];

	  return $retVal;
   }

   // --------------------------------------------------------------------
   /**
	* formfelder
	* 
	* @access 		
	* @param 		
	* @return 		
	* 
	*/
   function get_formfields ()
   {
	  $postName = 'formdata';
	  $namePreFx = '';
	  $idPreFx = 'weight_';

	  $fields['country_from']->type = 'dropdown';
	  $fields['country_from']->options  = $this->general_m->get_active_countries();

	  $fields['country_to']->type = 'dropdown';
	  $fields['country_to']->options  = $this->general_m->get_active_countries();

	  $fields['location_from']->type = 'input';

	  $fields['location_to']->type = 'input';

	  $fields['weight']->type = 'input';



	  if(is_array($this->input->post($postName)))
		 {
			foreach($this->input->post($postName) as $name => $value)
			   {
				  $fields[$name]->value = $value;
			   }
		 }
	  foreach ($fields as $key => $field)
		 {

			$field->name = $key;
			$field->type;
			$value = '';
			if(isset($field->value))
			   {
				  $value = $field->value;

			   }

			
			// standard input	  
			$conf = array(
						  'name'        => $postName . '[' . $field->name . ']',
						  'id'          => $key  . $field->name,
						  'value'       => $value,
						  );

			$formfields[$namePreFx . $field->name] = form_input($conf);


			if($field->type == 'dropdown')
			   {


				  $formfields[$namePreFx . $field->name] = form_dropdown($postName . '[' . $field->name . ']', $field->options, $value);

			   }
		 }

	  return $formfields;	  
   }

   // --------------------------------------------------------------------
   
}
