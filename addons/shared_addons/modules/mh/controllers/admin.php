<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is a ramstrg module for PyroCMS
 *
 * @author      Tobias C. Koch - mms&c.
 * @website     http://mmsetc.de
 * @package     PyroCMS
 * @subpackage  Ramstrg Module
 */
class Admin extends Admin_Controller
{

   protected $section = 'sites';

   public function __construct()
   {
	  parent::__construct();

	  // Load all the required classes
	  $this->load->model('ramstrg_sites_m');
	  $this->load->model('ramstrg_sites_business_hours_m');
	  $this->load->model('ramstrg_sites_holidays_m');

	  $this->load->library('form_validation');
	  $this->lang->load('ramstrg');
	
	  // Set the validation rules
	  $this->item_validation_rules = array(
										   array(
												 'field' => 'name', 
												 'label' => 'Name', 
												 'rules' => 'trim|max_length[100]|required'
												 ),
										   array(
												 'field' => 'slug', 
												 'label' => 'Slug', 
												 'rules' => 'trim|max_length[100]'
												 ),
										   array(
												 'field' => 'str',
												 'label' => 'Strasse',
												 'rules' => 'trim|max_length[155]|required'
												 ),
										   array(
												 'field' => 'nr',
												 'label' => 'Nr', 
												 'rules' => 'trim|max_length[4]'
												 ),
										   array(
												 'field' => 'plz',
												 'label' => 'PLZ',
												 'rules' => 'trim|max_length[5]|required'
												 ),
										   array(
												 'field' => 'ort',
												 'label' => 'Ort',
												 'rules' => 'trim|max_length[155]|required'
												 )
										   );

	  // We'll set the partials and metadata here since they're used everywhere
	  $this->template->append_js('module::admin.js')->append_css('module::admin.css');
   }

   // --------------------------------------------------------------------
   /**
	* List all Sites/ Standorte
	*/
   public function index()
   {
	  // here we use MY_Model's get_all() method to fetch everything
	  $items = $this->ramstrg_sites_m->get_all();


	  // Build the view with ramstrg/views/admin/items.php
	  $this->template
		 ->title($this->module_details['name'])
		 ->set('items', $items)
		 ->build('admin/items');
   }

   // --------------------------------------------------------------------
   /**
	* create site / standort
	*
	* @return void
	* @author
	**/

   public function create()
   {

	  // Set the validation rules from the array above
	  $this->form_validation->set_rules($this->item_validation_rules);

	  // check if the form validation passed
	  if ($this->form_validation->run())
		 {

			// See if the model can create the record
			if ($this->ramstrg_sites_m->create($this->input->post())) 
			   {
				  $insertID = $this->db->insert_id();
				  // All good...
				  $this->session->set_flashdata('success', lang('ramstrg.success'));
				  redirect('admin/ramstrg/edit/' . $insertID);
			   } 
			// Something went wrong. Show them an error
			else 
			   {
				  $this->session->set_flashdata('error', lang('ramstrg.error'));
				  redirect('admin/ramstrg/create');
			   }
		 }

	  $ramstrg = new stdClass;
	  foreach ($this->item_validation_rules as $rule) 
		 {
			$ramstrg->{$rule['field']} = $this->input->post($rule['field']);
		 }
	  // Build the view using ramstrg/views/admin/form.php
	  $this->template->title($this->module_details['name'], lang('ramstrg.new_item'))
		 ->set('ramstrg', $ramstrg)->build('admin/form');
   }

