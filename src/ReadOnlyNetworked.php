<?php

namespace Slendium\Http;

use Override;

use Slendium\Http\Network\SocketAddress;

/**
 * @since 1.0
 * @template T
 * @implements Networked<T>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final readonly class ReadOnlyNetworked implements Networked {

	/** @since 1.0 */
	public function __construct(

		#[Override]
		public SocketAddress $address,

		/** @var T */
		#[Override]
		public mixed $payload,

	) { }

}
