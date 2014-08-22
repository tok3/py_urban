<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class general_m extends MY_Model {

   protected $_table;
   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table für rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'ramstrg_bookings';
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
   
}
