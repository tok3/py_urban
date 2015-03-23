<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage 	mh Module
 */
class mh extends Public_Controller
{

    private $addr_errors = array();
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

    
    // --------------------------------------------------------------------
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
                'rules'   => 'required|callback_distlimit_check',
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
        $man_input = '';

        if ($this->input->post('submit'))
        {

			if ($this->form_validation->run() == FALSE)
            {

                $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';

            }
            else
            {

                $postFields = $this->input->post('formdata') ;
                
                $from_country = $postFields['country_from'];
                $from_location = $postFields['location_from'];

                $to_country = $postFields['country_to'];
                $to_location = $postFields['location_to'];

                $lastPost = $this->session->userdata('post_fields');

                
                
                // km manuell eingegeben, daten nicht nochmal veruchen von gmaps zu ziehen

                if(!$this->input->post('man_dist'))
                {
                    $dist  =   $this->get_addr_info($from_country . ' ' .$this->format->convert_spcialChars($from_location), $to_country . ' ' .$this->format->convert_spcialChars($to_location));	  
                }
                else
                {
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
                    
                    $info = $this->load->view('partials/man_price_info.php',$data,TRUE);
                    $man_input = $info;
                }
                else
                {
                        $postFields['weight'] = $this->format->curr2Dec($postFields['weight']);

                    
                    if($this->input->post('man_dist')) // distanz wurde manuell eingegeben 
                    {

                        $postFields['distance_km'] = $this->format->curr2Dec($this->input->post('man_dist'));

                        $dist = new stdClass();
                        $dist->distance = new stdClass();

                        $dist->price = $this->portage_ref_m->getPrice($postFields['distance_km'], $postFields['weight'], $postFields['country_from']);
                        $dist->distance->value = $this->input->post('man_dist');
                        $dist->distance->text = $this->input->post('man_dist') . ' km';

                    }
                    else
                    {
                        $postFields['distance_km'] = $dist->distance->value / 1000; 
                        
                        $dist->vc_from = $this->get_vc($dist->origin_addr);
                        $dist->vc_to = $this->get_vc($dist->dest_addr);
                        $dist->price = $this->portage_ref_m->getPrice($postFields['distance_km'], $postFields['weight'], $postFields['country_from']);
                    }

                    
// exakter preis für anzahl also, liter stück oder sonst was 
                    if(isset($postFields['exact_unit']) && $postFields['exact_unit'] > 0)
                    {
                $weightExactUnit = $dist->price->portage_eur / $postFields['exact_unit'] ;
                $dist->exactPrice = $this->format->displCurr($weightExactUnit);

                    }
                    else
                    {

                    }
// ende exacter preise

                    
                    $max_range = $this->get_limits('km'); // max entfernung 

                    if($postFields['distance_km'] > $max_range) // ist faktor fuer max entfernung hinterlegt
                    {
                        $info = $this->load->view('partials/exceeded_range',array('max_range' => $max_range),TRUE);
                    }
                    else
                    {
                        $postFields['country_from_long'] = $this->getCountryByIso($postFields['country_from']);
                        $postFields['country_to_long'] = $this->getCountryByIso($postFields['country_to']);

                        $dist->post_fields = $postFields;
                        if(!isset($dist->vc_to))
                        {
                            $dist->vc_to =  $this->get_vc( $postFields['location_to'] .', ' . $postFields['country_to_long'] );
                        }
                        $this->session->set_userdata('calcData',$dist);
                        if(!isset($dist->post_fields['mnt_unit']))
                        {
                            $dist->post_fields['mnt_unit'] = '';
                            $dist->post_fields['exact_unit'] = '';

                        }
                        $dist->cost_per_unit = $this->costPerUnit();
                        $info = $this->load->view('partials/price_info.php',$dist,TRUE);
                    }
                }
            }
        }

        
        
//        $this->session->set_userdata($postFields);

