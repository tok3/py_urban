<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a mh module for PyroCMS
 *
 * @author 		
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	mh Module
 */
class Plugin_mh extends Plugin
{
	/**
	 * Item List
	 * Usage:
	 * 
	 * {{ mh:items limit="5" order="asc" }}
	 *      {{ id }} {{ name }} {{ slug }}
	 * {{ /mh:items }}
	 *
	 * @return	array
	 */
	function items()
	{
		$limit = $this->attribute('limit');
		$order = $this->attribute('order');
		
		return $this->db->order_by('name', $order)
						->limit($limit)
						->get('mh_items')
						->result_array();
	}
}

/* End of file plugin.php */
