<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Wee Pixel AddSelect Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		John Clark - Wee Pixel
 * @link		http://weepixel.com
 */

class Wp_addselect_ext
{

	public $settings = array();
	public $name = "WP AddSelect";
	public $version = "0.9.0";
	public $description = '';
	public $settings_exist = FALSE;
	public $docs_url = "http://weepixel.com";
	
	private $EE;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = FALSE)
	{
		// Get global instance
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	// --------------------------------------------------------------------

	/**
	* Settings
	*
	* @return	array
	*/
	function settings()
	{
		return $settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Save select options
	 *
	 * Saves updated select options to the corresponding fieldtype 
	 *
	 * @return void
	 */
	public function save_select_options($id, $meta, $data)
	{
		$query = $this->EE->db->get_where("exp_channel_fields", "field_type = 'wp_addselect'");
		if ($query->num_rows() > 0) {
			
			// Check for new data for each AddSelect field
			foreach($query->result() as $field) {
				$check_field = "wp_addselect_field_id_" . $field->field_id . "_new_item";
				
				// If updated select options data for field, save to field_list_items in exp_channel_fields
				if (in_array($check_field, $data) && !empty($data[$check_field])) {
					
					$items = $field->field_list_items . "\n" . $data[$check_field];
					
					if ($this->EE->db->update(
						"exp_channel_fields",
						array("field_list_items" => $items),
						"field_id = ".$field->field_id)
					){
						$this->EE->db->update(
							"exp_channel_data",
							array("field_id_".$field->field_id => $data[$check_field]),
							array('entry_id'=>$id)
						);
					}
					
				}
				
			}
			
		}
	}

	// --------------------------------------------------------------------

	/**
	* Activate extension
	*
	* @return	null
	*/
	function activate_extension()
	{
		// data to insert
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'save_select_options',
			'hook'		=> 'entry_submission_absolute_end',
			'priority'	=> 10,
			'version'	=> $this->version,
			'enabled'	=> 'y',
			'settings'	=> serialize($this->settings)
		);

		// insert in database
		$this->EE->db->insert('exp_extensions', $data);
	}

	// --------------------------------------------------------------------

	/**
	* Update extension
	*
	* @param	string	$current
	* @return	null
	*/
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		// init data array
		$data = array();

		// Add version to data array
		$data['version'] = $this->version;

		// Update records using data array
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('exp_extensions', $data);
	}

	// --------------------------------------------------------------------

	/**
	* Disable extension
	*
	* @return	null
	*/
	function disable_extension()
	{
		// Delete records
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('exp_extensions');
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file ext.wp_addselect.php */
/* Location: /system/expressionengine/third_party/wp_addselect/ext.wp_addselect.php */