<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 *
 *
 * @category		Libraries
 * @author			tobias.koch@mmsetc.de	
 * @dependencies 	library table, helper url
 * 
 * USAGE EXAMPLE 1
 *  $sortable = new sortable_grid();
 *  $sortable->set_id('grid_id');
 *  $sortable->set_class('tablesorter anotherclass');
 *  $sortable->set_edit_link('id','taxSocialIns/showDetails');
 *  $sortable->set_heading(array('Employee ID','Surname','Name','Department','Manager',''));
 * 
 *  $grid = $sortable->getGrid($result); // OUTPUT
 * 
 * USAGE EXAMPLE 2
 *  $conf['id'] = 'grid_id';
 *  $conf['class'] = 'tablesorter anotherclass';
 *  $conf['edit_link'] = array('id'=>'taxSocialIns/showDetails');
 *  $conf['heading'] = array('Employee ID','Surname','Name','Department','Manager','');
 * 
 *  $sortable = new sortable_grid($conf);
 *  $grid = $sortable->getGrid($result); // OUTPUT
 *  
 * 
 */

class sortable_grid {

   public $filter = FALSE;
   public $quicksearch = FALSE;
   public $resetFilterIcon = 'assets/icons/filter_reset.png';

   private $table_id = 'sortable'; 			// string	 id of the table
   private $table_class = 'tablesorter'; 	// string	class(es) of table
   private $edit_colGroup_id = 'editCol'; 	// string	id columngroup containing the edit link
   private $table_heading = array(); 		// table heading in case its left empty column name will be used 
   private $edit_link = array();			// create edit link in last culumn key = assoc-key of column value = target eg. array(id=>'controller/function');
   private $disabled = array();				// $conf['disabled'] = array(0,1); cols where sorting should be disabled
   private $removeCols = array();			// colums that have to be removed from list
   private $THClass = '';                   // add special class to TH eg  "sorter-text" "sorter-false" "sorter-currency" "sorter-digit" "sorter-usLongDate" see tablesorter documentation for details 

   // --------------------------------------------------------------------
   /**
	* Constructor
	*/
   public function __construct($config = array())
   {

	  $this->CI =& get_instance();

	  if (count($config) > 0)
		 {
			$this->initialize_grid($config);
			$this->config = $config;	  
		 }
   }

   // --------------------------------------------------------------------
   /**
	* function initializes the grid, it uses the public setter functions of this class
	*
	* @access 	private	
	* @param 	array		
	* @return 	void	
	*/
   private function initialize_grid($config)
   {
   
	  foreach ($config as $key => $val)
		 {
			$method = 'set_'.$key;

			if (method_exists($this, $method))
			   {
				  $this->$method($val);
			   }
		 }
   }
   // --------------------------------------------------------------------
   /**
	* returns the complete grid depending on configuration
	*
	* @access 	public	
	* @param 	array	multidemensional array containing the data diplayed in the grid
	* @return 	string	the complete sortable datagrid, ready to be outputted
	*/
   public function getGrid($_res)
   {

	  if(isset($this->modCols))
		 {
			$_res = $this->arrangeCols($_res, $this->modCols) ;
		 }

	  // clean certain columns if set 
	  if(count($this->removeCols) > 0) 
		 {
			$_res = $this->getCleaned($_res);
		 }

 
	  $this->num_rows = count($_res);
	  // create heading if not set
	  if(count($this->table_heading) == 0) 
		 {
			$this->createColumnHeading($_res);
		 }

	  // set edit link
	  
	  $keyCheck = implode(array_keys($this->edit_link));

	  if((count($this->edit_link) == 1 && !is_numeric($keyCheck)) || (count($this->edit_link) == 2 && !is_numeric($keyCheck)))
 		 {

			$icoProps = '';
			if(isset($this->edit_link[0]))
			   {
				  $icoProps = $this->edit_link[0];
			   }			
 			$keyVal = each($this->edit_link);
 
 			$_res = $this->setEditLink($_res,$keyVal['key'],$keyVal['value'],$icoProps);
 		 }
	  
	  // set edit link
	  if(count($this->edit_link) >= 2 && is_numeric($keyCheck))
		 {
			$linksArr = $this->edit_link;
   
			foreach($linksArr as $key => $item)
			   {
				  $icoProps = '';

				  if(count($item) == 2)
					 {
						$keys = array_keys($item);
			
						$icoProps =  $item[$keys[1]];
			
					 }

				  
				  $keyVal = each($item);
				  	  
				  $_res = $this->setEditLink($_res,$keyVal['key'],$keyVal['value'], $icoProps);				  


			   }
		 }

	  return $this->getTable($_res);
   }

