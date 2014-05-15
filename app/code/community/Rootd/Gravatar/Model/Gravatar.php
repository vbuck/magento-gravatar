<?php

/**
 * Gravatar service model.
 *
 * @package  	Rootd_Gravatar
 * @author 		Rick Buczynski <me@rickbuczynski.com>
 * @copyright 	2014 Rick Buczynski. All Rights Reserved.
 *
 * License
 * 
 * Permission is hereby granted, free of charge, to any person 
 * obtaining a copy of this software and associated documentation 
 * files (the "Software"), to deal in the Software without 
 * restriction, including without limitation the rights to use, 
 * copy, modify, merge, publish, distribute, sublicense, and/or 
 * sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following 
 * conditions:
 *
 * The above copyright notice and this permission notice shall be 
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE.
 */

class Rootd_Gravatar_Model_Gravatar extends Mage_Core_Model_Abstract 
{

	const HTTP_URL 	= 'http://www.gravatar.com/avatar/';
	const HTTPS_URL = 'https://secure.gravatar.com/avatar/';

	protected $_size 			= 80;
	protected $_defaultImage 	= false;
	protected $_maxRating 		= 'g';
	protected $_useSecureUrl 	= false;
	protected $_paramCache 		= null;

	/**
	 * Get the currently set avatar size.
	 * 
	 * @return integer
	 */
	public function getAvatarSize() 
	{
		return $this->_size;
	}

	/**
	 * Set the avatar size to use.
	 * 
	 * @param 	integer $size
	 * @return 	Rootd_Gravatar_Model_Gravatar
	 */
	public function setAvatarSize($size) 
	{
		$this->_paramCache = null;

		if(!is_int($size) && !ctype_digit($size)) 
		{
			throw new InvalidArgumentException('Avatar size specified must be an integer');
		}

		$this->_size = (int) $size;

		if($this->_size > 512 || $this->_size < 0) 
		{
			throw new InvalidArgumentException('Avatar size must be within 0 pixels and 512 pixels');
		}

		return $this;
	}

	/**
	 * Get the current default image setting.
	 * 
	 * @return mixed
	 */
	public function getDefaultImage() 
	{
		return $this->_defaultImage;
	}

	/**
	 * Set the default image to use for avatars.
	 * 
	 * @param 	mixed $image
	 * @return 	Rootd_Gravatar_Model_Gravatar
	 */
	public function setDefaultImage($image) 
	{
		if($image === false) {
			$this->_defaultImage = false;

			return $this;
		}

		$this->_paramCache = null;

		// Check $image against recognized gravatar "defaults", and if it doesn't match any of those we need to see if it is a valid URL.
		$image 		= strtolower($image);
		$defaults 	= array(
			'404' 		=> 1, 
			'mm' 		=> 1, 
			'identicon' => 1, 
			'monsterid' => 1, 
			'wavatar' 	=> 1, 
			'retro' 	=> 1
		);

		if(!isset($defaults[$image])) 
		{
			if(!filter_var($image, FILTER_VALIDATE_URL)) 
			{
				throw new InvalidArgumentException('The default image specified is not a recognized gravatar "default" and is not a valid URL');
			}
			else 
			{
				$this->_defaultImage = rawurlencode($image);
			}
		}
		else 
		{
			$this->_defaultImage = $image;
		}

		return $this;
	}

	/**
	 * Get the current maximum allowed rating for avatars.
	 * 
	 * @return string
	 */
	public function getMaxRating() 
	{
		return $this->_maxRating;
	}

	/**
	 * Set the maximum allowed rating for avatars.
	 * 
	 * @param 	string $rating
	 * @return 	Rootd_Gravatar_Model_Gravatar
	 */
	public function setMaxRating($rating) 
	{
		$this->_paramCache = null;

		$rating 	= strtolower($rating);
		$ratings 	= array(
			'g' 	=> 1, 
			'pg' 	=> 1, 
			'r' 	=> 1, 
			'x' 	=> 1
		);

		if(!isset($ratings[$rating])) 
		{
			throw new InvalidArgumentException(sprintf('Invalid rating "%s" specified, only "g", "pg", "r", or "x" are allowed to be used.', $rating));
		}

		$this->_maxRating = $rating;

		return $this;
	}

	/**
	 * Check if we are using the secure protocol for the image URLs.
	 * 
	 * @return boolean
	 */
	public function usingSecureImages() 
	{
		return $this->_useSecureUrl;
	}

	/**
	 * Enable the use of the secure protocol for image URLs.
	 * 
	 * @return Rootd_Gravatar_Model_Gravatar
	 */
	public function enableSecureImages() 
	{
		$this->_useSecureUrl = true;

		return $this;
	}

	/**
	 * Disable the use of the secure protocol for image URLs.
	 * 
	 * @return Rootd_Gravatar_Model_Gravatar
	 */
	public function disableSecureImages() 
	{
		$this->_useSecureUrl = false;

		return $this;
	}

	/**
	 * Build the avatar URL based on the provided email address.
	 * 
	 * @param 	string $email
	 * @param 	string $hashEmail
	 * @return 	string
	 */
	public function buildGravatarURL($email = '', $hashEmail = true) 
	{
		if(!$email)
		{
			return '';
		}

		if($this->usingSecureImages()) 
		{
			$url = self::HTTPS_URL;
		}
		else 
		{
			$url = self::HTTP_URL;
		}

		if($hashEmail == true && !empty($email)) 
		{
			$url .= $this->getEmailHash($email);
		}
		else if(!empty($email)) 
		{
			$url .= $email;
		}
		else 
		{
			$url .= str_repeat('0', 32);
		}

		// Check to see if the _paramCache property has been populated yet
		if(is_null($this->_paramCache)) 
		{
			$params 	= array();
			$params[]	= 's=' . $this->getAvatarSize();
			$params[] 	= 'r=' . $this->getMaxRating();

			if($this->getDefaultImage()) 
			{
				$params[] = 'd=' . $this->getDefaultImage();
			}

			$this->_paramCache = (!empty($params)) ? '?' . implode('&amp;', $params) : '';
		}

		// Handle "null" gravatar requests.
		$tail = '';
		if(empty($email)) 
		{
			$tail = !empty($this->_paramCache) ? '&amp;f=y' : '?f=y';
		}

		return $url . $this->_paramCache . $tail;
	}

	/**
	 * Get the email hash to use (after cleaning the string).
	 * 
	 * @param 	string $email
	 * @return 	string
	 */
	public function getEmailHash($email) 
	{
		return hash('md5', strtolower(trim($email)));
	}

	/**
	 * @see Rootd_Gravatar_Model_Gravatar::buildGravatarURL()
	 */
	public function getUrl($email = '', $hashEmail = true) 
	{
		if(!$email)
		{
			return '';
		}

		return $this->buildGravatarURL($email, $hashEmail);
	}

}