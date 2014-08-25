<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is part of MH Module (Material Handling Urban Logistics)
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage MH(Material Handling) Module
 */
class factors extends Public_Controller
{
   protected $gridTable;

   public function __construct()
   {
	  parent::__construct();

	  $this->load->model('general_m');
	  $this->gridTable = new $this->general_m();
	  $this->gridTable->set_table('mh_calc_factors');

	  // Load the required classes

	  $this->lang->load($this->router->fetch_module());


	  $this->template
		 ->append_css('module::' . $this->router->fetch_module() . '.css')
		 ->append_css('module::jquery.datetimepicker.css')
		 ->append_css('module::tablesorter/style.css')
		 ->append_js('module::jquery.min.js')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::jquery.json-2.3.js')
		 ->append_js('module::jquery.cookie.js')
		 ->append_js('module::tablesorter/js/jquery.tablesorter.min.js')
		 ->append_js('module::tablesorter/js/tablesorter_widgets.js')
		 ->append_js('module::' . $this->router->fetch_module() . '.js');
   }

   // --------------------------------------------------------------------
   /**
	* All items
	*/
   public function index($offset = 0)
   {
	  // check permission
	  role_or_die('mh', 'mh_user','');

	  $grid = $this->getGrid();

	  $this->template
		 ->title('Kalkulationsfaktoren Verwalten')
		 ->set('legend','Kalkulationsfaktoren Verwalten')
		 ->set('editLink',$this->router->fetch_module() .'/settings/'. $this->router->fetch_class() . '/edit')
		 ->set('grid',$grid)
		 ->build('list');
   }


   // --------------------------------------------------------------------
   /**
	* show grid
	* 
	* @access 	private	
	* @param 	void	
	* @return 	string	html sortable grid
	* 
	*/
   private function getGrid()
   {
 	  $data = $this->format->object_to_array($this->gridTable->get_all());

	  if(is_array($data))
		 {
			$this->load->model('general_m');
			$this->countries = new $this->general_m();
			$this->countries->set_table('mh_countries');
			$countries = $this->format->object_to_array($this->countries->get_all());
	  
			foreach($data as $key => $item)
			   {

				  $land = $this->format->object_to_array($this->countries->get($item['country_id']));
 
				  $data[$key]['land'] = $land['de'][0];
				  $data[$key]['iso'] = $land['iso'][0];
				  $data[$key]['iso3'] = $land['iso3'][0];
				  $data[$key]['factor'] = $this->format->displCurr($data[$key]['factor']);
			   }
		 }

	  // grid konfigurieren und zurückgeben 
	  $conf['id'] = $this->router->fetch_class() . 'Grid';
	  $conf['column']['3']['style'] = 'text-align:right ';

	  $cols = array('land','iso','iso3','factor','id');

	  $grid = new sortable_grid($conf);
	  // $grid->copy_col('id','del_id');

	  $grid->set_heading(array('Land','ISO','ISO3', 'Kalk. Faktor',''));
	  $grid->set_edit_link(array('id'=>$this->router->fetch_module() .'/settings/'. $this->router->fetch_class() . '/edit'));
	  $gridData = $grid->arrangeCols($data,$cols);

	  return $grid->getGrid($gridData);



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
   public function edit($_edit_id = 0)
   {
	  $this->load->library('form_validation');

	  $data = $this->input->post('data');

	  $validation_rules = array(
								array(
									  'field'   => 'data[factor]',
									  'label'   => 'Kalkulationsfaktor',
									  'rules'   => 'required|callback_dec_check'
									  ),
								array(
									  'field'   => 'data[country_id]',
									  'label'   => 'Land',
									  'rules'   => 'required'
									  )
								);

	  $this->form_validation->set_rules($validation_rules);

	  $errors = '';

	  
	  if($this->input->post('submit'))
		 {
			$errors = $this->writeData($_edit_id);
		 }

	  $data = $this->gridTable->get_by('id', $_edit_id);
	  
	  $this->template
		 ->title('')
		 ->set('fields',$this->_form_fields($data))
		 ->set('backlink',$this->router->fetch_module() .'/settings/'. $this->router->fetch_class())
		 ->set('form_errors', $errors)
		 ->build('calc_factors');

   }

   public function dec_check($de_dec)
   {

	  $dec = $this->format->curr2Dec($de_dec);

	  $errMsg = FALSE;
	  if (preg_match('/^\d+\.\d+$/',$dec) != 1 || strlen($dec) > 5)
		 {
			$errMsg = 'Feld %s darf nur Dezimalzahlen mit max. 2 Vorkommastellen beinhalten. ';		
		 }
	  if ($dec < 0.00)
		 {
			$errMsg = 'Feld %s muss einen wert gr&ouml;&szlig;er 0,00 enthalten. ';		
		 }

	  if($errMsg !== FALSE
		 )
		 {

			$this->form_validation->set_message('dec_check',$errMsg );
			return FALSE;
		 }
	  else
		 {
			return TRUE;
		 }
   }

   // --------------------------------------------------------------------
   /**
	* validate and submit data
	* 
	* @access 	private	
	* @param 		
	* @return 		
	* 
	*/
   private function writeData($_edit_id)
   {
	  if ($this->form_validation->run() === FALSE)
		 {
			$errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';
			return $errors;	
		 }
	  else
		 {
			$data = $this->input->post('data');
			$data['factor'] = $this->format->curr2Dec($data['factor']);

			if($_edit_id < 1)
			   {
				  //insert

				  $_edit_id = $this->gridTable->insert($data);
				  redirect(current_url() . '/' . $_edit_id);
			
			   }
			else
			   {

				  $this->gridTable->update($_edit_id, $data);

				  redirect(current_url());
						
			   }


		 }


   } 
   // --------------------------------------------------------------------
   /**
	* detail formfields
	* 
	* @access 	private	
	* @param 	array 	
	* @return 	array	
	* 
	*/
   private function _form_fields($data = '')
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
			$formfields['delete'] = '<a href="' . $this->router->fetch_module() .'/settings/'. $this->router->fetch_class() . '/delete/' . $this->uri->segment(5) . '" class="button secondary tiny radius delBtn">L&ouml;schen&nbsp;<i class="fi-trash size-14">&nbsp;</i></a>&nbsp;';
		 }
	  $formfields['submit'] = '<button name="submit" value="1" type="submit" class="tiny radius">Speichern&nbsp;<i class="fi-save size-14">&nbsp;</i></button>';


