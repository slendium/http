<?php

namespace Slendium\Http;

/**
 * Interface for all errors returned from the library.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Error {

	/**
	 * @since 1.0
	 * @var non-empty-string
	 */
	public string $message { get; }

}
