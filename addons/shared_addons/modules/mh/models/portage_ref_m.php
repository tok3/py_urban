<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class portage_ref_m extends MY_Model {

   protected $_table;
   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table für rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'mh_portage_reference';
   }
	
   //create a new item
   public function create($to_insert)
   {
   
	  return $this->db->insert($this->_table, $to_insert);
   }
   public function set_table($_table)
   {
	  $this->_table = $_table;
   }


   /**
	* delete entries by column name
	*
	* @return void
	* @author 
	**/
   public function del_by_col ($_id, $_col = 'site_id')
   {
	  if(is_array($_id))
		 {
			foreach ($_id as $key => $value) 
			   {
				  $this->db->delete($this->_table, array($_col => $value));
			   }
		 }
	  else
		 {
			$this->db->delete($this->_table, array($_col => $_id));	
		 }
	
	  // echo $this->db->last_query();


   }

   // --------------------------------------------------------------------
   
   public function getPrice($_dist, $_weight)
   {
	  $this->db->select('mh_portage_reference.portage_eur, mh_distances.km, mh_weight_range.kg');
	  $this->db->from('mh_portage_reference');
	  $this->db->join('mh_distances','mh_portage_reference.distance_id = mh_distances.id');
	  $this->db->join('mh_weight_range','mh_portage_reference.weight_range_id = mh_weight_range.id');

	  $this->db->where('mh_distances.km >= ' .$_dist);
	  $this->db->where('mh_weight_range.kg >= ' .$_weight);


	  $query = $this->db->get();
	  $result = $query->result();
	  
	  if(count($result) >= 1)
		 {
			return $result[0];
		 }   
   }
   // --------------------------------------------------------------------
   
}
