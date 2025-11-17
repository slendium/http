<?php

namespace Slendium\Http;

use Slendium\Http\Network\SocketAddress;

/**
 * Wrapper for values that have network information associated with them.
 * @since 1.0
 * @template T
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Networked {

	/** @since 1.0 */
	public SocketAddress $address { get; }

	/**
	 * @since 1.0
	 * @var T
	 */
	public mixed $value { get; }

}
