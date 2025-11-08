<?php

namespace Slendium\Http;

/**
 * Wrapper for values that have IP network information associated with them.
 * @since 1.0
 * @template T
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Networked {

	/**
	 * The IP address from/to which the information was received/sent.
	 * @since 1.0
	 */
	public IpAddress $ip { get; }

	/**
	 * @since 1.0
	 * @var int<0,65535>
	 */
	public int $port { get; }

	/**
	 * @since 1.0
	 * @var T
	 */
	public mixed $value { get; }

}
