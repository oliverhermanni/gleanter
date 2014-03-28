<?php
class Invitation_model extends MY_Model{
	var $primary_table = 'invitations';

	var $fields = array(
		'id',
		'email',
		'code',
		'created',
		'activated',
		'active',
		'created_by'
	);

	var $required_fields = array(
		'email',
		'code'
	);

	public function disable_code($code) {
		$get_params = array(
			'code' => $code,
			'active' => 1
		);

		$set_params = array(
			'active' => 0,
			'activated' => mysql_datetime()
		);
		$this->db->update($this->primary_table,$set_params,$get_params);
	}
}
