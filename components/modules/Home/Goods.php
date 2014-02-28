<?php
/**
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
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
		'id'				=> 'int:0',
		'giver'				=> 'int:0',
		'comment'			=> 'text',
		'date_from'			=> 'int:0',
		'date_to'			=> 'int:0',
		'time_from'			=> 'int:0',
		'time_to'			=> 'int:0',
		'lat'				=> 'float',
		'lng'				=> 'float',
		'added'				=> 'int:0',
		'driver'			=> 'int:0',
		'given'				=> 'int:0',
		'reserved'			=> 'int:0',
		'reserved_driver'	=> 'int:0',
		'success'			=> 'int:-1..1',
		'address'			=> 'text',
		'phone'				=> 'text'
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
				$this->prepare_get_data($r, $User);
			}
		} else {
			$this->prepare_get_data($result, $User);
		}
		return $result;
	}
	/**
	 * @param array	$r
	 * @param User	$User
	 */
	protected function prepare_get_data (&$r, $User) {
		$r['username']	= $User->username($r['giver']);
		$r['date']		= date('d.m', $r['date_from']).' - '.date('d.m', $r['date_to']);
		$r['time']		=
			str_replace('.', ':', str_pad(str_pad($r['time_from'], 3, ':'), 5, '0')).
			' - '.
			str_replace('.', ':', str_pad(str_pad($r['time_to'], 3, ':'), 5, '0'));
		if ($User->admin()) {
			$r['profile_link'] = $this->db()->qfs([
				"SELECT `profile`
				FROM `[prefix]users_social_integration`
				WHERE `id` = '%s'
				LIMIT 1",
				$User->id
			]);
		}
	}
	/**
	 * Add new good
	 *
	 * @param int		$giver
	 * @param string	$comment
	 * @param string	$username
	 * @param string	$phone
	 * @param string	$address
	 * @param string	$coordinates	JSON [lat, lng]
	 * @param string	$date
	 * @param string	$time
	 *
	 * @return bool|int
	 */
	function add ($giver, $comment, $username, $phone, $address, $coordinates, $date, $time) {
		$User			= User::instance();
		$User->set('username', $username, $giver);
		$User->set_data([
			'phone'			=> xap($phone),
			'address'		=> xap($address),
			'coordinates'	=> $coordinates
		], null, $giver);
		$date			= _trim(explode('-', $date));
		$date			= [
			explode('.', $date[0]),
			explode('.', $date[1])
		];
		$date			= [
			mktime(0, 0, 0, $date[0][1], $date[0][0], $date[0][2]),
			mktime(23, 59, 59, $date[1][1], $date[1][0], $date[1][2])
		];
		$time			= _trim(explode('-', str_replace(':', '.', $time)));
		$coordinates	= _json_decode($coordinates);
		return $this->create_simple([
			$giver,
			$comment,
			$date[0],
			$date[1],
			$time[0],
			$time[1],
			$coordinates[0],
			$coordinates[1],
			TIME,
			0,
			0,
			0,
			0,
			-1,
			$address,
			$phone
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
		if ($this->update_simple($data)) {
			$Volunteers	= Volunteers::instance();
			$Volunteers->change_reputation($data['driver'], $success);
			if ($data['driver'] != $data['giver']) {
				$Volunteers->change_reputation($data['giver'], $success ?: .5);
			}
		}
		return true;
	}
	/**
	 * Get good added by specified giver
	 *
	 * @param int			$giver
	 *
	 * @return array[]|bool
	 */
	function unconfirmed ($giver) {
		return $this->get(
			$this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				WHERE
					`giver`		= '%s' AND
					`given`		= '0' AND
					`success`	= '-1'",
				$giver
			])
		);
	}
	/**
	 * Good reservation for 24 hours
	 *
	 * @param int	$id
	 * @param int	$driver
	 *
	 * @return bool
	 */
	function add_reservation ($id, $driver) {
		$this->db_prime()->q(
			"UPDATE `$this->table`
			SET
				`reserved`			= '%s',
				`reserved_driver`	= '%s'
			WHERE
				`id`		= '%s' AND
				`reserved`	< '%s'
			LIMIT 1",
			TIME + 24 * 60 * 60,	//24 hours
			$driver,
			(int)$id,
			TIME
		);
		return $this->db_prime()->affected() == 1;
	}
	/**
	 * Cancel reservation
	 *
	 * @param int	$id
	 * @param int	$driver
	 *
	 * @return bool
	 */
	function del_reservation ($id, $driver) {
		$this->db_prime()->q(
			"UPDATE `$this->table`
			SET
				`reserved`			= 0,
				`reserved_driver`	= 0
			WHERE
				`id`				= '%s' AND
				`reserved`			>= '%s' AND
				`reserved_driver`	= '%s'
			LIMIT 1",
			(int)$id,
			TIME,
			$driver
		);
		return $this->db_prime()->affected() == 1;
	}
	/**
	 * Search among goods
	 *
	 * @param array	$params	date/time
	 * @param int	$driver
	 *
	 * @return array
	 */
	function search ($params, $driver) {
		$where	= [];
		$subst	= [];
		if (isset($params['date'])) {
			$where[]	= "`date_from` <= %s AND `date_to` >= %s";
			$subst[]	= (int)$params['date'];
			$subst[]	= (int)$params['date'];
		}
		if (isset($params['time'])) {
			$where[]	= "((`time_from` >= %s AND `time_from` <= %s) OR (`time_to` >= %s AND `time_from` <= %s))";
			$subst[]	= (float)$params['time'][0];
			$subst[]	= (float)$params['time'][1];
			$subst[]	= (float)$params['time'][0];
			$subst[]	= (float)$params['time'][1];
		}
		if (isset($params['giver']) && $params['giver']) {
			$where[]	= "`giver` = '%s'";
			$subst[]	= $params['giver'];
		} else {
			$where[]	= "`given` = 0 AND  `success` = '-1'";
			if (isset($params['reserved']) && $params['reserved']) {
				$where[]	= "`reserved` > ".TIME." AND `reserved_driver` = '%s'";
				$subst[]	= $driver;
			} else {
				$where[]	= '`reserved` < '.TIME;
			}
		}
		$where	= 'WHERE '.implode(' AND ', $where);
		return $this->get(
			$this->db()->qfas([
				"SELECT `id`
				FROM `$this->table`
				$where
				ORDER BY `id` DESC",
				$subst
			])
		);
	}
	/**
	 * Get goods that needs approving
	 *
	 * @return array|bool
	 */
	function for_approving () {
		return $this->get(
			$this->db()->qfas(
				"SELECT `id`
				FROM `$this->table`
				WHERE
					`given`		!= 0 AND
					`success`	= '-1'"
			)
		);
	}
	/**
	 * Delete good
	 *
	 * @param int	$id
	 *
	 * @return bool
	 */
	function delete ($id) {
		return (bool)$this->db_prime()->q(
			"DELETE FROM `$this->table`
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		);
	}
}
