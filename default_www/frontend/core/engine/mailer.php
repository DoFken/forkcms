<?php

/**
 * This class will send mails.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		2.0
 */
class FrontendMailer
{
	/**
	 * Adds an email to the queue.
	 *
	 * @return	void
	 * @param	FrontendMail $email		The email object.
	 * @param	int[optional] $sendOn	The date we need to send this email. Null means right now.
	 */
	public static function addEmail(FrontendMail $email, $sendOn = null)
	{
		// set defaults
		$to = FrontendModel::getModuleSetting('core', 'mailer_to');
		$from = FrontendModel::getModuleSetting('core', 'mailer_from');
		$replyTo = FrontendModel::getModuleSetting('core', 'mailer_reply_to');
		$utm = array(
			'utm_source' => 'mail',
			'utm_medium' => 'email',
			'utm_campaign' => SpoonFilter::urlise($email->getSubject())
		);

		// from
		$item['from_email'] = (!$email->getFromEmail()) ? $from['email'] : $email->getFromEmail();
		$item['from_name'] = (!$email->getFromName()) ? $from['name'] : $email->getFromName();

		// reply to
		$item['reply_to_email'] = (!$email->getReplyToEmail()) ? $replyTo['email'] : $email->getReplyToEmail();
		$item['reply_to_name'] = (!$email->getReplyToName()) ? $replyTo['name'] : $email->getReplyToName();

		// validate the required e-mail addresses
		if(!empty($item['to_email']) && !SpoonFilter::isEmail($item['to_email'])) throw new FrontendException('Invalid recipeient e-mail address.');
		if(!empty($item['from_email']) && !SpoonFilter::isEmail($item['from_email'])) throw new FrontendException('Invalid sender e-mail address.');
		if(!empty($item['reply_to_email']) && !SpoonFilter::isEmail($item['reply_to_email'])) throw new FrontendException('Invalid reply-to e-mail address.');

		// subject, plaintext, html & created on
		$item['subject'] = SpoonFilter::htmlentitiesDecode($email->getSubject());
		$item['html'] =  $email->getHtmlContent();
		if($email->getPlainContent() !== null) $item['plain_text'] = $email->getPlainContent();
		$item['created_on'] = FrontendModel::getUTCDate();

		// prepend site url
		$item['html'] = self::prependSiteUrl($item['html'], SITE_URL);

		// append utm code
		$item['html'] = self::appendUtm($item['html'], $utm);

		// add attachments
		if(count($email->getAttachments()))
		{
			$item['attachments'] = serialize($email->getAttachments());
		}

		/*
		 * Add the date to send this object. If no date is set, this e-mail will be sent
		 * immediately instead of being added to the queue.
		 */
		if($sendOn !== null)
		{
			$item['send_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s', (int) $sendOn);
		}

		/*
		 * If no recipients were added to the mail object, we're going to send this
		 * email just to the default receiver.
		 */
		if(empty($email->getRecipients()))
		{
			// to
			$item['to_email'] = $to['email'];
			$item['to_name'] = $to['name'];

			// insert the email into the database
			$id = FrontendModel::getDB(true)->insert('emails', $item);

			// send e-mail right now
			if(!$sendOn) self::send($id);
		}

		/*
		 * One or more recipients were added, so we're going to send this email to all
		 * of them.
		 */
		else
		{
			// loop recipients
			foreach($email->getRecipients() as $recipient)
			{
				// to
				$item['to_email'] = $recipient['email'];
				$item['to_name'] = $recipient['name'];

				// insert the email into the database
				$id = FrontendModel::getDB(true)->insert('emails', $item);

				// send e-mail right now
				if(!$sendOn) self::send($id);
			}
		}
	}


	/**
	 * Append an utm string to all internal links.
	 *
	 * @return	string
	 * @param	string $string		The string to search for links.
	 * @param	array $utm			The utm codes as an array.
	 */
	public static function appendUtm($string, array $utm)
	{
		// init var
		$matches = array();

		// match links
		if(preg_match_all('/href="(http:\/\/(.*))"/iU', $string, $matches))
		{
			// init vars
			$searchLinks = array();
			$replaceLinks = array();

			// loop old links
			foreach($matches[1] as $i => $link)
			{
				$searchLinks[] = $matches[0][$i];
				$replaceLinks[] = 'href="' . FrontendModel::addURLParameters($link, $utm) . '"';
			}

			// replace
			$string = str_replace($searchLinks, $replaceLinks, $string);
		}

		return $string;
	}


