<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Home;
use			cs\User,
			cs\CRUD,
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
	 * Get good
	 *
	 * @param int|int[]		$id
	 *
	 * @return array|bool
	 */
	function get ($id) {
		$result	= $this->read_simple($id);
		$User	= User::instance();
		if (is_array($id)) {
			foreach ($result as &$r) {
				$r	+= $User->get_data([
					'phone',
					'address',
					'coordinates',
					'date',
					'time'
				], $r['giver']);
			}
		}
	}
	/**
	 * Add new good
	 *
	 * @param int		$giver
	 * @param string	$comment
	 * @param string	$phone
	 * @param string	$address
	 * @param string	$coordinates	JSON [lat, lng]
	 * @param string	$date
	 * @param string	$time
	 *
	 * @return bool|int
	 */
	function add ($giver, $comment, $phone, $address, $coordinates, $date, $time) {
		User::instance()->set_data([
			'phone'			=> $phone,
			'address'		=> $address,
			'coordinates'	=> $coordinates,
			'date'			=> $date,
			'time'			=> $time
		], null, $giver);
		return $this->create_simple([
			$giver,
			$comment,
			0,
			0,
			-1
		]);
	}
	/**
	 * Set driver
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
	 * Set success of good delivery
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