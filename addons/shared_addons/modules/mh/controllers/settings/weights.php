<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is part of MH Module (Material Handling Urban Logistics)
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage MH(Material Handling) Module
 */
class weights extends Public_Controller
{
   protected $gridTable;

   public function __construct()
   {
	  parent::__construct();

	  $this->load->model('general_m');
	  $this->gridTable = new $this->general_m();
	  $this->gridTable->set_table('mh_weight_range');

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
		 ->title('Gewichtsbereiche Verwalten')
		 ->set('legend','Gewichtsbereiche Verwalten')
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
			foreach($data as $key => $item)
			   {
				  //				  $data[$key]['kg'] = number_format($item['kg'],0, ",", ".") . ' KG';

			   }
		 }

	  // grid konfigurieren und zurückgeben 
	  $conf['id'] = $this->router->fetch_class() . 'Grid';
	  $conf['column']['0']['style'] = 'text-align:right ';

	  $cols = array('kg','id');

	  $grid = new sortable_grid($conf);
	  // $grid->copy_col('id','del_id');

	  $grid->set_heading(array('KG bis',''));
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
									  'field'   => 'data[kg]',
									  'label'   => 'Gewicht in KG',
									  'rules'   => 'required|integer'
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
		 ->build('weight_range');

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


	  $fields = $this->db->field_data('mh_weight_range');

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
   public function delete($_edit_id)
   {
	  $this->gridTable->delete($_edit_id);

	  $this->db->where('weight_range_id',$_edit_id);
	  $this->db->delete('mh_portage_reference',$_edit_id);

	  redirect($this->router->fetch_module() .'/settings/'. $this->router->fetch_class());

   }
   // --------------------------------------------------------------------
   
}
