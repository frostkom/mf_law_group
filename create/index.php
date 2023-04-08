<?php

$error_text = "";

$obj_law_group = new mf_law_group();
$obj_law_group->fetch_request();
echo $obj_law_group->save_data();
$obj_law_group->get_from_db();

echo "<div class='wrap'>
	<h2>".($obj_law_group->id > 0 ? __("Update", 'lang_law_group') : __("Add New", 'lang_law_group'))."</h2>"
	.get_notification()
	."<div id='poststuff'>
		<form method='post' action='' class='mf_form mf_settings'>
			<div id='post-body' class='columns-2'>
				<div id='post-body-content'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Name", 'lang_law_group')."</span></h3>
						<div class='inside'>"
							.show_textfield(array('name' => 'strLawGroupName', 'text' => __("Name", 'lang_law_group'), 'value' => $obj_law_group->name, 'maxlength' => 100, 'required' => true, 'xtra' => "autofocus"))
						."</div>
					</div>
				</div>
				<div id='postbox-container-1'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Save", 'lang_law_group')."</span></h3>
						<div class='inside'>"
							.show_submit(array('name' => 'btnLawGroupCreate', 'text' => ($obj_law_group->id > 0 ? __("Update", 'lang_law_group') : __("Add", 'lang_law_group'))))
							.input_hidden(array('name' => 'intLawGroupID', 'value' => $obj_law_group->id))
							.wp_nonce_field('law_group_create_'.$obj_law_group->id, '_wpnonce_law_group_create', true, false)
						."</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Settings", 'lang_law_group')."</span></h3>
						<div class='inside'>"
							.show_textfield(array('type' => 'number', 'name' => 'intLawGroupOrder', 'text' => __("Order", 'lang_law_group'), 'value' => $obj_law_group->order));

							$arr_data = $obj_law_group->get_group_select(array('levels' => 1));

							echo show_select(array('data' => $arr_data, 'name' => 'intLawGroupID2', 'text' => __("Subgroup to", 'lang_law_group'), 'value' => $obj_law_group->id2))
						."</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>";