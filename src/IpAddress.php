<?php

namespace Slendium\Http;

use Stringable;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface IpAddress extends Stringable {

	/**
	 * @since 1.0
	 * @var 4|6
	 */
	public int $version { get; }

}