   // --------------------------------------------------------------------
   /**
	* Standort=Site  editieren
	*
	* @return void
	* @author
	**/
   public function edit($id = 0)
   {

	  $ramstrg = $this->ramstrg_sites_m->get($id);
	  // business hours
	  $where = array('site_id', $id);

	  // geschäftszeiten holen 
	  $site_business_hours = $this->ramstrg_sites_business_hours_m->getBH($id);
	  $bh_inputs = $this->_getBH_inputs($id);

	  // betriebsferien holen 
	  $site_holidays = $this->_getHoliday_inputs($id);

	  // Set the validation rules from the array above
	  $this->form_validation->set_rules($this->item_validation_rules);

	  // check if the form validation passed
	  if ($this->form_validation->run()) {


		 $this->_updBusinessHours($_POST['bh']);
		 $this->_delBusinessHours($_POST['del_bh']);

		 $action = $_POST['btnAction'];

		 unset($_POST['btnAction']); // action button
		 unset($_POST['bh']); // fld mit geschäftszeiten löschen
		 unset($_POST['del_bh']); // action button in geschäftszeiten list

		 $this->_updHolidays($_POST['holidays']);
		 $this->_delHolidays($_POST['del_holidays']);

		 unset($_POST['holidays']); // fld mit geschäftszeiten löschen
		 unset($_POST['del_holidays']); // action button in geschäftszeiten list

		 // See if the model can create the record
		 if ($this->ramstrg_sites_m->update($id, $this->input->post())) 
			{
			   // All good...
			   $this->session->set_flashdata('success', lang('ramstrg.success'));

			   ($action == 'save_exit') ? redirect('admin/ramstrg') : redirect('admin/ramstrg/edit/'.$id);  
			} 
		 else  // Something went wrong. Show them an error
			{
			   $this->session->set_flashdata('error', lang('ramstrg.error'));
			   redirect('admin/ramstrg/edit');
			}
	  }
	  // Build the view using ramstrg/views/admin/form.php
	  $this->template->title($this->module_details['name'], lang('ramstrg.edit'))
		 ->set('business_hours', $site_business_hours)
		 ->set('bh_inputs', $bh_inputs)
		 ->set('holidays', $site_holidays)
		 ->set('ramstrg', $ramstrg)
		 ->build('admin/form');
   }


   // --------------------------------------------------------------------
   /**
	* create business hours // ajax called
	*
	* @return void
	* @author tobias
	**/
   public function create_bh()
   {

	  $days = array_combine(range(0, 6), $this->lang->line('ramstrg:day_names'));
	  $data['d_start'] = form_dropdown('day_start', $days, 1, 'class="day_select"');
	  $data['d_end'] = form_dropdown('day_end', $days, 5, 'class="day_select"');
	  $data['t_start'] = $this->format->timeSelect((date('H:i', time())), 'start');
	  $data['t_end'] = $this->format->timeSelect(date('H:i', time()), 'end');

	  // Render the view and echo for ajax 
	  $form = $this->load->view('admin/form_create_bh', $data, true);
	  echo $form;
   }


   // --------------------------------------------------------------------
   /**
	* create business holidays // ajax 
	*
	* @return void
	* @author tobias
	**/
   public function create_holidays()
   {


	  $data['d_start'] = form_input('date_start', '', 'maxlength="10" class="datepicker dpBH"');
	  $data['d_end'] = form_input('date_end', '', 'maxlength="10" class="datepicker dpBH"');

	  $data['t_start'] = $this->format->timeSelect((date('H:i', time())), 'start');
	  $data['t_end'] = $this->format->timeSelect(date('H:i', time()), 'end');

	  // Render the view and echo for ajax 
	  $form = $this->load->view('admin/form_create_holidays', $data, true);
	  echo $form;
   }




