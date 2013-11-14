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
/**
 * @method static \cs\modules\Home\Goods instance($check = false)
 */
class Goods {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]goods';
	protected $data_model	= [
		'id'		=> 'int',
		'giver'		=> 'int',
		'comment'	=> 'text',
		'driver'	=> 'int',
		'date_from'	=> 'int:0',
		'date_to'	=> 'int:0',
		'time_from'	=> 'int:0',
		'time_to'	=> 'int:0',
		'lat'		=> 'float',
		'lng'		=> 'float',
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
					'address'
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
		$date	= _trim(explode('-', $date));
		$date	= [
			explode('.', $date[0]),
			explode('.', $date[1])
		];
		$date	= [
			mktime(0, 0, 0, $date[0][1], $date[0][0], $date[0][2]),
			mktime(0, 0, 0, $date[1][1], $date[1][0], $date[1][2])
		];
		$time	= _trim(explode('-', str_replace(':', '.', $time)));
		return $this->create_simple([
			$giver,
			$comment,
			0,
			0,
			$date[0],
			$date[1],
			$time[0],
			$time[1],
			$coordinates[0],
			$coordinates[1],
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