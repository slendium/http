<?php

namespace Slendium\Http\Content;

use Slendium\Http\Content\MediaType;

/**
 * Metadata about a file uploaded as part of an HTTP message.
 *
 * Uploaded files should be returned as part of a `Structured` request body, where the name of the
 * file input is associated either with an `UploadedFile` or an `UploadFailure`.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface UploadedFile {

	/**
	 * The filename of the file, if provided by the client.
	 * @since 1.0
	 */
	public ?string $filename { get; }

	/**
	 * The media type of the file, if provided by the client.
	 * @since 1.0
	 */
	public ?MediaType $type { get; }

	/**
	 * The path to the file, if stored locally.
	 * @since 1.0
	 */
	public ?string $path { get; }

	/**
	 * Returns the binary contents of the file.
	 * @since 1.0
	 */
	public function getContents(): string;

}
