<?php

namespace Slendium\Http\Base;

use Slendium\Http\Networked as INetworked;

/**
 * @since 1.0
 * @template T
 * @implements INetworked<T>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Networked implements INetworked {

	/** @since 1.0 */
	public function __construct(

		/** @override */
		public readonly IpAddress $ip,

		/**
		 * @override
		 * @var int<0,65535>
		 */
		public readonly int $port,

		/**
		 * @override
		 * @var T
		 */
		public readonly mixed $value,

	) { }

}
