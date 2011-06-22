<?php

/**
 * This class will create an e-mail object.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		2.3
 */
class FrontendMail extends SpoonEmail
{
	/**
	 * Class constructor.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// set compile directory
		$this->compileDirectory = FRONTEND_CACHE_PATH . '/compiled_templates';
	}


	/**
	 * This method is not supported.
	 *
	 * @return	void
	 * @param	string $email
	 * @param	string[optional] $name
	 */
	public function addBCC($email, $name = null)
	{
		throw new FrontendException('This method is not supported.');
	}


	/**
	 * This method is not supported.
	 *
	 * @return	void
	 * @param	string $email
	 * @param	string[optional] $name
	 */
	public function addCC($email, $name = null)
	{
		throw new FrontendException('This method is not supported.');
	}


	/**
	 * Fetch html content.
	 *
	 * @return	string
	 */
	public function getHtmlContent()
	{
		return $this->content['html'];
	}


	/**
	 * Fetch plain content.
	 *
	 * @return	string
	 */
	public function getPlainContent()
	{
		return $this->content['plain'];
	}


	/**
	 * Fetch reply to e-mail address.
	 *
	 * @return	string
	 */
	public function getReplyToEmail()
	{
		if(isset($this->replyTo[0]['email']))
		{
			return $this->replyTo[0]['email'];
		}
	}


	/**
	 * Fetch reply to name.
	 *
	 * @return	string
	 */
	public function getReplyToName()
	{
		if(isset($this->replyTo[0]['name']))
		{
			return $this->replyTo[0]['name'];
		}
	}
}

?>