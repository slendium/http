<?php

namespace Slendium\Http\Content;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
enum UploadFailure {

	/**
	 * The file was attempted to be uploaded, but it was blocked externally due to being too large.
	 * @since 1.0
	 */
	case FileTooLarge;

	/**
	 * The file was only uploaded partially or not all all, even though there should be a file.
	 * @since 1.0
	 */
	case FileIncomplete;

	/**
	 * The upload failed due to an internal error, such as cancellation due to a plugin or failure
	 * to write the file to a temporary folder.
	 * @since 1.0
	 */
	case InternalError;

}
