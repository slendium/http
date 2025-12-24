# Slendium HTTP

A framework-agnostic PHP library for handling HTTP. Includes:

* PHPDoc type annotations for static analyzers
* Common types related to HTTP, such as `Field`, `MediaType`, `Url` and `IpAddress`
* Parsing and serialization of IPv4 and IPv6 addresses
* Parsing and serialization of structured fields per [RFC 9651](https://www.rfc-editor.org/rfc/rfc9651.html)

## Installation

Requires **PHP >= 8.5**. Simply run `composer install slendium/http` to add it to your project. Most
likely you do not want to use this package directly, but use the following implementation(s) instead:

* [slendium/http-superglobals](https://github.com/slendium/http-superglobals) - implements these interfaces
  directly using the PHP superglobal variables
* [slendium/http-client](https://github.com/slendium/http-client) - a cURL-based HTTP client implementation
  based on these interfaces

## Examples

### Consume a request

```php
$networked = $framework->getRequest(); // returns a Networked<Request>
$request = $networked->payload; // the request message with headers, body and trailers
$address = $networked->address; // contain IP and port, can be passed around independently

// implementation-agnostic access to POST-ed data ($_POST, form data, JSON, CBOR, mocked data)
if ($request->body instanceof Structured) {
	$post = $request->body->root; // ArrayAccess&Countable&Traversable
}

// access query arguments
$queryData = $request->url->getQuery(); // (ArrayAccess&Countable&Traversable)|null

// implementation-agnostic access to cookies (a parsed header, $_COOKIES or even mocked values)
if ($request->headers['cookie'] instanceof Structured) {
	$cookies = $request->headers['cookie']->root; // ArrayAccess&Countable&Traversable
	// or get the raw value instead
	$cookieString = $request->headers['cookie']->value;
}
```

### Uploaded files

```php
if ($request->body instanceof Structured) {
	// files are merged with body data
	$upload = $request->body->root['file'];
	if ($upload instanceof UploadedFile) {
		// process file
	} else if ($upload instanceof UploadFailure) {
		// handle error
	}
}
```

### Structured fields

```php
// check if implementation recognized the header
$field = $request->headers['x-custom-field'];
if ($field instanceof DictionaryField) {
	// do something with an RFC 9651 dictionary
	$dictionary = $field->toDictionary();
} else if ($field instanceof ListField) {
	// do something with an RFC 9651 list
	$list = $field->toList();
} else if ($field instanceof ItemField) {
	// do something with an RFC 9651 item
	$item = $field->toItem();
	if ($item instanceof Item\BinarySequence) { ... } // etc.
}
```

### IP-addresses

```php
// create raw ip address from octets or 16-bit values and convert them to strings later
$ipv4 = IpAddress::V4([ 127, 0, 0, 1 ]);
echo (string)$ipv4, "\n";
$ipv6 = IpAddress::V6([ 0, 0, /* ... */ 0x80 ]);
echo (string)$ipv6, "\n";

// parse a string into ipv4 or ipv6
$parsed = IpAddress::fromString($addrString);
if ($parsed instanceof Ipv4Address) { ... }
else if ($parsed instanceof Ipv6Address { ... }
```

## Motivation

This library serves a similar purpose as [PSR-7](https://www.php-fig.org/psr/psr-7/), but the decision
not to use PSR-7 was based on the following factors:

* The design has aged, many of the interface "getters" can be replaced by property hooks now
* The interfaces force implementation of a builder pattern - e.g. `withMethod()` - which requires
  implementing a lot of methods you don't need when you are simply responding to a single request
  received through PHP/CGI
* Some of the interfaces contain methods that could be folded into built-in PHP types: e.g.
  `hasHeader()`, `getHeader()`, `withHeader()`, `addHeader()`, `getHeaders()` could all be replaced
  using an `ArrayAccess&Traversable` type hint for a `$headers` property
* The PHP "superglobals" are not integrated transparently, they require a separate interface which
  `RequestInterface` consumers need to be aware of
* No abstractions for messages that contain structured objects - a message consumer should be able to
  treat input data generically without knowing the serialization method (form data, JSON, CBOR, ...)
* Missing interfaces for common HTTP concepts such as "fields", "media types", "structured values", etc.
