<?php

if(!defined('ABSPATH'))
{
	$folder = str_replace("/wp-content/plugins/mf_law_group/include/api", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

do_action('run_cache', array('suffix' => 'json'));

$json_output = array();

$type = check_var('type', 'char');

if(is_user_logged_in())
{
	switch($type)
	{
		case 'table_search':
			$tbl_group = new mf_law_group_table();

			$tbl_group->select_data(array(
				'select' => "lawGroupID, lawGroupID2, lawGroupName",
				'limit' => 0, 'amount' => 10
			));

			foreach($tbl_group->data as $r)
			{
				$json_output[] = $r['lawGroupName'];
			}
		break;
	}
}

echo json_encode($json_output);