   // --------------------------------------------------------------------
   /**
	* Format result in HTML-Table
	*
	* @access	private
	* @param	array	result array from employees
	* @return	string
	*/

   private function getTable($_res)
   {

	  if(!is_array($_res) || count($_res) == 0)
		 {
			return FALSE;
		 }

	  $this->CI->load->library('table');

	  $this->CI->table->set_heading($this->table_heading);  
	
	  $colspanFoot =  count($_res[0]);
	  $spanFirstColGroup = $colspanFoot - 1;
	
	  $tmpl = array ( 
					 'table_open'  => '<table cellpadding="0" cellspacing="1" id="'.$this->table_id.'" class="'.$this->table_class.'">
					  <colgroup>
						<col span="'.$spanFirstColGroup.'" />
					  </colgroup>
					  <colgroup>
					    <col  id="'.$this->edit_colGroup_id.'" width="15px" />
					  </colgroup>',
					 'heading_row_start'   => '<tr>',
					 'heading_row_end'     => '</tr>',
					 'table_close'         => '</table>',
					 'thead_close'=>'</thead>
					  <tfoot>
	    			 	<tr style="display:none;">
	        				<td colspan="'.$colspanFoot.'">
	            				No rows match the filter...
	        				</td>
	    				</tr>	    
					  </tfoot>', 
					  );

	  if(is_array($_res))
		 {

			$rows = $this->prepRows($_res);
	  
			foreach($rows as $key => $item)
			   {

				  $this->CI->table->add_row($item);

			   }
		 }

	  $this->CI->table->set_template($tmpl); 

	  //  return $this->CI->table->generate($_res).$this->appendJS(); 
	  return $this->getQuicksearch().$this->CI->table->generate().$this->appendJS(); 

   }

   // --------------------------------------------------------------------
   /**
	* function preps cols and rows for adding to table
	*
	* @access 	privat	
	* @param 	array	resultset
	* @return 	array	containing array for each column 
	*/   

   function prepRows($rows)
   {


	  if(!is_array($rows) || count($rows) == 0)
		 {
			return FALSE;
		 }

	  $colums = count($rows[0]);

	  $keys = array_flip(array_keys($rows[0]));
	  $prepped = array();
	  foreach($rows as $key => $item)
		 {

			foreach($item as $innerKey => $innerItem)
			   {
				  				  
				  if(strlen($item[$innerKey]) == 0)
					 {
						$item[$innerKey] = '&nbsp;';
					 }
				  
				  $prepped[$key][$keys[$innerKey]] =  array('data' => $item[$innerKey]);

				  // --------------------------------------------------------------------
				  // config per culum/cell

				  if(isset($this->config['column'][$keys[$innerKey]]))
					 {

						$conConfique = array_merge($prepped[$key][$keys[$innerKey]], $this->config['column'][$keys[$innerKey]]);
						$prepped[$key][$keys[$innerKey]] = $conConfique;

					 }
				  // --------------------------------------------------------------------

			   }
		 }

	  return $prepped; 
   }

   // --------------------------------------------------------------------
   /**
	* function creates default column heading if it is not assigned in config array or with setter method
	*
	* @param 	array	
	* @return 	void	
	*/
   private function createColumnHeading($_data)
   {
	  if(count($_data) > 0 && is_array($_data[0]))
		 {
			$this->table_heading = array_keys($_data[0]);
		 }
	  else
		 {
			return FALSE;
		 }
   }
   // --------------------------------------------------------------------
   /**
	* function cleans array from columns set in $this->remove_columns()
	*
	* @access 	private	
	* @param 	array	
	* @return 	array	
	*/
   private function getCleaned($res)
   {

	  foreach($res as $key => $item)
		 {

			foreach($this->removeCols as $remKey => $remItem)
			   {
				  unset($res[$key][$this->removeCols[$remKey]]);
			   }
		 }
	  return $res;
   }
 
