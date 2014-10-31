<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Ramstrg Events Class
 * 
 * @package        PyroCMS
 * @subpackage    Ramstrg Module
 * @category    events
 * @author        Jerel Unruh - PyroCMS Dev Team
 * @website        http://unruhdesigns.com
 */
class Events_mh {
    
   protected $ci;
    
   public function __construct()
   {
	  $this->ci =& get_instance();

	  $this->ci->load->model('files/file_folders_m');
	  $this->ci->load->add_package_path("addons/shared_addons/modules/trakoka");

        //register the public_controller event
        Events::register('public_controller', array($this, 'run'));
		
		//register a second event that can be called any time.
		// To execute the "run" method below you would use: Events::trigger('sample_event');
		// in any php file within PyroCMS, even another module.
		Events::register('sample_event', array($this, 'run'));
$this->ci->session->set_userdata('redirect_to', 'mh');



    }
    
    public function run()
    {
        
        // we're fetching this data on each front-end load. You'd probably want to do something with it IRL
		//        $this->ci->sample_m->limit(5)->get_all();
    }
    
}
/* End of file events.php */