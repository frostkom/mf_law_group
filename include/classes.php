<?php

class mf_law_group
{
	function __construct($id = 0)
	{
		if($id > 0)
		{
			$this->id = $id;
		}

		else
		{
			$this->id = check_var('intLawGroupID');
		}

		$this->is_updating = $this->id > 0;
	}

	function admin_menu()
	{
		$menu_root = 'mf_law_group/';
		$menu_start = $menu_root.'list/index.php';
		$menu_capability = 'edit_pages';

		$menu_title = __("Group", 'lang_law_group');
		add_submenu_page("mf_law/list/index.php", $menu_title, $menu_title, $menu_capability, $menu_start);

		$menu_title = __("Add New", 'lang_law_group');
		add_submenu_page("mf_law/index.php", $menu_title, $menu_title, $menu_capability, $menu_root.'create/index.php');
	}

	function deleted_user($user_id)
	{
		global $wpdb;

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."law_group SET userID = '%d' WHERE userID = '%d'", get_current_user_id(), $user_id));
	}

	function fetch_request()
	{
		$this->id2 = check_var('intLawGroupID2');
		$this->name = check_var('strLawGroupName');
		$this->order = check_var('intLawGroupOrder');
	}

	function save_data()
	{
		global $wpdb, $error_text, $done_text;

		$out = "";

		if(isset($_POST['btnLawGroupCreate']) && wp_verify_nonce($_POST['_wpnonce_law_group_create'], 'law_group_create_'.$this->id))
		{
			if($this->name == '')
			{
				$error_text = __("Please, enter all required fields", 'lang_law_group');
			}

			else
			{
				if($this->id > 0)
				{
					$this->update();
				}

				else
				{
					$wpdb->get_results($wpdb->prepare("SELECT lawGroupID FROM ".$wpdb->prefix."law_group WHERE lawGroupName = '%d' LIMIT 0, 1", $this->name));

					if($wpdb->num_rows > 0)
					{
						$error_text = __("There is already a type with that name. Try with another one.", 'lang_law_group');
					}

					else
					{
						$this->create();
					}
				}

				if($wpdb->rows_affected > 0)
				{
					echo "<script>location.href='".admin_url("admin.php?page=mf_law_group/list/index.php&".($this->is_updating ? "updated" : "created"))."'</script>";
				}
			}
		}

		else if(isset($_REQUEST['btnLawGroupDelete']) && $this->id > 0 && wp_verify_nonce($_REQUEST['_wpnonce_law_group_delete'], 'law_group_delete_'.$this->id))
		{
			$this->trash();

			$done_text = __("The information was deleted", 'lang_law_group');
		}

		else if(isset($_GET['created']))
		{
			$done_text = __("The information was created", 'lang_law_group');
		}

		else if(isset($_GET['updated']))
		{
			$done_text = __("The information was updated", 'lang_law_group');
		}

		return $out;
	}

	function get_from_db()
	{
		global $wpdb;

		if($this->id > 0 && !isset($_POST['btnLawGroupCreate']))
		{
			if(isset($_GET['recover']))
			{
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."law_group SET lawGroupDeleted = '0' WHERE lawGroupID = '%d'", $this->id));
			}

			$result = $wpdb->get_results($wpdb->prepare("SELECT lawGroupID2, lawGroupName, lawGroupOrder FROM ".$wpdb->prefix."law_group WHERE lawGroupID = '%d'", $this->id));
			$r = $result[0];
			$this->id2 = $r->lawGroupID2;
			$this->name = $r->lawGroupName;
			$this->order = $r->lawGroupOrder;
		}
	}

	function is_used()
	{
		global $wpdb;

		$wpdb->get_results($wpdb->prepare("SELECT lawGroupID FROM ".$wpdb->prefix."law2group WHERE lawGroupID = '%d' LIMIT 0, 1", $this->id));
		$law2group_amount = $wpdb->num_rows;

		$wpdb->get_results($wpdb->prepare("SELECT lawGroupID FROM ".$wpdb->prefix."law_group WHERE lawGroupID2 = '%d' LIMIT 0, 1", $this->id));
		$subgroup_amount = $wpdb->num_rows;

		return $law2group_amount > 0 || $subgroup_amount > 0 ? true : false;
	}

	function get_group_select($data = array())
	{
		if(!isset($data['levels'])){	$data['levels'] = 2;}
		if(!isset($data['multiple'])){	$data['multiple'] = false;}

		$tbl_group = new mf_law_group_table();

		$tbl_group->select_data(array(
			'select' => "lawGroupID, lawGroupID2, lawGroupName",
			'order_by' => "lawGroupOrder",
			'sort_data' => true,
		));

		$arr_data = array();

		if($tbl_group->num_rows > 0)
		{
			if($data['multiple'] == false)
			{
				$arr_data[''] = "-- ".__("Choose group here", 'lang_law_group')." --";
			}

			foreach($tbl_group->data as $r)
			{
				if($data['levels'] > 1 || $r['lawGroupID2'] == 0)
				{
					$arr_data[$r['lawGroupID']] = ($r['lawGroupID2'] > 0 ? "&mdash; " : "").$r['lawGroupName'];
				}
			}
		}

		return $arr_data;
	}

	function get_name()
	{
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT lawGroupName FROM ".$wpdb->prefix."law_group WHERE lawGroupID = '%d'", $this->id));
	}

	function find($value)
	{
		global $wpdb;

		$id = $wpdb->get_var($wpdb->prepare("SELECT lawGroupID FROM ".$wpdb->prefix."law_group WHERE lawGroupName = %s AND lawGroupDeleted = '0'", $value));

		return $id;
	}

	function create()
	{
		global $wpdb;

		if($this->name != '')
		{
			$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."law_group SET lawGroupID2 = '%d', lawGroupName = %s, lawGroupOrder = '%d', lawGroupCreated = NOW(), userID = '%d'", $this->id2, $this->name, $this->order, get_current_user_id()));

			$this->id = $wpdb->insert_id;
		}

		return $this->id;
	}

	function update()
	{
		global $wpdb;

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."law_group SET lawGroupID2 = '%d', lawGroupName = %s, lawGroupOrder = '%d' WHERE lawGroupID = '%d'", $this->id2, $this->name, $this->order, $this->id));
	}

	function trash($id = 0)
	{
		global $wpdb;

		if($id > 0)
		{
			$this->id = $id;
		}

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."law_group SET lawGroupDeleted = '1', lawGroupDeletedID = '%d', lawGroupDeletedDate = NOW() WHERE lawGroupID = '%d'", get_current_user_id(), $this->id));
	}

	/*function delete($id = 0)
	{
		global $wpdb;

		if($id > 0)
		{
			$this->id = $id;
		}

		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."law_group WHERE lawGroupID = '%d'", $this->id));
	}*/
}

