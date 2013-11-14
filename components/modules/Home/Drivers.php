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
		'active'		=> 'int:0..1',
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
		$code	= substr(md5(MICROTIME.uniqid()), 0, 6);
		while ($this->db_prime()->qfs(
			"SELECT `id`
			FROM `$this->table`
			WHERE `code` = '%s'
			LIMIT 1",
			$code
		)) {
			$code	= substr(md5(MICROTIME.uniqid()), 0, 6);
		}
		return $this->create_simple([
			$user,
			$code,
			substr(md5(MICROTIME.uniqid()), 0, 6),
			0,
			0
		]);
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
			SET `active` = 1
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
			SET `active` = 0
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
			SET `reputation` = `reputation` + (%s)
			WHERE `id` = '%s'
			LIMIT 1",
			$value,
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
}