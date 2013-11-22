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
 * @method static \cs\modules\Home\Givers instance($check = false)
 */
class Givers {
	use	CRUD,
		Singleton;

	protected $table		= '[prefix]givers';
	protected $data_model	= [
		'id'			=> 'int',
		'reputation'	=> 'int'
	];

	protected function cdb () {
		return '0';
	}
	/**
	 * Get giver's reputation
	 *
	 * @param int|int[]		$user
	 *
	 * @return array|bool
	 */
	function get ($user) {
		$result	= $this->read_simple($user);
		if (!$result) {
			if (!$this->create_simple([
				$user,
				0
			])) {
				return false;
			}
			$result	= $this->get($user);
		}
		return $result;
	}
	/**
	 * Change giver reputation
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
	 * Get list of all givers
	 *
	 * @return array|bool|string
	 */
	function get_list () {
		return $this->db()->qfa(
			"SELECT
				`g`.*,
				`s`.`profile`
			FROM `$this->table` AS `g`
			LEFT JOIN `[prefix]users_social_integration` AS `s`
			ON `g`.`id` = `s`.`id`
			WHERE `g`.`id` NOT IN (SELECT `id` FROM `[prefix]drivers`)
			GROUP BY `g`.`id`
			ORDER BY `g`.`id` DESC"
		);
	}
}