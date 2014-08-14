<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Theme_Urban extends Theme
{


	public $name			= 'Urban Logistics';
	public $author			= 'Tobias C. Koch | mms&amp;c.';
	public $author_website	= 'http://mmsetc.de/';
	public $website			= 'http://mmsetc.de/';
	public $description		= 'An HTML5 template build with foundation 5. for urban logistics // Linde MH';
	public $version			= '0.0.1';
	public $options 		= array(


		'footer_width' => array(
			'title'         => 'Footer',
			'description'   => 'How would you like to display the Footer',
			'default'       => 'full',
			'type'          => 'radio',
			'options'       => 'content=Content Width|full=Browser Width',
			'is_required'   => true
			),

		'sticky_footer' => array(
			'title'         => 'Sticky Footer',
			'description'   => 'How would you like the Footer behaviour',
			'default'       => 'on_less_content',
			'type'          => 'radio',
			'options'       => 'on_less_content=Sticky if no vertical scrollbar|allways=Always sticky|off=Never',
			'is_required'   => true
			),

		'has_sidebar' => array(
			'title'         => 'Use Sidebar ?',
			'description'   => '2 Column Layout with right Sidebar for Side Navigation and Widget. ',
			'default'       => 'true',
			'type'          => 'radio',
			'options'       => 'true=Yes|false=No',
			'is_required'   => true
			),

		'orbit' => array(
			'title'         => 'Orbit (Image Slider)',
			'description'   => 'Would you like to display the <a href="http://foundation.zurb.com/docs/components/orbit.html" target="top">Orbit</a> image slider on the homepage?',
			'default'       => 'no',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
			),
		'show_breadcrumbs' 	=> array(
			'title'         => 'Do you want to show breadcrumbs?',
			'description'   => 'If selected it shows a string of breadcrumbs at the top of the page.',
			'default'       => 'no',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
			),
		'slogan' => array(
			'title'         => 'Display Site Slogan',
			'description'   => 'You want to display the site slogan?',
			'default'       => 'no',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
			),

		'show_sitename' => array(
			'title'         => 'Display Site Name',
			'description'   => 'You want to display the site name?',
			'default'       => 'hide',
			'type'          => 'radio',
			'options'       => 'topbar=Top Bar|header=Header|hide=Don\'t Show',
			'is_required'   => true
			),

		'header_nav_width' => array(
			'title'         => 'Top Navbar & Header',
			'description'   => 'How would you like to display the Top Navbar & Header',
			'default'       => 'full',
			'type'          => 'radio',
			'options'       => 'content=Content Width|full=Browser Width',
			'is_required'   => true
			),
		


		);




}

/* End of file theme.php */