   // --------------------------------------------------------------------
   /**
	* This function sets the edit link in the employees overview grid
	* 
	*  
	* @param  array		resultset retrieved from function employeeList in model mod_employee 
	* @param  string	column that contains an unique value 
	* @param  string	link target eg controller/function will return controller/function/value from $_editLinkColumn 
	*					in case there are further uri segments after $_editLinkColumn, it is possible to set a wildcard like %%id%% ($_editLinkColumn) in the 3rd param eg controller/function/%%id%%/segment3/segment4
	* @return array
	*/
   private function setEditLink($_empList, $_editLinkColumn, $_target, $_iconProperties = array())
   {

	  if(count($_empList) == 0)
		 {
			return FALSE; // return false if no data is given 
		 }
	  
	  $checkEditColOffset = array_flip(array_keys($_empList[0]));
	  $this->editLinkOffset = $checkEditColOffset[$_editLinkColumn];

	  if(count($_iconProperties) == 0 || !is_array($_iconProperties))
		 {
			// set up icon properties
			$iconProperties = array(
									'src' => 'assets/icons/file_edit.png',
									'alt' => 'edit',
									'title' => 'edit entry',
									'class' => 'listIcons',
									);
		 }
	  else
		 {

			$iconProperties = $_iconProperties;
		 }

	  $icon = img($iconProperties);
	  $icon = '<i class="fi-page-edit size-18">&nbsp;</i>';

	  // walk thru employees list
	  foreach($_empList as $key => $value)
		 {

			if(stristr($_target,'%%'.$_editLinkColumn.'%%'))
			   {
				  $_empList[$key][$_editLinkColumn] = anchor(str_replace('%%'.$_editLinkColumn.'%%',$_empList[$key][$_editLinkColumn],$_target),$icon,array('rel'=>'overlay'));
			   }
			else
			   {
				  $_empList[$key][$_editLinkColumn] = anchor($_target.'/'.$_empList[$key][$_editLinkColumn],$icon, array('rel'=>'overlay'));
			   }
		 }	  

	  return $_empList;	  
   }
   // --------------------------------------------------------------------
   /**
	* This function appends the javascript to make the grid working. 
	* In case edit_link_col is set, sorting will be disabled on this column
	*
	* @return 	string	containing the js snippet to be append on outputed table
	* 
	*/
   private function appendJS()
   {
	  $sortPersist = '';
	  $applyFilter = '';
	  if($this->filter == TRUE)
		 {
			$applyFilter = $this->appendFilter();
		 }
	  if($this->num_rows > 2)
		 {
			$sortPersist = ',\'sortPersist\'';
		 }
	  if(isset($this->editLinkOffset))
		 {
			array_push($this->disabled,$this->editLinkOffset);
		 }
	  $disabledSortColumns = '';

	  if(count($this->disabled) > 0)
		 {
			foreach($this->disabled as $disabled)
			   {
				  $disabledSortColumns .= $disabled.': {sorter: false},';
			   }
			$disabledSortColumns = 			 ',headers: {
 					   '.substr($disabledSortColumns, 0, -1).'
 							 } ';
		 }

	  $retVal = '  <script type="text/javascript">//<![CDATA[
 
