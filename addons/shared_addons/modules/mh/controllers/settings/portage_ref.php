<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is part of MH Module (Material Handling Urban Logistics)
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage MH(Material Handling) Module
 */
class portage_ref extends Public_Controller
{
   protected $gridTable;

   public function __construct()
   {
	  parent::__construct();

	  $this->load->model('portage_ref_m');
	  $this->gridTable = $this->load->model('portage_ref_m');

	  // Load the required classes

	  $this->lang->load($this->router->fetch_module());


	  $this->template
		 ->append_css('module::' . $this->router->fetch_module() . '.css')
		 ->append_css('module::jquery.datetimepicker.css')
->append_css('module::tablesorter/style.css')
		 ->append_css('module::responsive-tables.css')
		 ->append_js('module::jquery.min.js')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::jquery.json-2.3.js')
		 ->append_js('module::jquery.cookie.js')
		 ->append_js('module::tablesorter/js/jquery.tablesorter.min.js')
		 ->append_js('module::tablesorter/js/tablesorter_widgets.js')
		 ->append_js('module::responsive-tables.js')
		 ->append_js('module::' . $this->router->fetch_module() . '.js');

	  Asset::add_path('mh', base_url(SHARED_ADDONPATH .  'modules/' .$this->router->fetch_module()) . '/') ;

   }

   // --------------------------------------------------------------------
   /**
	* All items
	*/
   public function index($dir = "dist")
   {



	  if($this->input->post('submit'))
		 {
			$portRef = $this->input->post('portRef');

			$this->load->model('general_m');
			$this->optTable = new $this->general_m();
			$this->optTable->set_table('mh_portage_reference');

			foreach($portRef as $id => $value)
			   {
				  $updDat['portage_eur'] = $this->format->curr2Dec($value);
				  $this->optTable->update($id,$updDat);
			   }
			redirect(current_url());
		 }


	  if($dir == "dist")
		 {
			$dirSwitch = anchor($this->router->fetch_module() .'/settings/'. $this->router->fetch_class(). '/' . $this->router->fetch_method() . '/weight', '<i class="fi-tablet-landscape"></i>&nbsp;Ausrichtung &auml;ndern');
		 }
	  else
		 {
			$dirSwitch = anchor($this->router->fetch_module() .'/settings/'. $this->router->fetch_class(). '/' . $this->router->fetch_method() . '/dist', '<i class="fi-tablet-portrait"></i>&nbsp;Ausrichtung &auml;ndern');

		 }

	  $errors ='';
	  $this->template
		 ->title('')
		 ->set('dirSwitch',$dirSwitch)
		 ->set('refMatrix',$this->getRefMtrx($dir))
		 ->set('backlink',$this->router->fetch_module() .'/settings/'. $this->router->fetch_class())
		 ->set('form_errors', $errors)
		 ->build('portage_ref');

   }


   // --------------------------------------------------------------------
   /**
	* get matrix to fill in reference prices
	* 
	* @access 	private	
	* @param 	string	direction of table determine the top row weigts or distances	
	* @return 	array	
	* 
	*/

   private function getRefMtrx($dir = 'weight')
   {

	  $headingRow = $dir; // weight or dist as top heading if weight is top than dist is first row vice versa !

	  $this->load->model('general_m');
	  $this->optTable = new $this->general_m();
	  $this->optTable->set_table('mh_distances');
	  $this->db->order_by('km','asc');
	  $dist = $this->optTable->get_all();

	  $this->optTable->set_table('mh_weight_range');
	  $this->db->order_by('kg','asc');
	  $weights = $this->optTable->get_all();



	  $this->load->library('table');

$tmpl = array (
                    'table_open'          => '<table class="refPrice _responsive">',
                    'table_close'         => '</table>'
              );

$this->table->set_template($tmpl); 
	  $headingArr = array();
   
	  foreach($weights as $key => $colHeading)
		 {
			$headingWeight[$key] = '<span>' . $colHeading->kg . ' Kg</span>';
		 }
   
	  foreach($dist as $key => $colHeading)
		 {
			$headingDist[$key] = '<span>' . $colHeading->km . ' Km</span>';
		 }

	  if($headingRow == 'weight')
		 {
			$headingArr = $headingWeight;
			array_unshift ( $headingArr , '' );
			$firstCol = $headingDist;

			$outer = $dist;
			$inner =  $weights;

		 }

	  if($headingRow == 'dist')
		 {
			$headingArr = $headingDist;
			array_unshift ( $headingArr , '' );
			$firstCol = $headingWeight;

			$outer = $weights;
			$inner =  $dist;
		 }


	  $this->table->set_heading($headingArr);


	  $this->get_rows($outer, $inner,$firstCol);


	  return $this->table->generate(); 

   }


   // --------------------------------------------------------------------
   /**
	* generate row in matrix
	* 
	* @access 	private	
	* @param 	array	containing objects	
	* @param 	array	containing objects	
	* @return 	void	
	* 
	*/
   private function get_rows($outer, $inner, $firstCol)
   {
	  $outerKeys =  array_keys((array)$outer['0']);
	  $outerUnit = $outerKeys['1'];

	  $innerKeys =  array_keys((array)$inner['0']);
	  $innerUnit = $innerKeys['1'];

	  foreach($outer as $key => $outerVal)
		 {

			$row = array();
			foreach($inner as $innerVal)
			   {
				  $inp = $this->getMxFieds(array($outerUnit=>$outerVal->id,$innerUnit=>$innerVal->id));

				  array_push ( $row , $inp);
				  //array_push ( $row , $outerUnit.':'.$outerVal->$outerUnit.' '.$innerUnit.':'.$innerVal->$innerUnit);
		
			   }

			array_unshift ( $row , '<strong>' . $firstCol[$key] . '</strong>' );

			$this->table->add_row($row);

		 }

   }
   // --------------------------------------------------------------------
   /**
	* set and get matrix fields
	* 
	* @access 	private	
	* @param 		
	* @return 		
	* 
	*/
   private function getMxFieds($val)
   {
 
	  $value = 0;  
	  $this->load->model('general_m');
	  $this->optTable = new $this->general_m();
	  $this->optTable->set_table('mh_portage_reference');

	  $this->db->where('distance_id',$val['km']);
	  $this->db->where('weight_range_id',$val['kg']);

	  $refField = $this->optTable->get_all();
   
	  if(count($refField) == 1 )
		 {
			$id = $refField[0]->id;
			$value = $refField[0]->portage_eur;

		 }
	  else
		 {
			$insDat['distance_id'] = $val['km'];
			$insDat['weight_range_id'] = $val['kg'];
			$insDat['portage_eur'] = 0;

			$id = $this->optTable->insert($insDat);
		 }

	  $conf = array(
					'name'        => 'portRef[' . $id . ']',
					'id'          => 'portRef' . $id,
					'class'          => 'inpPortRef',
					'maxlength'   => 12,
					'value'       => $this->format->displCurr($value),
					);

	  return form_input($conf);
   }
   // --------------------------------------------------------------------
   
}
