<?php

namespace Slendium\Http\Base;

use Override;

use Slendium\Http\IpAddress as IIpAddress;

/**
 * A basic IP-address holder.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class IpAddress implements IIpAddress {

	/** @override */
	public int $version {
		get => \strpos($this->address, ':') === false ? 4 : 6;
	}

	/** @since 1.0 */
	public function __construct(

		/**
		 * @since 1.0
		 * @var non-empty-string
		 */
		public readonly string $address,

	) { }

	#[Override]
	public final function __toString(): string {
		return $this->address;
	}

}