        // --------------------------------------------------------------------
        $this->template
            ->title($this->module_details['name'])
            ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
            ->set('formfields',$this->get_formfields())
            ->set('info',$info)
            ->set('man_inp',$man_input)
            ->set('errors',$errors)
            ->set('backlink',$this->router->fetch_module() .'/'. $this->router->fetch_class())
            ->build('index')
            ;
    }

    // --------------------------------------------------------------------
    /**
     * kalkulatorische kosten por einheit
     * 
     * @access 	public	
     * @param 		
     * @return 		
     * 
     */
    public function costPerUnit()
    {
        $data = $this->session->userdata('calcData');
        $info = $data->post_fields;
        if($info['mnt_unit'] < 1)
        {
            return FALSE;
        }
        
        $weightPerUnit = $info['weight'] / $this->format->curr2dec($info['mnt_unit']);
        $staffelung = array(1,10,100,300,1000,1500);
        $storage = array();


        foreach($staffelung as $key => $mnt)
        {

            $kg = $mnt * $weightPerUnit;
            $transp = $this->portage_ref_m->getPrice(round($info['distance_km'],0, PHP_ROUND_HALF_EVEN), $kg, $info['country_from']);
            if($transp != FALSE)
            {

                $storage[$key]['mnt'] = $mnt;
                $storage[$key]['kg'] = number_format($kg, 2, ',', ' ');
                $storage[$key]['transp_per_unit'] = $this->format->displCurr($transp->portage_eur / $mnt, TRUE) ;
                $storage[$key]['transp_compl'] = $this->format->displCurr($transp->portage_eur, TRUE);

            }

        }

        array_unshift($storage, lang('mh:t_heading_cost_per_unit'));
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table border="0" cellpadding="0" cellspacing="0" class="table" id="tablePPU">' );

        $this->table->set_template($tmpl);
        $p_index = $this->table->generate($storage);

        $this->session->set_userdata('preisindex', $p_index);

        return  '<h6><strong>Preisindex</strong></h6>' . $p_index;
    }
    // --------------------------------------------------------------------
    /**
     * adresse für ausgabe formatieren
     * 
     */
    function   get_vc($_addr)
    {

        $tmp = explode(', ',$_addr);
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

        $retVal = '<ul class="vcard"  style="width:99%">
				   <!--<li class="">' . $line1 . '</li>-->
				   <li class="fn street-address">' . $line2 . '</li>
				   <li class="locality">' . $line3 . '</li>
				 </ul>';
        return  $retVal;
    }


    // --------------------------------------------------------------------
    function get_addr_info($_from, $_to)
    {

        /*
          get_addr_info('DE 65185', 'DE 60323');
          //  $dist = $this->googlemaps->get_dist('DE 65185', 'mailand');
          */	  
        $dist = $this->googlemaps->get_dist($_from, $_to);

        if($dist->status != 'OK')
        {
            $this->addr_errors['status'] = '!OK';

            return $dist;
        }

        
        if(!property_exists ($dist->rows[0]->elements[0],'distance'))
        {
            $this->addr_errors['no_property_distance'] = 'Keine Distanz';

            return $dist;
        }

        $retVal['distance'] = $dist->rows[0]->elements[0]->distance;
        $retVal['status'] = $dist->rows[0]->elements[0]->status;
        $retVal['origin_addr'] = $dist->origin_addresses[0];
        $retVal['dest_addr'] = $dist->destination_addresses[0];

      
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

        $_fields = array('country_from','country_to','location_from','location_to','country_from','weight','mnt_unit','exact_unit'); 

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
        $fields['exact_unit']->type = 'input';


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
* validation rule distance limit check
* 
*/

public function distlimit_check($str)
	{


	
$errMsg = 0;
if(!preg_match('/^[a-zA-Z0-9.,]+$/', $str))
{
$errMsg = 'Bitte Eingabe in Feld %s prüfen';
}
if($str > $this->get_limits('kg'))
{
$errMsg = 'Feld %s kann Maximalwert von ' . $this->get_limits('kg') . ' KG &uuml;berschritten!';
}
		if ($errMsg !== 0)
		{
			$this->form_validation->set_message('distlimit_check',$errMsg);
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

    // --------------------------------------------------------------------

    function mailkram()
    {

        $this->load->helper('file');
        $template = read_file('./addons/shared_addons/modules/mh/files/calc_mail_tmpl.php');


        $calcData = $this->session->userdata('calcData');
        $post_fileds = $this->session->userdata('post_fileds');
        $costPerUnit = $calcData->cost_per_unit;
        
        $sNr['price_info'] =  $this->session->userdata('price_info');
        $sNr['vc_to'] =  $calcData->vc_to;
        $sNr['vc_from'] =  isset($calcData->vc_from) ? $calcData->vc_from : '<ul class="vcard" style="width:99%">
<li class="fn street-address">n/a</li>
<li class="locality">Deutschland (DE)</li>
</ul>';
        $weightPer_ME = $calcData->post_fields['weight'] / $calcData->post_fields['mnt_unit'];
        $sNr['distance'] =  number_format($calcData->post_fields['distance_km'], 2, ',', '.');

        $sNr['weight'] =  number_format($this->format->float($calcData->post_fields['weight']), 2, ',', '.');
        $sNr['price'] =  number_format($calcData->price->portage_eur, 2, ',', '.');
        $sNr['cost_per_unit'] = '<h7>Preisindex ('.$calcData->post_fields['mnt_unit'] .' ME = '.$weightPer_ME.' KG/ME )</h7><br><br>'. $costPerUnit;

        $search =  explode(',','%%'.implode('%%, %%', array_keys($sNr)).'%%');

//$search  = array('%%price_info%%');
        $replace = $sNr;

        $msg =  str_replace($search, $replace, $template);
// nachrichtende 
// --------------------------------------------------------------------
        

        $py_settings = $this->settings->get_all();
        $py_variables = $this->variables->get_all();

        $config['protocol'] = $py_settings['mail_protocol'];
        $this->load->library('email');

        if($config['protocol'] == 'smtp')
        {

            $config['smtp_host'] = $py_settings['mail_smtp_host'];
            $config['smtp_user'] = $py_settings['mail_smtp_user'];
            $config['smtp_pass'] = $py_settings['mail_smtp_pass'];
            $config['smtp_port'] = $py_settings['mail_smtp_port'];
        }
        $config['mailpath'] = $py_settings['mail_sendmail_path'];
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);
        $this->email->from($this->session->userdata('email'));
        $this->email->to($py_variables['mh_mail_receiver']); 
        $this->email->cc($this->session->userdata('email'));

        if(isset($py_variables['mh_mail_subject']))
        {
            $subject = $py_variables['mh_mail_subject'];
        }
        else
        {
            $subject = '';

        }

        $this->email->subject($subject);
        $this->email->message($msg);	

        if($this->email->send())
        {

            redirect(site_url($this->router->fetch_module().'/success'));
        }
    }

// --------------------------------------------------------------------
    function success()
    {
        $py_variables = $this->variables->get_all();

        $this->template
            ->set('receiver',$py_variables['mh_mail_receiver'])
            ->build('mail_success')
            ;
    }

// --------------------------------------------------------------------

function mailtest()
{

        $py_settings = $this->settings->get_all();
        $py_variables = $this->variables->get_all();

        $config['protocol'] = $py_settings['mail_protocol'];

        $this->load->library('email');

        if($config['protocol'] == 'smtp')
        {

            $config['smtp_host'] = $py_settings['mail_smtp_host'];
            $config['smtp_user'] = $py_settings['mail_smtp_user'];
            $config['smtp_pass'] = $py_settings['mail_smtp_pass'];
            $config['smtp_port'] = $py_settings['mail_smtp_port'];
        }
        $config['mailpath'] = $py_settings['mail_sendmail_path'];
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);

        $this->email->from('tobias@mmsetc.de');
        $this->email->to('tobias@eq3w.de'); 
        $this->email->cc($this->session->userdata('email'));

        if(isset($py_variables['mh_mail_subject']))
        {
            $subject = $py_variables['mh_mail_subject'];
        }
        else
        {
            $subject = '';

        }

        $this->email->subject($subject);
        $this->email->message('test');	
        if($this->email->send())
        {
echo "ja";
        }





}

    // --------------------------------------------------------------------
    
}
