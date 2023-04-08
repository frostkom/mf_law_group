<?php
/*
Plugin Name: MF Law Group
Plugin URI: 
Description: 
Version: 3.6.6
Author: Martin Fors
Author URI: http://martinfors.se
Text Domain: lang_law_group
Domain Path: /lang

Depends: MF Base, MF Law
*/

if(is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_law_group = new mf_law_group();

	add_action('cron_base', 'activate_law_group', mt_rand(1, 10));

	if(is_admin())
	{
		register_activation_hook(__FILE__, 'activate_law_group');
		register_uninstall_hook(__FILE__, 'uninstall_law_group');

		add_action('admin_menu', array($obj_law_group, 'admin_menu'));

		add_action('deleted_user', array($obj_law_group, 'deleted_user'));

		load_plugin_textdomain('lang_law_group', false, dirname(plugin_basename(__FILE__)).'/lang/');
	}

	function activate_law_group()
	{
		global $wpdb;

		require_plugin("mf_law/index.php", "MF Law");

		$default_charset = (DB_CHARSET != '' ? DB_CHARSET : 'utf8');

		$arr_add_column = $arr_update_column = $arr_add_index = array();

		$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."law_group (
			lawGroupID INT UNSIGNED NOT NULL AUTO_INCREMENT,
			lawGroupID2 INT UNSIGNED NOT NULL DEFAULT '0',
			lawGroupName VARCHAR(100),
			lawGroupOrder SMALLINT UNSIGNED,
			lawGroupCreated DATETIME,
			userID INT UNSIGNED DEFAULT '0',
			lawGroupDeleted ENUM('0', '1') NOT NULL DEFAULT '0',
			lawGroupDeletedDate DATETIME DEFAULT NULL,
			lawGroupDeletedID INT UNSIGNED DEFAULT '0',
			PRIMARY KEY (lawGroupID)
		) DEFAULT CHARSET=".$default_charset);

		$arr_add_column[$wpdb->prefix."law_group"] = array(
			//'' => "ALTER TABLE [table] ADD [column]  AFTER ",
		);

		$arr_update_column[$wpdb->prefix."law_group"] = array(
			//'' => "ALTER TABLE [table] DROP [column]",
		);

		$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."law2group (
			lawID INT UNSIGNED,
			lawGroupID INT UNSIGNED,
			KEY lawID (lawID),
			KEY lawGroupID (lawGroupID)
		) DEFAULT CHARSET=".$default_charset);

		$arr_add_index[$wpdb->prefix."law2group"] = array(
			//'' => "ALTER TABLE [table] ADD INDEX [column] ([column])",
		);

		add_columns($arr_add_column);
		update_columns($arr_update_column);
		add_index($arr_add_index);

		delete_base(array(
			'table' => "law_group",
			'field_prefix' => "lawGroup",
		));
	}

	function uninstall_law_group()
	{
		mf_uninstall_plugin(array(
			'tables' => array('law_group', 'law2group'),
		));
	}
}