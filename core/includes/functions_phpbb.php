<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* This file contains functions, to be backwards and forwards compatible with phpBB-versions
* Minimum requirement: phpBB 3.0.9
*/

if (!function_exists('send_status_line'))
{
	/**
	* Outputs correct status line header.
	*
	* Depending on php sapi one of the two following forms is used:
	*
	* Status: 404 Not Found
	*
	* HTTP/1.x 404 Not Found
	*
	* HTTP version is taken from HTTP_VERSION environment variable,
	* and defaults to 1.0.
	*
	* Sample usage:
	*
	* send_status_line(404, 'Not Found');
	*
	* @param int $code HTTP status code
	* @param string $message Message for the status code
	* @return void
	*/
	function send_status_line($code, $message)
	{
		global $request;

		if (substr(strtolower(@php_sapi_name()), 0, 3) === 'cgi')
		{
			// in theory, we shouldn't need that due to php doing it. Reality offers a differing opinion, though
			header("Status: $code $message", true, $code);
		}
		else
		{
			$version = $request->server('SERVER_PROTOCOL');
			if (empty($version))
			{
				$version = $request->server('HTTP_VERSION');
			}
			if (empty($version))
			{
				$version = 'HTTP/1.0';
			}
			header("$version $code $message", true, $code);
		}
	}
}

if (!function_exists('phpbb_parse_http_date'))
{
	/**
	* Converts an HTTP 'full date' to UNIX timestamp
	* See:	http://tools.ietf.org/html/rfc2616#section-3.3.1
	*
	* Formats allowed by rfc 2616 are:
	*
	*      Sun, 06 Nov 1994 08:49:37 GMT  ; RFC 822, updated by RFC 1123
	*      Sunday, 06-Nov-94 08:49:37 GMT ; RFC 850, obsoleted by RFC 1036
	*      Sun Nov  6 08:49:37 1994       ; ANSI C's asctime() format
	*
	* The asctime format has no timezone information. At least some systems
	* take timezone as an argument to asctime, but the timezone is lost by
	* the time formatted string is produced. Because it is impossible to know
	* what timezone a time in asctime format is in, we do not support the
	* asctime format and return false if a time in asctime format is passed in.
	*
	* @param string	$date		Parameter array, see $param_defaults array.
	*
	* @return int|bool			False on failure,
	*							GMT Unix timestamp otherwise.
	*/
	function phpbb_parse_http_date($date)
	{
		if (substr($date, -3) == 'GMT')
		{
			return strtotime($date);
		}

		return false;
	}
}

if (!function_exists('phpbb_parse_if_modified_since'))
{
	/**
	* Parses If-Modified-Since HTTP header, returning the UNIX timestamp.
	*
	* The value may be given as $date parameter. If no parameter is given,
	* $_SERVER['HTTP_IF_MODIFIED_SINCE'] will be examined.
	*
	* If a date is supplied via the $date parameter or $_SERVER, and the
	* date is valid, the UNIX timestamp for the date is returned.
	*
	* If there is no date supplied or the date is invalid or does not parse,
	* false is returned.
	*
	* phpbb_parse_http_date is used for date parsing, which does not accept
	* ANSI C asctime-formatted dates.
	*
	* @param string	$date		HTTP 'full date' to parse, or false to use $_SERVER['HTTP_IF_MODIFIED_SINCE'].
	*
	* @return int|bool			False on failure,
	*							GMT Unix timestamp otherwise.
	*/
	function phpbb_parse_if_modified_since($date = false)
	{
		global $request;

		if ($date === false && $request->server('HTTP_IF_MODIFIED_SINCE'))
		{
			$date = trim($request->server('HTTP_IF_MODIFIED_SINCE'));
		}

		if (empty($date))
		{
			return false;
		}

		$if_modified_time = phpbb_parse_http_date($date);
		return $if_modified_time;
	}
}
