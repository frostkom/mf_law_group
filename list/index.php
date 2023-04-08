<?php

$obj_law_group = new mf_law_group();
$obj_law_group->fetch_request();
echo $obj_law_group->save_data();

echo "<div class='wrap'>
	<h2>"
		.__("Group", 'lang_law_group')
		."<a href='".admin_url("admin.php?page=mf_law_group/create/index.php")."' class='add-new-h2'>".__("Add New", 'lang_law_group')."</a>
	</h2>"
	.get_notification();

	$tbl_group = new mf_law_group_table();

	$tbl_group->select_data(array(
		//'select' => "*",
		'sort_data' => true,
	));

	$tbl_group->do_display();

echo "</div>";