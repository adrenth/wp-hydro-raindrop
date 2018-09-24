<?php

declare( strict_types=1 );

/**
 * Requirement checker.
 *
 * @since      2.0.0
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
final class Hydro_Raindrop_RequirementChecker {

	const REQUIREMENT_SSL         = 'ssl';
	const REQUIREMENT_PHP_VERSION = 'php_version';
	const REQUIREMENT_CURL        = 'curl';

	/**
	 * Run all checks.
	 *
	 * @return bool
	 */
	public function passes() : bool {

		foreach ( $this->get_requirements() as $requirement ) {
			if ( ! $this->check( $requirement['test'] ) ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Returns TRUE if given requirement passes the requirement check.
	 *
	 * @param string $requirement Use class constants.
	 *
	 * @return bool
	 */
	public function passes_requirement( string $requirement ) : bool {

		$requirements = $this->get_requirements();

		if ( array_key_exists( $requirement, $requirements ) ) {
			return $this->check( $requirements[ $requirement ]['test'] );
		}

	}

	/**
	 * Get the requirements for Hydro Raindrop plugin.
	 *
	 * @return array
	 */
	public function get_requirements() : array {

		return [
			self::REQUIREMENT_SSL         => [
				'label'       => 'SSL',
				'requirement' => 'SSL should be enabled.',
				'test'        => function () {
					return is_ssl();
				},
			],
			self::REQUIREMENT_PHP_VERSION => [
				'label'       => 'PHP version',
				'requirement' => 'PHP version 7.0 or higher.',
				'test'        => function () {
					return version_compare( PHP_VERSION, '7.0.0' ) >= 0;
				},
			],
			self::REQUIREMENT_CURL        => [
				'label'       => 'cURL extension',
				'requirement' => 'PHP cURL extension (ext-curl) installed and enabled.',
				'test'        => function () {
					return function_exists( 'curl_version' );
				},
			],
		];

	}

	/**
	 * Check requirement.
	 *
	 * @param callable $test The requirement test.
	 *
	 * @return bool
	 */
	public function check( callable $test ) : bool {

		return $test();

	}

}