class mf_law_group_table extends mf_list_table
{
	function set_default()
	{
		global $wpdb;

		$this->arr_settings['query_from'] = $wpdb->prefix."law_group";
		$this->arr_settings['query_select_id'] = "lawGroupID";
		$this->arr_settings['query_all_id'] = "0";
		$this->arr_settings['query_trash_id'] = "1";
		$this->orderby_default = "lawGroupOrder ASC, lawGroupName";

		$this->arr_settings['has_autocomplete'] = true;
		$this->arr_settings['plugin_name'] = 'mf_law_group';
	}

	function init_fetch()
	{
		if($this->search != '')
		{
			$this->query_where .= ($this->query_where != '' ? " AND " : "")."(lawGroupName LIKE '".$this->filter_search_before_like($this->search)."' OR SOUNDEX(lawGroupName) = SOUNDEX('".$this->search."'))";
		}

		$this->set_views(array(
			'db_field' => 'lawGroupDeleted',
			'types' => array(
				'0' => __("All", 'lang_law_group'),
				'1' => __("Trash", 'lang_law_group')
			),
		));

		$arr_columns = array(
			//'cb' => '<input type="checkbox">',
			'lawGroupName' => __("Name", 'lang_law_group'),
			'amount' => __("Amount", 'lang_law_group'),
			'lawGroupOrder' => __("Order", 'lang_law_group'),
		);

		$this->set_columns($arr_columns);

		$this->set_sortable_columns(array(
			'lawGroupName',
			'lawGroupOrder',
		));
	}

	function sort_data()
	{
		$arr_parents = $arr_children = array();

		foreach($this->data as $row)
		{
			$intLawGroupID2 = $row['lawGroupID2'];

			if($intLawGroupID2 > 0)
			{
				$arr_children[$intLawGroupID2][] = $row;
			}

			else
			{
				$arr_parents[] = $row;
			}
		}

		$this->data = array();

		foreach($arr_parents as $parent)
		{
			$this->data[] = $parent;

			if(isset($arr_children[$parent['lawGroupID']]))
			{
				foreach($arr_children[$parent['lawGroupID']] as $child)
				{
					$this->data[] = $child;
				}
			}
		}
	}

	function column_default($item, $column_name)
	{
		global $wpdb;

		$out = "";

		$intLawGroupID = $item['lawGroupID'];

		$obj_law_group = new mf_law_group($intLawGroupID);

		switch($column_name)
		{
			case 'lawGroupName':
				$item_value = $item['lawGroupName'];
				$intLawGroupDeleted = $item['lawGroupDeleted'];

				$post_edit_url = admin_url("admin.php?page=mf_law_group/create/index.php&intLawGroupID=".$intLawGroupID);

				$actions = array();

				if($intLawGroupDeleted == 0)
				{
					if(IS_ADMIN)
					{
						$actions['edit'] = "<a href='".$post_edit_url."'>".__("Edit", 'lang_law_group')."</a>";

						if(!$obj_law_group->is_used())
						{
							$actions['delete'] = "<a href='".wp_nonce_url(admin_url("admin.php?page=mf_law_group/list/index.php&btnLawGroupDelete&intLawGroupID=".$intLawGroupID), 'law_group_delete_'.$intLawGroupID, '_wpnonce_law_group_delete')."'>".__("Delete", 'lang_law_group')."</a>";
						}
					}
				}

				else
				{
					$actions['recover'] = "<a href='".$post_edit_url."&recover'>".__("Recover", 'lang_law_group')."</a>";
				}

				$out .= "<a href='".$post_edit_url."'>"
					.($item['lawGroupID2'] > 0 ? "&mdash; " : "")
					.$item_value
				."</a>"
				.$this->row_actions($actions);
			break;

			case 'amount':
				$wpdb->get_results($wpdb->prepare("SELECT lawGroupID FROM ".$wpdb->prefix."law2group WHERE lawGroupID = '%d'", $intLawGroupID));

				echo $wpdb->num_rows;
			break;

			default:
				if(isset($item[$column_name]))
				{
					$out .= $item[$column_name];
				}
			break;
		}

		return $out;
	}
}