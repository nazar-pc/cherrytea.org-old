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
 * @method static \cs\modules\Home\Drivers instance($check = false)
 */
class Drivers {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]drivers';
	protected $data_model	= [
		'id'			=> 'int',
		'code'			=> 'text:6:',
		'password'		=> 'text',
		'active'		=> 'int:-1..1',
		'reputation'	=> 'int'
	];

	protected function cdb () {
		return '0';
	}
	/**
	 * Get driver's information
	 *
	 * @param int|int[]		$user
	 *
	 * @return array|bool
	 */
	function get ($user) {
		return $this->read_simple($user);
	}
	/**
	 * Find driver by his code
	 *
	 * @param string		$code
	 *
	 * @return array|bool
	 */
	function get_by_code ($code) {
		return $this->get(
			$this->db()->qfs([
				"SELECT `id`
				FROM `$this->table`
				WHERE `code` = '%s'",
				$code
			])
		);
	}
	/**
	 * Add new driver (inactive by default)
	 *
	 * @param int		$user
	 *
	 * @return bool|int
	 */
	function add ($user) {
		$code	= mt_rand(10000, 99999);
		while ($this->db_prime()->qfs(
			"SELECT `id`
			FROM `$this->table`
			WHERE `code` = '%s'
			LIMIT 1",
			$code
		)) {
			$code	= mt_rand(10000, 99999);
		}
		return $this->create_simple([
			$user,
			$code,
			substr(md5(MICROTIME.uniqid()), 0, 6),
			-1,
			0
		]);
	}
	/**
	 * Is this user active driver?
	 *
	 * @param int	$user
	 *
	 * @return bool
	 */
	function active ($user) {
		$driver	= User::instance()->get_data('driver', $user) ? $this->get($user) : false;
		return (bool)($driver && $driver['active'] === '1');
	}
	/**
	 * Activate driver
	 *
	 * @param int	$user
	 *
	 * @return bool
	 */
	function activate ($user) {
		return $this->db_prime()->q(
			"UPDATE `$this->table`
			SET `active` = '1'
			WHERE `id` = '%s'
			LIMIT 1",
			$user
		);
	}
	/**
	 * Deactivate driver
	 *
	 * @param int	$user
	 *
	 * @return bool
	 */
	function deactivate ($user) {
		return $this->db_prime()->q(
			"UPDATE `$this->table`
			SET `active` = '0'
			WHERE `id` = '%s'
			LIMIT 1",
			$user
		);
	}
	/**
	 * Change driver reputation
	 *
	 * @param int	$user
	 * @param int	$value	Positive or negative value
	 *
	 * @return bool
	 */
	function change_reputation ($user, $value) {
		$value	= (int)$value;
		return $this->db_prime()->q(
			"UPDATE `$this->table`
			SET `reputation` = `reputation` + $value
			WHERE `id` = '%s'
			LIMIT 1",
			$user
		);
	}
	/**
	 * Set driver's password
	 *
	 * @param int		$user
	 * @param string	$password
	 *
	 * @return bool
	 */
	function set_password ($user, $password) {
		$password	= (int)$password;
		return $this->db_prime()->q(
			"UPDATE `$this->table`
			SET `password` = '%s'
			WHERE `id` = '%s'
			LIMIT 1",
			$password,
			$user
		);
	}
	/**
	 * Get list of all drivers
	 *
	 * @return array|bool|string
	 */
	function get_list () {
		return $this->db()->qfa(
			"SELECT
				`d`.*,
				`s`.`profile`
			FROM `$this->table` AS `d`
			LEFT JOIN `[prefix]users_social_integration` AS `s`
			ON `d`.`id` = `s`.`id`
			ORDER BY
			 	(CASE WHEN (`d`.`active` = -1) THEN 2 ELSE `d`.`active` END) DESC,
				`d`.`id` DESC"
		);
	}
}