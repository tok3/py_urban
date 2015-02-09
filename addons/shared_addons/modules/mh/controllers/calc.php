<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage 	mh Module
 */
class calc extends Public_Controller
{

    private $addr_errors = array();
    private $vcards = array();

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

        $this->portage_ref_m->getFactor('DE');

        $info = '';
        $errors = '';

        $this->load->library('form_validation');

        $data = $this->input->post('data');

        $validation_rules = array(
            array(
                'field'   => 'formdata[weight]',
                'label'   => 'Gewicht in KG',
                'rules'   => 'required|integer|less_than['.($this->get_limits('kg') + 1) .']'
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
                'label'   => 'Empfangsort',
                'rules'   => 'required'
            )
        );



        $this->form_validation->set_rules($validation_rules);
        if ($this->input->post('submit'))
        {
            echo "<pre><code>";
            print_r($this->session->all_userdata());
            echo "</code></pre>";
            
            //         $this->session->unset_userdata('calcData');

            if ($this->form_validation->run() == FALSE)
            {

                $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';

            }
            else
            {

                $info = $this->yieldInfo();           
            }
        }


        /**
         *  $transp = $this->portage_ref_m->getPrice(606,300,'DE');
         echo "<pre><code>";
         print_r($transp);
         echo "</code></pre>";

         * 
         */
        //  $this->costPerUnit();
        // --------------------------------------------------------------------
        $this->template
            ->title($this->module_details['name'])
            ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
            ->set('formfields',$this->get_formfields())
            ->set('info',$info)
            ->set('errors',$errors)
            ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
            ->build('parts/t_calc_view')
            ;
    }
    // --------------------------------------------------------------------
    /**
     * infos zur berechnung "ernten"
     * 
     * @access 	public	
     * @param 		
     * @return 		
     * 
     */
    public function yieldInfo()
    {

        $this->session->unset_userdata('calcInfo');
        $this->session->set_userdata('calcInfo',array());

        $postFields = $this->input->post('formdata') ;

        $from_country = $postFields['country_from'];
        $from_location = $postFields['location_from'];

        $to_country = $postFields['country_to'];
        $to_location = $postFields['location_to'];

        // km MANUELL EINGEGEBEN, daten nicht nochmal veruchen von gmaps zu ziehen

        if(!$this->input->post('man_dist'))
        {
            $dist  =   $this->get_addr_info($from_country . ' ' .$this->format->convert_spcialChars($from_location), $to_country . ' ' .$this->format->convert_spcialChars($to_location));	  

        }
        else
        {
            $postFields['distance_km'] = $this->input->post('man_dist');
            $postFields['distance_text'] = $this->input->post('man_dist') . ' km';

            $dist = FALSE; // distance wurde manuell eingegeben 

        }


        if(count($this->addr_errors)) // FALLS GOOGLE MAPS KEIN ERGEBNIS ODER EIN FALSCHES LIEFERT
        {

            // input fuer manuelle distanzeingabe	  
            $conf = array(
                'name'        => 'man_dist',
                'value'       => set_value('man_dist', ''),
            );

            $data['man_dist']  = form_input($conf);
                    
            $info = $this->load->view('parts/man_price_info.php',$data, TRUE);

        }
        else
        {

            $data = array_merge($this->session->userdata('calcInfo'),$postFields);

            $data['price'] = $this->portage_ref_m->getPrice($data['distance_km'], $data['weight'], $data['country_from']);

            $info = $this->load->view('parts/price_info.php',$data, TRUE);

            $max_range = $this->get_limits('km'); // max entfernung 

            if($data['distance_km'] > $max_range) // ist faktor fuer max entfernung hinterlegt
            {
                $info = $this->load->view('parts/exceeded_range',array('max_range' => $max_range),TRUE);
            }

        }
        
        return $info;
    }
    

    // --------------------------------------------------------------------
    /**
     * daten von googlemaps api beziehen
     * 
     * @access 	public	
     * @param 	string	
     * @return 	string	
     * 
     */
    function get_addr_info($_from, $_to)
    {
        $dist = $this->googlemaps->get_dist($_from, $_to);

        if($dist->status != 'OK')
        {
            $this->addr_errors['status'] = '!OK';

            return $dist;

        }

        //keine entfernung gefunden 
        if(!property_exists ($dist->rows[0]->elements[0],'distance'))
        {
            $this->addr_errors['no_property_distance'] = 'Keine Distanz';

            return $dist;
        }

        $retVal['distance_text'] = $dist->rows[0]->elements[0]->distance->text;
        $retVal['distance_km'] = round($dist->rows[0]->elements[0]->distance->value / 1000,0, PHP_ROUND_HALF_EVEN);

        $retVal['status'] = $dist->rows[0]->elements[0]->status;
        $retVal['origin_addr'] = $dist->origin_addresses[0];
        $retVal['dest_addr'] = $dist->destination_addresses[0];

        $formdata =  $this->input->post('formdata');

        $retVal['country_from'] = $formdata['country_from'];
        $retVal['country_to'] = $formdata['country_to'];



        $this->session->set_userdata('calcInfo',$retVal); 
        return (object) $retVal;
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

        $_fields = array('country_from','country_to','location_from','location_to','country_from','weight','mnt_unit'); 

        foreach($_fields as $key => $field)
        {
            $fields[$field] = new stdClass();			
        }



        $fields['country_from']->type = 'dropdown';
        $fields['country_from']->options  = $this->general_m->get_active_countries();
        $fields['country_from']->selected  = 'DE';

        $fields['country_to']->type = 'dropdown';
        $fields['country_to']->options  = $this->general_m->get_active_countries();

        $fields['location_from']->type = 'input';

        $fields['location_to']->type = 'input';

        $fields['weight']->type = 'input';

        $fields['mnt_unit']->type = 'input';


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

                if(isset($field->selected) && $value == '')
                {
                    $value = $field->selected;
                }
                                    
                $formfields[$namePreFx . $field->name] = form_dropdown($postName . '[' . $field->name . ']', $field->options, $value);

            }
        }
        return $formfields;	  
    }

    // --------------------------------------------------------------------
    // array mit maximalen kg und km angaben aus db holen 
    function get_limits($_unit = '')
    {
        $this->load->model('general_m', 'limit');
        $this->limit->set_table('mh_distances');
        $this->db->order_by('km','DESC');
        $this->db->limit(1);
        $distMax = $this->limit->get_all();


        $this->limit->set_table('mh_weight_range');
        $this->db->order_by('kg','DESC');
        $this->db->limit(1);
  
        $weightMax = $this->limit->get_all();
 
        $limits = array_merge((array) $weightMax[0], (array) $distMax[0]);
        unset($limits['id']);


        if($_unit != '' && isset($limits[$_unit]))	
        {
            return $limits[$_unit];
        }
        return $limits;
    }

