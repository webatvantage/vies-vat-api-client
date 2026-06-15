<?php

declare (strict_types=1);

/**
 * Vies
 *
 * Component using the European Commission (EC) VAT Information Exchange System (VIES) to verify and validate VAT
 * registration numbers in the EU, using PHP and Composer.
 *
 * @license  MIT
 *
 */

namespace Webatvantage\Vies\Exceptions;

/**
 * ViesServiceException
 *
 * This class provides an exception layer for usage of the VIES web service
 * provided by the European commission to validate VAT numbers of companies
 * registered within the European Union.
 *
 * When VIES rejects a request with a structured error response (an HTTP 200
 * whose body carries actionSucceed=false), the offending error codes are
 * exposed through getErrorCodes(). The constants below enumerate the codes
 * documented by the EC VIES REST API.
 *
 * @see \Exception
 */
class ViesServiceException extends \Exception
{
	/** The supplied country code or VAT number was rejected by VIES. */
	public const INVALID_INPUT = 'INVALID_INPUT';

	/** The requester's country/VAT identification was rejected by VIES. */
	public const INVALID_REQUESTER_INFO = 'INVALID_REQUESTER_INFO';

	/** The VIES web service itself is temporarily unavailable. */
	public const SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

	/** The member state's back-end service is temporarily unavailable. */
	public const MS_UNAVAILABLE = 'MS_UNAVAILABLE';

	/** The member state's back-end service did not answer in time. */
	public const TIMEOUT = 'TIMEOUT';

	/** The VAT number was blocked from validation. */
	public const VAT_BLOCKED = 'VAT_BLOCKED';

	/** The requesting IP address was blocked. */
	public const IP_BLOCKED = 'IP_BLOCKED';

	/** Too many concurrent requests towards VIES globally. */
	public const GLOBAL_MAX_CONCURRENT_REQ = 'GLOBAL_MAX_CONCURRENT_REQ';

	/** Too many concurrent requests towards VIES globally (time window). */
	public const GLOBAL_MAX_CONCURRENT_REQ_TIME = 'GLOBAL_MAX_CONCURRENT_REQ_TIME';

	/** Too many concurrent requests towards the targeted member state. */
	public const MS_MAX_CONCURRENT_REQ = 'MS_MAX_CONCURRENT_REQ';

	/** Too many concurrent requests towards the targeted member state (time window). */
	public const MS_MAX_CONCURRENT_REQ_TIME = 'MS_MAX_CONCURRENT_REQ_TIME';

	/** @var string[] */
	private array $errorCodes = [];

	/**
	 * Build an exception for a structured VIES error response, retaining the raw
	 * error codes so callers can react to them (e.g. retry on rate limiting).
	 *
	 * @param string[] $errorCodes
	 */
	public static function fromErrorResponse(string $message, array $errorCodes): self
	{
		$exception = new self($message);
		$exception->errorCodes = $errorCodes;

		return $exception;
	}

	/**
	 * The VIES error codes that triggered this exception (see the class
	 * constants). Empty when the failure was a transport-level error.
	 *
	 * @return string[]
	 */
	public function getErrorCodes(): array
	{
		return $this->errorCodes;
	}
}
