<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage 	calc_base Module
 */
class calc_base extends Public_Controller
{
   protected $weights;

   public function __construct()
   {
	  parent::__construct();

	  $this->load->model('general_m');
	  $this->weights = new $this->general_m();
	  $this->weights->set_table('mh_weight_range');

	  // Load the required classes

	  $this->lang->load($this->router->fetch_module());


	  $this->template
		 ->append_css('module::' . $this->router->fetch_module() . '.css')
		 ->append_js('module::jquery.min.js')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::' . $this->router->fetch_module() . '.js');
   }

   /**
	* All items
	*/
   public function index($offset = 0)
   {



	  $all = $this->weights->get_all();
	  echo $this->db->last_query();
	  echo "<pre><code>";
	  print_r($all);
	  echo "</code></pre>";
	  
	  $this->template
		 ->title($this->module_details['name'], 'the rest of the page title')
		 ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
		 ->build('index')
		 ;
   }



  // --------------------------------------------------------------------
   /**
	* editieren und einfügen neuer gewichtsbereiche
	* 
	* @access 	public	
	* @param 	int	gewicht id
	* @return 	output	
	* 
	*/
   public function edit_weights($_weight_id = 0)
   {
	  $this->load->library('form_validation');

	  $data = $this->input->post('data');

	  $validation_rules = array(
								array(
									  'field'   => 'data[kg]',
									  'label'   => 'Gewicht in KG',
									  'rules'   => 'required'
									  )
								);

	  $this->form_validation->set_rules($validation_rules);

	  $errors = '';

	  
	  if($this->input->post('submit'))
		 {

			if ($this->form_validation->run() === FALSE)
			   {
				  $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';
			   }
			else
			   {
				  $data = $this->input->post('data');
				  if($_weight_id < 1)
					 {
						//insert

						$_weight_id = $this->weights->insert($data);
						redirect(current_url() . '/' . $_weight_id);
			
					 }
				  else
					 {

						$this->weights->update($_weight_id, $data);

						redirect(current_url());
						
					 }


			   }

		 }

	  $data = $this->weights->get_by('id', $_weight_id);
	  
	  $this->template
		 ->title('')
		 ->append_js('module::calendar.js')
		 ->set('fields',$this->weights_form_fields($data))
		 ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
		 ->set('form_errors', $errors)
		 ->build('weight_range');

   }

 // --------------------------------------------------------------------
   /**
	* weights detail formfields
	* 
	* @access 	private	
	* @param 	array 	
	* @return 	array	
	* 
	*/
   private function weights_form_fields($data = '')
   {
	  if(is_object($data))
		 {
			$data = (array) $data;
		 }	

	  $namePreFx = '';
	  $idPreFx = 'weight_';

	  if($this->input->post('data'))
		 {
			$data = $this->input->post('data');
		 }
	  else
		 {
			$data = $data;
		 }


	  $formfields['open'] = form_open();
	  $formfields['close'] = form_close();

	  $formfields['delete'] = '';
	  if($this->uri->segment(4) != "")
		 {
			$formfields['delete'] = '<a href="' . $this->router->fetch_module() .'/'. $this->router->fetch_class() . '/delete/' . $this->uri->segment(4) . '" class="button secondary tiny radius delBtn">L&ouml;schen&nbsp;<i class="fi-trash size-14">&nbsp;</i></a>&nbsp;';
		 }
	  $formfields['submit'] = '<button name="submit" value="1" type="submit" class="tiny radius">Speichern&nbsp;<i class="fi-save size-14">&nbsp;</i></button>';


	  $fields = $this->db->field_data('mh_weight_range');

	  foreach ($fields as $field)
		 {
			$field->name;
			$field->type;
			$field->max_length;
			$field->primary_key;


			$value = set_value('data[date]', isset($data[$field->name]) ? $data[$field->name] : '');
	
			if($field->name == 'lic_number')
			   {
				  $value = strtoupper($value);
			   }
			// standard input	  
			$conf = array(
						  'name'        => 'data[' . $field->name . ']',
						  'id'          => $idPreFx  . $field->name,
						  'maxlength'   => $field->max_length,
						  'value'       => $value,
						  );

			$formfields[$namePreFx . $field->name] = form_input($conf);

		 } 

	  return $formfields;
   } 

// --------------------------------------------------------------------
   /**
	* delete 
	* 
	* @access 	public	
	* @param 	integer	
	* @return 	void	
	* 
	*/
   public function delete($_weight_id)
   {
	  $this->weights->delete($_weight_id);
	  redirect($this->router->fetch_module() .'/'. $this->router->fetch_class());

   }
// --------------------------------------------------------------------
   
}