   public function delete($id = 0)
   {
	  // make sure the button was clicked and that there is an array of ids
	  if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) 
		 {
			// pass the ids and let MY_Model delete the items
			$this->ramstrg_sites_m->delete_many($this->input->post('action_to'));
			$this->ramstrg_sites_business_hours_m->del_by_col($this->input->post('action_to'));
			$this->ramstrg_sites_holidays_m->del_by_col($this->input->post('action_to'));

		 } 
	  elseif (is_numeric($id)) 
		 {
			// they just clicked the link so we'll delete that one
			$this->ramstrg_sites_m->delete($id);
			$this->ramstrg_sites_business_hours_m->del_by_col($id);
			$this->ramstrg_sites_holidays_m->del_by_col($id);
		 }
	  redirect('admin/ramstrg');
   }

   // --------------------------------------------------------------------
   /**
	* gschaeftszeiten akutalisieren
	*
	* @return void
	* @author
	**/
   private function _updBusinessHours($_business_hours)
   {

	  foreach ($_business_hours as $id => $data) {
		 $upd['day_start'] = $data['day_start'];
		 $upd['day_end'] = $data['day_end'];
		 $upd['time_start'] = $data['hour_start'] . ':' . $data['min_start'];
		 $upd['time_end '] = $data['hour_end'] . ':' . $data['min_end'];

		 $this->ramstrg_sites_business_hours_m->update($id, $upd);
	  }
   }

   // --------------------------------------------------------------------
   /**
	* betriebsferien aktualisieren
	*
	* @return void
	* @author
	**/
   private function _updHolidays($_holidays)
   {

	  foreach ($_holidays as $id => $data) {
		 $upd['date_start'] = $data['date_start'];
		 $upd['date_end'] = $data['date_end'];
		 $upd['time_start'] = $data['hour_start'] . ':' . $data['min_start'];
		 $upd['time_end '] = $data['hour_end'] . ':' . $data['min_end'];

		 $this->ramstrg_sites_holidays_m->update($id, $upd);
	  }
   }
   // --------------------------------------------------------------------
   /**
	* geschaeftszeiten erstellen
	*
	* @return void
	* @author
	**/
   public function insertBusinessHours()
   {

	  $insDat['site_id'] = $this->input->get_post('site_id');
	  $insDat['day_start'] = $this->input->get_post('day_start');
	  $insDat['day_end'] = $this->input->get_post('day_end');
	  $insDat['time_start'] = $this->input->get_post('hour_start') . ':' . $this->input->get_post('min_start');
	  $insDat['time_end'] = $this->input->get_post('hour_end') . ':' . $this->input->get_post('min_end');
	  $this->ramstrg_sites_business_hours_m->insert($insDat);

	  redirect('admin/ramstrg/edit/' . $insDat['site_id']);
   }

   // --------------------------------------------------------------------
   /**
	* betriebsferien erstellen
	*
	* @return void
	* @author
	**/
   public function insertBusinessHolidays()
   {

	  $insDat['site_id'] = $this->input->get_post('site_id');
	  $insDat['date_start'] = $this->input->get_post('date_start');
	  $insDat['date_end'] = $this->input->get_post('date_end');
	  $insDat['time_start'] = $this->input->get_post('hour_start') . ':' . $this->input->get_post('min_start');
	  $insDat['time_end'] = $this->input->get_post('hour_end') . ':' . $this->input->get_post('min_end');
	  $this->ramstrg_sites_holidays_m->insert($insDat);

	  redirect('admin/ramstrg/edit/' . $insDat['site_id']);
   }

   // --------------------------------------------------------------------
   /**
	* geschaeftszeiten loeschen
	*
	* @return void
	* @author
	**/
   private function _delBusinessHours($_bh_id)
   {
	  foreach ($_bh_id as $key => $id) {
		 $this->ramstrg_sites_business_hours_m->delete($id);
	  }

   }

   // --------------------------------------------------------------------
   /**
	* betriebsfereien loeschen
	*
	* @return void
	* @author
	**/
   private function _delHolidays($_holiday_id)
   {

	  foreach ($_holiday_id as $key => $id) {
		 $this->ramstrg_sites_holidays_m->delete($id);
	  }

   }

   // --------------------------------------------------------------------
   /**
	* input fields fuer beriebsferien beziehen 
	*
	* @return void
	* @author
	**/
   private function _getHoliday_inputs($_site_id)
   {
	  $site_business_holidays = $this->ramstrg_sites_holidays_m->get($_site_id);

	  foreach ($site_business_holidays as $key => $value) {

		 $data[$key]['id'] = $value->id;

		 $data[$key]['date_start'] = form_input('holidays[' . $value->id . '][date_start]', $value->date_start, 'maxlength="10" class="datepicker dpBH"');
		 $data[$key]['time_start'] = $this->format->timeSelect($value->time_start, 'start', 'holidays[' . $value->id . '][%%]');

		 $data[$key]['date_end'] = form_input('holidays[' . $value->id . '][date_end]', $value->date_end, 'maxlength="10" class="datepicker dpBH"');
		 $data[$key]['time_end'] = $this->format->timeSelect($value->time_end, 'end', 'holidays[' . $value->id . '][%%]');


	  }

	  if (isset($data)) 
		 {
			return $data;
		 } 
	  else 
		 {
			return false;
		 }
   }

   // --------------------------------------------------------------------
   /**
	* Input felder fue geschaeftszeiten beziehen 
	*
	* @return void
	* @author
	**/
   private function _getBH_inputs($_site_id)
   {


	  $site_business_hours = $this->ramstrg_sites_business_hours_m->getBH($_site_id);

	  foreach ($site_business_hours as $key => $value) 
		 {

			$days = array_combine(range(0, 6), $this->lang->line('ramstrg:day_names'));

			$data[$key]['id'] = $value->id;

			$data[$key]['d_start'] = form_dropdown('bh[' . $value->id . '][day_start]', $days, $value->day_start, 'class="day_select"');

			$data[$key]['t_start'] = $this->format->timeSelect($value->time_start, 'start', 'bh[' . $value->id . '][%%]');

			$data[$key]['d_end'] = form_dropdown('bh[' . $value->id . '][day_end]', $days, $value->day_end, 'class="day_select"');

			$data[$key]['t_end'] = $this->format->timeSelect($value->time_end, 'end', 'bh[' . $value->id . '][%%]');

		 }

	  if(isset($data)) 
		 {
			return $data;
		 } 
	  else 
		 {
			return false;
		 }
   }




}


