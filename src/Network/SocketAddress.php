<?php

namespace Slendium\Http\Network;

/**
 * Combines IP address and port number information.
 *
 * Normally the socket address also includes the network protocol (TCP/UDP). This was omitted since
 * this is an HTTP library, where the protocol is fixed depending on the HTTP version used.
 *
 * This is a concrete implementation. The IPv6 representation should always be the canonical one, it
 * would be riskier to allow custom implementations. Additionally, not making it an interface allows
 * adding other network information later without compatibility issues.
 *
 * @since 1.0
 * @author
 * @copyright
 */
final class SocketAddress {

	/** @since 1.0 */
	public function __construct(

		/** @since 1.0 */
		public Ipv4Address|Ipv6Address $ip,

		/**
		 * @since 1.0
		 * @var int<0,65535>
		 */
		public int $port,

	) { }

	/** @return lowercase-string&non-empty-string */
	public function __toString(): string {
		return $this->ip instanceof Ipv6Address
			? "[{$this->ip}]:{$this->port}"
			: "{$this->ip}:{$this->port}";
	}

}