	  $fields = $this->db->field_data('mh_calc_factors');
	  
	  foreach ($fields as $field)
		 {
			$field->name;
			$field->type;
			$field->max_length;
			$field->primary_key;

			$value = set_value('data[date]', isset($data[$field->name]) ? $data[$field->name] : '');
	
			// standard input	  
			$conf = array(
						  'name'        => 'data[' . $field->name . ']',
						  'id'          => $idPreFx  . $field->name,
						  'maxlength'   => $field->max_length,
						  'value'       => $value,
						  );
			if($field->name == 'factor')
			   {
				  $conf['value'] = $this->format->displCurr($value);
			   }


			$formfields[$namePreFx . $field->name] = form_input($conf);


			if($field->name == 'country_id')
			   {

				  $options = $this->drop_opt('mh_countries','id', 'de');
				  $formfields[$namePreFx . $field->name] = form_dropdown('data[' . $field->name . ']', $options, $value);

			   }
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
   public function delete($_edit_id)
   {
	  $this->gridTable->delete($_edit_id);

	  $this->db->where('calc_factors_id',$_edit_id);

	  redirect($this->router->fetch_module() .'/settings/'. $this->router->fetch_class());

   }
   // --------------------------------------------------------------------
   /**
	* dropdown options distances
	* 
	* @access 	private	
	* @param 	void	
	* @return 	array	
	* 
	*/
   private function drop_opt($_table, $_optVal, $_opt, $_postFX = '')
   {
	  $retVal = array();
	  $retVal[''] = 'Bitte w&auml;hlen';

	  $this->load->model('general_m');
	  $this->optTable = new $this->general_m();
	  $this->db->order_by($_opt, 'asc');
	  $this->optTable->set_table($_table);



	  foreach($this->optTable->get_all() as $key => $item)
		 {


			$retVal[$item->$_optVal] = $item->$_opt . $_postFX;			
		 }

	  return $retVal;
   }

   // --------------------------------------------------------------------
   
}
