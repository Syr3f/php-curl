<?php


/**
 * Curl
 *
 * [Short description here]
 *
 * @copyright (c)2005-2010, WDT Media Corp (http://wdtmedia.net)
 * @author jadb
 *
 * @copyright (c) 2010, Serafim Junior Dos Santos Fagundes Cyb3r Network (http://cyb3r.ca)
 * @author Serafim Junior Dos Santos Fagundes
 */
 
 
class Curl {
	/**
	 * Curl resource handle
	 *
	 * @var resource
	 * @access public
	 */
	public $_rCh = null;
	
	
	/**
	 * Current `CURLOPT_COOKIE`
	 *
	 * @var string
	 * @access private
	 */
	private $_sCookie = "";
	
	
	/**
	 * Current `CURLOPT_COOKIEFILE`
	 *
	 * @var string
	 * @access private
	 */
	private $_sCookieFile = null;
	
	
	/**
	 * Current `CURLOPT_COOKIEJAR`
	 *
	 * @var string
	 * @access private
	 */
	private $_sCookieJar = "";
	
	
	/**
	 * Allow cookies or not
	 *
	 * @var bool
	 * @access private
	 */
	private $_bAllowCookies = false;
	
	
	/**
	 * Current `CURLOPT_ENCODING`
	 *
	 * @var string
	 * @access private
	 */
	private $_sEncoding = "";
	
	
	/**
	 * Supported "Accept-Encoding: " header types
	 *
	 * @var array
	 * @access private
	 */
	private $_asEncodingTypes = array('identity', 'deflate', 'gzip');
	
	
	/**
	 * Current `CURLOPT_FOLLOWLOCATION`
	 *
	 * @var bool
	 * @access private
	 */
	private $_bFollowLocation = false;
	
	
	/**
	 * Current `CURLOPT_HTTPHEADER`
	 *
	 * @var array
	 * @access private
	 */
	private $_asHTTPHeader = null;
	
	
	/**
	 * Current `CURLOPT_HTTP_VERSION`
	 *
	 * @var string
	 * @access private
	 */
	private $_sHTTPVersion = "";
	
	
	/**
	 * Supported HTTP versions
	 *
	 * @var array
	 * @access private
	 */
	private $_hiHTTPVersions = array(
		'' => CURL_HTTP_VERSION_NONE,
		'1.0' => CURL_HTTP_VERSION_1_0,
		'1.1' => CURL_HTTP_VERSION_1_1,
	);
	
	
	/**
	 * Last transfer info
	 *
	 * @var array|bool
	 * @access private
	 */
	private $_asInfo = array();
	
	
	/**
	 * Current `CURLOPT_MAXREDIRS`
	 *
	 * @var int
	 * @access private
	 */
	private $_iMaxRedirects = 0;
	
	
	/**
	 * Current `CURLOPT_POSTFIELDS`
	 *
	 * @var string|array
	 * @access private
	 *	@todo Voir si array possible
	 */
	private $_sPostFields = "";
	
	
	/**
	 * Current `CURLOPT_REFERER`
	 *
	 * @var string
	 * @access private
	 */
	private $_sReferer = "";
	
	
	/**
	 * undocumented variable
	 *
	 * @var string
	 * @access private
	 *	@todo Voir si utilisé
	 */
	private $_sRequest = "";
	
	
	/**
	 * Current request type
	 *
	 * @var string
	 * @access private
	 */
	private $_sRequestType = "";
	
	
	/**
	 * Supported HTTP request types
	 *
	 * @var array
	 * @access private
	 */
	private $_asHTTPRequestTypes = array('CONNECT', 'DELETE', 'GET', 'POST', 'PUT');
	
	
	/**
	 * undocumented variable
	 *
	 * @var string
	 * @access private
	 */
	private $_sResponse = "";
	
	
	/**
	 * undocumented variable
	 *
	 * @var bool
	 * @access private
	 */
	private $_bReturnHeader = true;
	
	
	/**
	 * Current `CURLOPT_SSL_VERIFYHOST`
	 *
	 * @var bool
	 * @access private
	 */
	private $_bSSLVerifyHost = true;
	
	
	/**
	 * Current `CURLOPT_SSL_VERIFYPEER`
	 *
	 * @var bool
	 * @access private
	 */
	private $_bSSLVerifyPeer = true;
	
	
	/**
	 * Current `CURLOPT_CONNECTTIMEOUT`
	 *
	 * @var int
	 * @access private
	 */
	private $_iTimeout = 60;
	
	
	/**
	 * Current URL
	 *
	 * @var string
	 * @access private
	 */
	private $_sUrl = "";
	
	
	/**
	 * Current `CURLOPT_USERAGENT`
	 *
	 * @var string
	 * @access private
	 */
	private $_sUserAgent = "";
	
	
	/**
	 *	Last error
	 *
	 *	@var string
	 *	@access private
	 */
	private $_sLastError;
	
	
	/**
	 * Constructor
	 *
	 * @param undefined $url
	 * @param undefined $options
	 * @access public
	 */
	public function __construct($url = null, $options = array())
	{
		if (!extension_loaded('curl'))
		{
			if (!dl('curl'))
			{
				trigger_error('Curl: PHP was not built with --with-curl', E_USER_ERROR);
				return false;
			}
		}

		$this->_sUrl = $url;

		$this->Connect();

		if (!is_null($this->_sUrl))
		{
			$this->Execute();
		}
	}
	
	
	/**
	 * Close connection and free-up resource
	 *
	 * @return void
	 * @access public
	 */
	public function Close()
	{
		curl_close($this->_rCh);
	}
	
	
	/**
	 * Start the curl resource
	 *
	 * @return void
	 * @access public
	 * @todo add CURLOPT_FRESH_CONNECT
	 */
	public function Connect()
	{
		if (!is_resource($this->_rCh))
		{
			$this->_rCh = is_null($this->_sUrl) ? curl_init() : curl_init($this->_sUrl);
			if (!is_resource($this->_rCh))
			{
				$this->_rCh = null;
				return false;
			}
		}
		return $this->_rCh;
	}
	
	
	/**
	 * Last error for current session
	 *
	 * @return string
	 * @access public
	 */
	public function Error()
	{
		$this->_sLastError = curl_error($this->_rCh);
		if (empty($this->_sLastError))
		{
			if ((int)$this->Info('http_code') >= 400)
			{
				$this->_sLastError = $this->_asInfo['http_code'];
			}
		}
		return $this->_sLastError;
	}
	
	
	/**
	 * Execute curl
	 *
	 * @return void
	 * @access public
	 */
	public function Execute($url = null, $options = array(), $type = 'GET', $ssl = false)
	{
		$this->_asInfo = array();
		$this->_sLastError = null;
		$this->_sUrl = $url;

		foreach ($options as $key => $val)
		{
			$method = 'set' . str_replace(" ", "", ucwords(str_replace("_", " ", $key)));
			$this->{$method}($val);
		}

		if ($this->_sRequestType != $type)
		{
			$this->SetRequestType($type);
		}

		curl_setopt($this->_rCh, CURLOPT_URL, $this->_sUrl);
		// response as string instead of outputting (which is curl's default)
		curl_setopt($this->_rCh, CURLOPT_RETURNTRANSFER, true);

		if (is_null($this->_bReturnHeader))
		{
			$this->SetReturnHeader(true);
		}

		$this->SetSslVerify($ssl);

		$this->_sResponse = curl_exec($this->_rCh);
		$this->Error();
		$this->Reset($type);
	}
	
	
	/**
	 * Execute a GET request
	 *
	 * @return void
	 * @access public
	 */
	public function Get($url = null, $options = array())
	{
		$this->Execute($url, $options);
	}
	
	
	/**
	 * undocumented function
	 *
	 * @param undefined $opt
	 * @return void
	 * @access public
	 */
	public function Info($opt)
	{
		if (empty($this->_asInfo))
		{
			$this->_asInfo = curl_getinfo($this->_rCh);
			if (false === $this->_asInfo)
			{
				trigger_error('', E_USER_ERROR);
				return false;
			}
		}
		if (!array_key_exists($opt, $this->_asInfo))
		{
			trigger_error('', E_USER_ERROR);
			return false;
		}
		return $this->_asInfo[$opt];
	}
	
	
	/**
	 * Execute a POST request
	 *
	 * @return void
	 * @access public
	 */
	public function Post($url = null, $data = null, $options = array())
	{
		if ($this->SetPostFields($data))
		{
			$this->Execute($url, $options, 'POST');
		}
	}
	
	
	/**
	 * undocumented function
	 *
	 * @param undefined $type
	 * @return void
	 * @access public
	 */
	public function Reset($type)
	{
		if ('GET' != $type)
		{
			// reset to default 'GET' type of requests
			curl_setopt($this->_rCh, CURLOPT_HTTPGET, true);
		}
		// force use of new connection instead of cached one
		curl_setopt($this->_rCh, CURLOPT_FRESH_CONNECT, true);
	}
	
	
	/**
	 * Set `CURLOPT_CONNECTTIMEOUT`
	 *
	 * @param int $secs
	 * @return bool
	 * @access public
	 */
	public function SetConnectTimeout($secs)
	{
		if ($this->_iTimeout != $secs)
		{
		 	if (curl_setopt($this->_rCh, CURLOPT_CONNECTTIMEOUT, $secs))
			{
				$this->_iTimeout = $secs;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_COOKIE`, the HTTP request "Cookie: " header
	 *
	 * @param string $cookie
	 * @return bool
	 * @access public
	 */
	public function SetCookie($cookie)
	{
		if ($this->_sCookie != $cookie)
		{
			if (curl_setopt($this->_rCh, CURLOPT_COOKIE, $cookie))
			{
				$this->_sCookie = $cookie;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_COOKIEFILE`, the name of the file containing the cookie data
	 *
	 * @param string $file
	 * @return bool
	 * @access public
	 */
	public function SetCookieFile($file)
	{
		if ($this->_sCookieFile != $file)
		{
			if (curl_setopt($this->_rCh, CURLOPT_COOKIEFILE, $file))
			{
				$this->_sCookieFile = $file;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_COOKIEJAR`, the name of the file to save internal cookies
	 * to when the connection closes
	 *
	 * @param string $file
	 * @return bool
	 * @access public
	 */
	public function SetCookieJar($file)
	{
		if ($this->_sCookieJar != $file)
		{
			if (curl_setopt($this->_rCh, CURLOPT_COOKIEJAR, $file))
			{
				$this->_sCookieJar = $file;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_CUSTOMREQUEST`, just to keep w/ the curl real option names
	 * as function name
	 *
	 * @param string $type
	 * @return bool
	 * @access public
	 */
	public function SetCustomRequest($type = 'GET')
	{
		return $this->SetRequestType($type);
	}
	
	
	/**
	 * Set `CURLOPT_ENCODING`, the HTTP request "Accept-Encoding: " header to enable
	 * decoding of the response
	 *
	 * @param string $encoding if empty, all supported types (Curl::_asEncodingTypes) are set
	 * @return bool
	 * @access public
	 */
	public function SetEncoding($encoding = "gzip")
	{
		if (!empty($encoding) && !in_array($encoding, $this->_asEncodingTypes))
		{
			trigger_error('', E_USER_ERROR);
			return false;
		}

		if ($this->_sEncoding != $encoding)
		{
			if (curl_setopt($this->_rCh, CURLOPT_ENCODING, $encoding))
			{
				$this->_sEncoding = $encoding;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_FOLLOWLOCATION`, to follow any "Location: " header
	 * that the server sends as part of the HTTP header
	 *
	 * Note: this is recursive, PHP will follow as many "Location: "
	 * headers that it is sent, unless CURLOPT_MAXREDIRS is set
	 *
	 * @param bool $bool
	 * @return bool
	 * @access public
	 */
	public function SetFollowLocation($bool)
	{
		if ($this->_bFollowLocation != $bool)
		{
			if (curl_setopt($this->_rCh, CURLOPT_FOLLOWLOCATION, $bool))
			{
				$this->_bFollowLocation = $bool;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_HTTPHEADER`, the HTTP request header
	 *
	 * @param array $headers
	 * @return bool
	 * @access public
	 */
	public function SetHttpHeader($headers)
	{
		if ($this->_asHTTPHeader != $headers)
		{
			if (curl_setopt($this->_rCh, CURLOPT_HTTPHEADER, $headers))
			{
				$this->_asHTTPHeader = $headers;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURL_HTTP_VERSION`
	 *
	 * @param string $version empty, 1.0 or 1.1
	 * @return bool
	 * @access public
	 */
	public function SetHttpVersion($version = '')
	{
		if (!array_key_exists($version, $this->_hiHTTPVersions))
		{
			trigger_error('Curl: invalid HTTP version', E_USER_ERROR);
			return false;
		}
		if ($this->_sHTTPVersion != $version)
		{
			if (curl_setopt($this->_rCh, CURLOPT_HTTP_VERSION, $this->_hiHTTPVersions[$version]))
			{
				$this->_sHTTPVersion = $version;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_MAXREDIRS`, the maximum amount of HTTP redirections to follow
	 *
	 * Note: this will automatically set `CURLOPT_FOLLOWLOCATION` to true
	 *
	 * @param int $max
	 * @return bool
	 * @access public
	 */
	public function SetMaxRedirects($max)
	{
		if ($this->_iMaxRedirects != $max)
		{
			if ($this->SetFollowLocation(true))
			{
				if (curl_setopt($this->_rCh, CURLOPT_MAXREDIRS, $max))
				{
					$this->_iMaxRedirects = $max;
					return true;
				}
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_POSTFIELDS`, the data to post in an HTTP POST request
	 *
	 * To post a file, prepend a filename w/ `@` and use the full path
	 *
	 * @param string|array $data urlencoded string like 'para1=val1&para2=val2&...'
	 *                     or as an array with the field name as key and field
	 *                     data as value (in which case, "Content-Type: " header
	 *                     will be set to multipart/form-data)
	 * @param bool $multipart if FALSE, transforms a $data array into a urlencoded
	 *             string to avoid the "Content-Type: " header being changed
	 * @return bool
	 * @access public
	 */
	public function SetPostFields($data, $multipart = true)
	{
		if ($this->_sPostFields != $data)
		{
			if (false === $multipart && is_array($data))
			{
				$_data = array();
				foreach ($data as $key => $val)
				{
					$_data[] = $key . '=' . urlencode($val);
				}
				$data = implode('&', $_data);
			}
			if (curl_setopt($this->_rCh, CURLOPT_POSTFIELDS, $data))
			{
				$this->_sPostFields = $data;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_REFERER`, the HTTP request "Referer: " header
	 *
	 * @param string $referer
	 * @return bool
	 * @access public
	 */
	public function SetReferrer($referer)
	{
		if ($this->_sReferer != $referer)
		{
			if (curl_setopt($this->_rCh, CURLOPT_REFERER, $referer))
			{
				$this->_sReferer = $referer;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_CUSTOMREQUEST`, the HTTP request type
	 *
	 * @param string $type supported type (Curl::requestTypes)
	 * @return bool
	 * @access public
	 */
	public function SetRequestType($type = 'GET')
	{
		if ($this->_sRequestType != $type)
		{
			if (!in_array($type, $this->_asHTTPRequestTypes))
			{
				$this->_sLastError = sprintf('un-supported HTTP request type (%s)', $type);
				trigger_error('Curl: ' . $this->_sLastError, E_USER_ERROR);
				return false;
			}
			if (curl_setopt($this->_rCh, CURLOPT_CUSTOMREQUEST, $type))
			{
				$this->_sRequestType = $type;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_HEADER`, to include or not the HTTP headers in the response
	 *
	 * @param bool $bool
	 * @return bool
	 * @access public
	 */
	public function SetReturnHeader($bool)
	{
		if ($this->_bReturnHeader != $bool)
		{
			if (curl_setopt($this->_rCh, CURLOPT_HEADER, $bool))
			{
				$this->_bReturnHeader = $bool;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_USERAGENT`, the HTTP request "User-Agent: " header
	 *
	 * @param string $agent
	 * @return bool
	 * @access public
	 */
	public function SetUserAgent($agent)
	{
		if ($this->_sUserAgent != $agent)
		{
		 	if (curl_setopt($this->_rCh, CURLOPT_USERAGENT, $agent))
			{
				$this->_sUserAgent = $agent;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * undocumented function
	 *
	 * @param bool $bool
	 * @return bool
	 * @access public
	 */
	public function SetSslVerify($bool)
	{
		if ($this->SetSslVerifyHost($bool))
		{
			return $this->SetSslVerifyPeer($bool);
		}
		return false;
	}
	
	
	/**
	 * Set `CURLOPT_SSL_VERIFYHOST`
	 *
	 * @param bool $bool
	 * @return bool
	 * @access public
	 */
	public function SetSslVerifyHost($bool)
	{
		if ($this->_bSSLVerifyHost != $bool)
		{
			if (curl_setopt($this->_rCh, CURLOPT_SSL_VERIFYHOST, $bool))
			{
				$this->_bSSLVerifyHost = $bool;
				return true;
			}
			return false;
		}
		return true;
	}
	
	
	/**
	 * Set `CURLOPT_SSL_VERIFYPEER`
	 *
	 * @param bool $bool
	 * @return bool
	 * @access public
	 */
	public function SetSslVerifyPeer($bool)
	{
		if ($this->_bSSLVerifyPeer != $bool)
		{
			if (curl_setopt($this->_rCh, CURLOPT_SSL_VERIFYPEER, $bool))
			{
				$this->_bSSLVerifyPeer = $bool;
				return true;
			}
			return false;
		}
		return true;
	}
}
?>