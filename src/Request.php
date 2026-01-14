<?php

namespace Slendium\Http;

/**
 * An HTTP request message.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Request extends Message {

	/**
	 * The effective URI, constructed from the scheme, hostname and the `:path` header.
	 *
	 * For received requests, the effective scheme and hostname portions should be derived from information
	 * provided by the gateway (web server) for security reasons.
	 *
	 * @since 1.0
	 */
	public Uri $uri { get; }

}
