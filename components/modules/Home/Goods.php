<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\CRUD,
			cs\Singleton;

class Goods {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]goods';
	protected $data_model	= [
		'id'		=> 'int',
		'giver'		=> 'int',
		'comment'	=> 'text',
		'driver'	=> 'int',
		'given'		=> 'int',
		'success'	=> 'int:-1..1'
	];

	protected function cdb () {
		return '0';
	}
	/**
	 * Get giver's reputation
	 *
	 * @param int|int[]		$id
	 *
	 * @return array|bool
	 */
	function get ($id) {
		return $this->read_simple($id);
	}
	/**
	 * Add new good
	 *
	 * @param int		$giver
	 * @param string	$comment
	 *
	 * @return bool|int
	 */
	function add ($giver, $comment) {
		return $this->create_simple([
			$giver,
			$comment,
			0,
			0,
			-1
		]);
	}
	/**
	 * Change giver reputation
	 *
	 * @param int	$id
	 * @param int	$driver
	 *
	 * @return bool
	 */
	function set_driver ($id, $driver) {
		$data	= $this->get($id);
		if (!$data || $data['driver']) {
			return false;
		}
		$data['driver']	= $driver;
		$data['given']	= TIME;
		return $this->update_simple($data);
	}
	/**
	 * Set driver's password
	 *
	 * @param int	$id
	 * @param int	$success	0 or 1
	 *
	 * @return bool
	 */
	function set_success ($id, $success) {
		$data	= $this->get($id);
		if (!$data) {
			return false;
		}
		$data['success']	= $success;
		return $this->update_simple($data);
	}
}