   $(document).ready(function() 
					 { 
' . $this->THClass . '
	   $("#'.$this->table_id.'").tablesorter({ 
						 widthFixed: false,
						 sortLocaleCompare: true,
							 widgets: [\'zebra\''.$sortPersist.']
							 '.$disabledSortColumns.'  

			 });   
	' . $applyFilter . '
	 } );
	//]]>
	</script>';		


	  return $retVal; 
   }
   // --------------------------------------------------------------------
   /**
	* append filter
	*/
   private function appendFilter()
   {

	  $fScript = 'var options = {
				additionalFilterTriggers: [ $(\'#'.$this->table_id.'Quickfind\')],
				clearFiltersControls: [$(\'#cleanfilters\')],
			   	filteredRows: function(filterStates) {      															
				 $("#'.$this->table_id.'").trigger("applyWidgets");
                }            
			};

			$(\'#'.$this->table_id.'\').tableFilter(options);

//$(\'#'.$this->table_id.'\').find("tr.filters").find("td:last").html(\'' . $this->getClearIcon('cleanFiltersRow'). '\');    

';


	  return $fScript;
   }
   // --------------------------------------------------------------------
   /**
	* get quicksearch fields if filter is true
	*/
   private function getQuicksearch()
   {


	  if($this->filter == TRUE)
		 {
			$visibility = '';


			return '<div id="picNetQuicksearch"><input type="text" id="' . $this->table_id. 'Quickfind"/>' . $this->getClearIcon() . '</div>';
		 }


   }
   // --------------------------------------------------------------------
   function getClearIcon($id = 'cleanfilters')
   {
	  $iconProperties = array(
							  'src' => $this->resetFilterIcon,
							  'alt' => 'Clean Filter',
							  'title' => 'Clean Filter',
							  'style' => 'cursor:pointer; width:20px;',
							  'id' => $id,

							  );

	  $icon = img($iconProperties);

	  return $icon;
   }

   // --------------------------------------------------------------------
   /**
	* function removes column by key 
	*
	* @access 	public	
	* @param 	mixed	string containig array key of column to removed or array width colum names
	*/

   public function remove_columns($_cols)
   {

	  if(is_array($_cols))
		 {
			$this->removeCols = $_cols;

		 }
	  else
		 {
			$this->removeCols = array($_cols);
 
		 }

   }

   // --------------------------------------------------------------------
   /**
	* function arranges columns in new order
	* useful if order of columns in result set doesn't fit your needs 
	*
	* @access 	public	
	* @param 	array	result 
	* @param 	array	col names in desired order
	*/
   public function arrangeCols($_res, $_cols = 0)
   {
	  if(isset($this->col2Copy) && isset($this->colCopyName))
		 {
			$_res = $this->cloneColumn($_res);

		 }


	  $retVal = array();


	  foreach($_res as $key => $value)
		 {

			foreach($_cols as $column)
			   {
				  $retVal[$key][$column] = $value[$column];				  
	
				
			   }
			
		 }
	  
	  return $retVal;
   }
   // --------------------------------------------------------------------
   /**
	* function clones colum given in copy_col()
	* eg useful in cases where one colum is displayed with its value and in onother col the value is used as "edit link". or if its intendet to have two "edit links" which jums to differnd tagets with the same value
	*
	* @access 	private	
	* @param 	array	result
	* @return 	array	
	* 
	*/   
   private function cloneColumn($_res)
   {

	  foreach($_res as $key => $value)
		 {

			$_res[$key][$this->colCopyName] = $_res[$key][$this->col2Copy];
			
		 }

	  return $_res;
   }

   // --------------------------------------------------------------------
   /**
	* function specifies culumns that have to be cloned in function cloneColumn()
	*  
	* @param 	string	column name
	* @param 	string 	copy name
	*/
   public function copy_col($_colName, $_copyName)
   {
	  $this->col2Copy = $_colName;
	  $this->colCopyName = $_copyName;

   }
   /* --------------------------------------------------------------------
	*********************************************************************
	SETTER METHODS FOR CLASS VARS
	--------------------------------------------------------------------*/
   /**
	* function arranges columns in new order and useses only given cols 
	* useful if order of columns in result set doesn't fit your needs 
	*
	* @access 	public	
	* @param 	array	result 
	*/

   public function set_cols($_cols)
   {
	  $this->modCols = $_cols;
   }
   // --------------------------------------------------------------------
   /**
	* set id of table
	*
	* @access 	public	
	* @param 	string	
	*/   

   public function set_id($_id)
   {

	  $this->table_id = $_id;
   }   

   // --------------------------------------------------------------------
   /**
	* set class(es) of table
	*
	* @access 	public	
	* @param 	string	
	*/

   public function set_class($_class)
   {
	  $this->table_class = $_class;
   }   

   // --------------------------------------------------------------------
   /**
	* set heading of table
	*
	* @access 	public	
	* @param 	array	
	*/

   public function set_heading($_heading = array())
   {

	  	  $this->table_heading = $_heading;
   }
   // --------------------------------------------------------------------
   /**
	* set edit link 
	*
	* @access 	public	
	* @param 	array/string	array(colname=>target) or colname if string is given  	
	* @param 	string			target 
	*/

   public function set_edit_link($_field_or_target_arr, $_target = '')
   {
	  if(is_array($_field_or_target_arr))
		 {
			$this->edit_link = $_field_or_target_arr;
		 }
	  else
		 {
			$this->edit_link = array($_field_or_target_arr => $_target);
		 }
   }
   // --------------------------------------------------------------------
   /**
	* set colum sorting disabled 
	*
	* @access 	public	
	* @param 	array
	*/

   public function set_disabled($_disabled = array())
   {
	  $this->disabled = $_disabled;;
   }

   // --------------------------------------------------------------------
   public function filtered($_filterd = FALSE)
   {
	  $this->filter = $_filtered;
   }
   // --------------------------------------------------------------------
   public function addHeaderClass($_elem, $_thClass)
   {
	  $tmpStr = $this->THClass;

			$tmpStr .= '$("#'.$this->table_id.' th:nth-child(' . $_elem .')").addClass("' . $_thClass . '");';

	  $this->THClass = $tmpStr;
   }

// --------------------------------------------------------------------
   
}
/* End of file sortable_grid.php */
/* Location: ./application/libraries/sortable_grid.php */