// --------------------------------------------------------------------
/**
* get countryname by iso 
* 
*/
    function getCountryByIso($_iso2 = DE)
    {

$countries = $this->general_m->get_active_countries();
return $countries[$_iso2];
    }
    // --------------------------------------------------------------------
    /**
     * adresse für ausgabe formatieren
     * 
     */
    function   get_vc($_addr)
    {

        $tmp = explode(',',$_addr);
        $segments = count($tmp);

        if($segments != 3 && $segments != 4)
        {
            return FALSE;
        }

        if($segments == 3)
        {
            $line1 = $tmp[0];  
            $line2 = $tmp[1];  
            $line3 = $tmp[2];  
        }
        if($segments == 4)
        {
            $line1 = $tmp[0] . ' ' .$tmp[1];  
            $line2 = $tmp[2];  
            $line3 = $tmp[3];  
        }

        $retVal = '<ul class="vcard">
				   <!--<li class="">' . $line1 . '</li>-->
				   <li class="fn street-address">' . $line2 . '</li>
				   <li class="locality">' . $line3 . '</li>
				 </ul>';
        return  $retVal;
    }



    // --------------------------------------------------------------------
    
    
    function test()
    {

        echo $this->get_vc(' ,  , Deutschland');
    }
    // --------------------------------------------------------------------
   
}
