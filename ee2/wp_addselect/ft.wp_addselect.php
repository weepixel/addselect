<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

// --------------------------------------------------------------------

/**
 * Wee Pixel AddSelect Fieldtype
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Fieldtype
 * @author		John Clark - Wee Pixel
 * @link		http://weepixel.com
 */

class Wp_addselect_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'WP AddSelect',
		'version'	=> '0.9.2'
	);

	var $has_array_data = TRUE;

	/**
	 * Display AddSelect field
	 *
	 * @param array $data
	 * @return string
	 */
	function display_field($data)
	{
		$this->EE->load->helper('custom_field');
		$values = decode_multi_field($data);
		$field_options = $this->_get_field_options($data);

		// Field form
		$r = form_dropdown(
			"select_" . $this->field_name, $field_options, $values, 'dir="' .
			$this->settings['field_text_direction'] .
			'" style="float:left;margin-right:10px;margin-top:4px" id="' .
			$this->field_id . '"'
		);
		
		$r.= "<span style='float:left;margin-top:4px;clear:right;'>or <a class='wp_addselect_add' href='#' title='Add item'>add item</a></span>";
		
		// Add item text field (hidden initially)
		$r.= form_input(
			array(
				'name'  => $this->field_name,
				'value' => $data,
				'class' => 'field',
				'style' => 'display:none'
			)
		);
		
		// Add item button
		$r.= form_input(
			array(
				'label' => 'Add new item',
				'name'  => 'wp_addselect_'.$this->field_name.'_new_item',
				'id'    => 'wp_addselect_'.$this->field_name.'_new_item',
				'class' => 'field',
				'style' => 'width:200px; float:left;margin-right:10px; display:none'
			)
		);
		
		$r.= "<span style='float:left;margin-top:4px;display:none'><a class='wp_addselect_remove' href='#' title='Remove'>remove</a></span>";
		
		// Field javscript
		$js = 'jQuery(document).ready(function(){
			
			$("input[name=' . $this->field_name . ']")
			.prev("span").children("a.wp_addselect_add")
			.click(function(){
				$(this).parent().hide();
				$(this).parent().siblings("select").hide();
				$("#wp_addselect_'.$this->field_name.'_new_item").show().parent("span").show();
				$(this).parent().siblings("span").show();
				return false;
			});
					
			$("a.wp_addselect_remove").click(function(){
				$(this).parent().hide();
				$(this).parent().siblings("select").show();
				$("#wp_addselect_'.$this->field_name.'_new_item").val("").hide().parent("span").hide();
				$(this).parent().siblings("span").show();
				return false;
			});
					
			if("'.$data.'"!="" && $("option", "select[name=select_'.$this->field_name.']").filter(function(){return this.value == "'.html_entity_decode($data, ENT_QUOTES).'"}).length == 0){
				$("input[name=wp_addselect_' . $this->field_name . '_new_item]")
				.show().val($("input[name='.$this->field_name.']").val());
				$("input[name='.$this->field_name.']").prev("span").hide();
				$("#wp_addselect_'.$this->field_name.'_new_item").next("span").show();
				$("select[name=select_'.$this->field_name.']").hide();
			}
					
			// $("input[name='.$this->field_name.']").attr("value", $("select[name=select_'.$this->field_name.']").val());
			
			$("#wp_addselect_'.$this->field_name.'_new_item").keypress(function(e){
				if(e.which == 13){
					return false;
				}
			});
					
			$("select[name=select_'.$this->field_name.']").change(function(){
				$("input[name='.$this->field_name.']").attr("value", $(this).val());
			});
			
			$("#wp_addselect_'.$this->field_name.'_new_item").keyup(function(e){
				if(e.which == 13){
					return false;
				} else {
					$("input[name='.$this->field_name.']").attr("value", $(this).val());
				}
			});
					
		});
		';
		$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
		
		return $r;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display AddSelect settings
	 *
	 * @param array $data
	 * @return void
	 */
	function display_settings($data)
	{
		$this->EE->table->add_row(
			'<p class="field_format_option select_format"><strong>Default options</strong></p>',
			'<p class="field_format_option select_format_n">'.
				lang('field_list_instructions').BR.
				form_textarea(array('id'=>'wp_addselect_field_list_items','name'=>'wp_addselect_field_list_items', 'rows'=>10, 'cols'=>50, 'value'=>$data['field_list_items'])).
			'</p>'.
			form_hidden(array('wp_addselect_field_pre_populate'=>'n'))
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display AddSelect matrix cell
	 *
	 * @param array $data
	 * @return void
	 */
	
	function display_cell($data)
	{
		$this->EE->load->helper('custom_field');

		$values = decode_multi_field($data);
		$field_options = $this->_get_field_options($data);
		
		$r = form_dropdown(
			"select_".$this->cell_name,
			$field_options,
			$values,
			'dir="' . $this->settings['field_text_direction'] .
			'"style="float:left;margin-right:10px;margin-top:4px" id="' .
			$this->field_id.'"'
		);
		
		$r.= "<span style='float:left;margin-top:4px'>or <a class='wp_addselect_add' href='#' title='Add item'>add item</a></span>";
		
		$r.= form_input(
			array(
				'name'  => $this->cell_name,
				'value' => html_entity_decode($data, ENT_QUOTES),
				'class' => 'wp_addselect_value field',
				'style' => 'display:none'
			)
		);
		
		$r.= form_input(
			array(
				'label' => 'Add new item',
				'name'  => 'wp_addselect_'.$this->cell_name.'_new_item',
				'class' => 'wp_addselect_new field',
				'style' => 'width:200px; float:left;margin-right:10px; display:none'
			)
		);
		
		$r.= "<span style='float:left;margin-top:4px;display:none'><a class='wp_addselect_remove' href='#' title='Remove'>remove</a></span>";
		
		$this->EE->load->library('javascript');

		$this->EE->javascript->output('
			Matrix.bind("wp_addselect", "display", function(cell){
					
					var name = cell.field.id+"["+cell.row.id+"]["+cell.col.id+"]";
								
					var input = $(cell.dom.$td).find(".wp_addselect_new:input");
					if (input.length > 0 && input.attr("name").match(new RegExp(/^wp_addselect.*_new_item$/))) {
								input.attr("name", input.attr("name").replace(/[\[\]]/g, "_"));
					}
					
					$("a.wp_addselect_add", cell.dom.$td).click(function(){
						$(this).parent().hide();
						$(this).parent().siblings("select").hide();
						$(input, cell.dom.$td).show().parent("span").show();
						$(this).parent().siblings("span").show();
						return false;
					});
					
					$("a.wp_addselect_remove").click(function(){
						$(this).parent().hide();
						$(this).parent().siblings("select").show();
						$(this).parent().siblings("input").val("").hide();
						$(this).parent().siblings("span").show();
						return false;
					});
																		
					if($(".wp_addselect_value", cell.dom.$td).val() !="" && $("option", $("select", cell.dom.$td)).filter(function(){return this.value == $(".wp_addselect_value", cell.dom.$td).val();}).length == 0){
						$(".wp_addselect_new", cell.dom.$td).show().val($(".wp_addselect_value", cell.dom.$td).val());
						$(".wp_addselect_add", cell.dom.$td).parent("span").hide();
						$("select", cell.dom.$td).hide();
						$(".wp_addselect_remove", cell.dom.$td).parent("span").show();
					} else {
						$("select", cell.dom.$td).val($(".wp_addselect_value", cell.dom.$td).val());
					}
					
					$(".wp_addselect", cell.dom.$td).keypress(function(e){
						if(e.which == 13){
							return false;
						}
					});
										
					$("select", cell.dom.$td).change(function(){
						$(".wp_addselect_value", cell.dom.$td).attr("value",$("select", cell.dom.$td).val());
					});
					
					$(input, cell.dom.$td).keyup(function(e){
						if(e.which == 13){
							return false;
						} else {
							$(input).prev().attr("value", $(this).val());
						}
					});

			});
		');
		
		return $r;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display AddSelect matrix cell settings
	 *
	 * @param array $settings
	 * @return array
	 */
	function display_cell_settings($settings)
	{
		return array(
			array('Options', form_textarea(array('id'=>'field_list_items','name'=>'field_list_items', 'rows'=>10, 'cols'=>20, 'value'=>$settings['field_list_items']))),
			array("", form_hidden(array('field_pre_populate'=>'n')))
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save field data
	 *
	 * @param array $data
	 * @return array
	 */
	function save($data)
	{
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save matrix cell data
	 *
	 * @param array $data
	 * @return array
	 */
	function save_cell($data)
	{
		$s = $this->settings;
		
		$postnew = 'wp_addselect_'.$s['field_name']
				.'_'.$s['row_name'].'__'.$s['col_name'].'__new_item';
				
		if (isset($_POST[$postnew]) && !empty($_POST[$postnew]))
		{
			$new = $_POST[$postnew];
			$this->EE->db->where("col_id", $s['col_id']);
			$query = $this->EE->db->get("exp_matrix_cols");
			
			if ($query->num_rows() > 0)
			{
				$col = $query->row();
				$col_settings = unserialize(base64_decode($col->col_settings));
				$fl_items_arr = explode("\n", $col_settings['field_list_items']);
				if (!in_array($new, $fl_items_arr)) {
					$col_settings['field_list_items'] = $col_settings['field_list_items']."\n".$new;
					$this->EE->db->update("exp_matrix_cols", array("col_settings" => base64_encode(serialize($col_settings))), "col_id = ".$s['col_id']);
				}
				return $new;
			}
		}
		else
		{
			return $data;
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get field select options
	 *
	 * @param array $data
	 * @return array
	 */
	function _get_field_options($data)
	{
		$field_options = array();

		if ($this->settings['field_pre_populate'] == 'n')
		{
			if ( ! is_array($this->settings['field_list_items']))
			{
				foreach (explode("\n", trim($this->settings['field_list_items'])) as $v)
				{
					$v = trim($v);
					$field_options[form_prep($v)] = form_prep($v);
				}
			}
			else
			{
				$field_options = $this->settings['field_list_items'];
			}
		}
		else
		{
			// We need to pre-populate this menu from an another channel custom field

			$this->EE->db->select('field_id_'.$this->settings['field_pre_field_id']);
			$this->EE->db->where('channel_id', $this->settings['field_pre_channel_id']);
			$pop_query = $this->EE->db->get('channel_data');

			$field_options[''] = '--';

			if ($pop_query->num_rows() > 0)
			{
				foreach ($pop_query->result_array() as $prow)
				{
					$selected = ($prow['field_id_'.$this->settings['field_pre_field_id']] == $data) ? 1 : '';
					$pretitle = substr($prow['field_id_'.$this->settings['field_pre_field_id']], 0, 110);
					$pretitle = str_replace(array("\r\n", "\r", "\n", "\t"), " ", $pretitle);
					$pretitle = form_prep($pretitle);

					$field_options[form_prep($prow['field_id_'.$this->settings['field_pre_field_id']])] = $pretitle;
				}
			}
		}
		
		return $field_options;
	}
	
}

// END Wp_addselect_ft class

/* End of file ft.wp_addselect.php */
/* Location: /system/expressionengine/third_party/wp_addselect/ft.wp_addselect.php */