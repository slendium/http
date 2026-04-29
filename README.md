# Slendium HTTP

A framework-agnostic PHP library for handling HTTP. Includes:

* PHPDoc type annotations for static analyzers
* Common types related to HTTP, such as `Field`, `MediaType`, `Uri` and `IpAddress`
* Parsing and serialization of IPv4 and IPv6 addresses
* Parsing and serialization of structured fields per [RFC 9651](https://www.rfc-editor.org/rfc/rfc9651.html)

## Installation

Requires **PHP >= 8.5**. Simply run `composer require slendium/http` to add it to your project. Most
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
$queryData = $request->uri->getQuery(); // (ArrayAccess&Countable&Traversable)|null
```

Cookies can also be accessed in an implementation-agnostic way. It doesn't matter if the cookies
come from a parsed header, the `$_COOKIES` superglobal or are mocked values.

```php
$cookieHeader = Headers::getFirst($request, 'cookie');
if ($cookieHeader !== null && $cookieHeader instanceof Structured) {
	$cookies = $cookieHeader->root; // ArrayAccess&Countable&Traversable
	$cookieValue = $cookies['cookieName'];
	// or get the full header value instead
	$cookieString = $cookieHeader->value;
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

### Obtain structured values

The library also supports parsing of HTTP headers into [structured values](https://www.rfc-editor.org/rfc/rfc9651.html).
Knowing which HTTP header is supposed to be which structured value type is the responsibility of the
implementor (eg. returning a `Field` that implements `DictionaryField`).

```php
// check if implementation recognized the header
$field = Headers::getFirst($request, 'x-custom-field');
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

### Parse or serialize structured values

```php
$value = Fields::getFirst($headers, 'x-custom-header')->value;

// parse strictly according to the spec
$strict = new Rfc9651Parser();
$asList = $strict->parseList($value);
$asDictionary = $strict->parseDictionary($value);
$asItem = $strict->parseItem($value);

// parse with higher error tolerance
$lenient = new LenientParser();
$asList = $lenient->parseList($value);
// ...etc

// serialize strictly according to the spec
$serializer = new Rfc9651Serializer();
echo $serializer->serializeItem(Item::DisplayString('Hello')); // prints %"Hello"
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

* The design has aged, many of the interface "getters" can be replaced with property hooks now
* Many of the interfaces have dual responsibilities, where they simultaneously are an abstraction of
  the underlying information and are builder types - eg. `RequestInterface` contains both a `getUri()`
  and a `withUri()` method, the latter of which is not needed when you are simply responding to an
  incoming request received through PHP/CGI
* Some of the interfaces contain methods that could be folded into built-in PHP types: eg. `hasHeader()`,
  `getHeader()`, `withHeader()`, `addHeader()`, `getHeaders()` could all be replaced with a
  `public iterable $headers` property, a set of utility functions and a builder class
* The PHP "superglobals" are not integrated transparently, they require a separate interface which
  `RequestInterface` consumers need to be aware of
* No abstractions for messages that contain structured objects - a message consumer should be able to
  treat input data generically without knowing the serialization method (form data, JSON, CBOR, ...)
* Missing interfaces for common HTTP concepts such as "fields", "media types", "structured values", etc.