	/**
	 * Get all queued mail ids
	 *
	 * @return	array
	 */
	public static function getQueuedMailIds()
	{
		return (array) FrontendModel::getDB()->getColumn('SELECT e.id
															FROM emails AS e
															WHERE e.send_on < ?',
															array(FrontendModel::getUTCDate()));
	}


	/**
	 * Returns the content from a given template
	 *
	 * @return	string
	 * @param	string $template				The template to use.
	 * @param	array[optional] $variables		The variabled to assign.
	 */
	private static function getTemplateContent($template, $variables = null)
	{
		// new template instance
		$tpl = new FrontendTemplate(false);

		// set some options
		$tpl->setForceCompile(true);

		// variables were set
		if(!empty($variables)) $tpl->assign($variables);

		// grab the content
		$content = $tpl->getContent($template);

		// replace internal links/images
		$search = array('href="/', 'src="/');
		$replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');
		$content = str_replace($search, $replace, $content);

		// require CSSToInlineStyles
		require_once 'external/css_to_inline_styles.php';

		// create instance
		$cssToInlineStyles = new CSSToInlineStyles();

		// set some properties
		$cssToInlineStyles->setHTML($content);
		$cssToInlineStyles->setUseInlineStylesBlock(true);
		$cssToInlineStyles->setEncoding(SPOON_CHARSET);

		// return the content
		return (string) $cssToInlineStyles->convert();
	}


	/**
	 * Prepend the site url to internal links.
	 *
	 * @return	string
	 * @param	string $string	The string to prepend the url to all the links.
	 * @param	string $url		The url to prepend to internal links.
	 */
	public static function prependSiteUrl($string, $url)
	{
		// init var
		$matches = array();

		// get internal links
		if(preg_match_all('|href="/(.*)"|i', $string, $matches))
		{
			// init vars
			$search = array();
			$replace = array();

			// loop the links
			foreach($matches[0] as $key => $link)
			{
				$search[] = $link;
				$replace[] = 'href="' . $url . '/' . $matches[1][$key] . '"';
			}

			// replace
			$string = str_replace($search, $replace, $string);
		}

		return $string;
	}


	/**
	 * Send an email
	 *
	 * @return	void
	 * @param	int $id		The id of the mail to send.
	 */
	public static function send($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB(true);

		// get record
		$emailRecord = (array) $db->getRecord('SELECT *
												FROM emails AS e
												WHERE e.id = ?',
												array($id));

		// mailer type
		$mailerType = FrontendModel::getModuleSetting('core', 'mailer_type', 'mail');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(FRONTEND_CACHE_PATH . '/compiled_templates');

		// send via SMTP
		if($mailerType == 'smtp')
		{
			// get settings
			$SMTPServer = FrontendModel::getModuleSetting('core', 'smtp_server');
			$SMTPPort = FrontendModel::getModuleSetting('core', 'smtp_port', 25);
			$SMTPUsername = FrontendModel::getModuleSetting('core', 'smtp_username');
			$SMTPPassword = FrontendModel::getModuleSetting('core', 'smtp_password');

			// set server and connect with SMTP
			$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);

			// set authentication if needed
			if($SMTPUsername !== null && $SMTPPassword !== null) $email->setSMTPAuth($SMTPUsername, $SMTPPassword);
		}

		// set some properties
		$email->setFrom($emailRecord['from_email'], $emailRecord['from_name']);
		$email->addRecipient($emailRecord['to_email'], $emailRecord['to_name']);
		$email->setReplyTo($emailRecord['reply_to_email']);
		$email->setSubject($emailRecord['subject']);
		$email->setHTMLContent($emailRecord['html']);
		$email->setCharset(SPOON_CHARSET);
		if($emailRecord['plain_text'] != '') $email->setPlainContent($emailRecord['plain_text']);

		// attachments added
		if(isset($emailRecord['attachments']) && $emailRecord['attachments'] !== null)
		{
			// unserialize
			$attachments = (array) unserialize($emailRecord['attachments']);

			// add attachments to email
			foreach($attachments as $attachment) $email->addAttachment($attachment);
		}

		// send the email
		if($email->send())
		{
			// remove the email
			$db->delete('emails', 'id = ?', array($id));
		}
	}
}

?>