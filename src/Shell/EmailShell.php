<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Mailer\Email;

class EmailShell extends Shell
{
    const DEFAULT_DOMAIN = 'localhost';
    const DEFAULT_SUBJECT = 'Test message';
    const DEFAULT_BODY = 'Hello.  This is a test message.';

    /**
     * Get default domain
     *
     * CakePHP 3 requires a domain, for generating Message-ID headers,
     * when sending emails from the CLI.  This method gets the default
     * by reading the configuration from the environment or assuming
     * localhost.
     *
     * @link http://book.cakephp.org/3.0/en/core-libraries/email.html#sending-emails-from-cli
     * @return string
     */
    protected function _getDefaultDomain()
    {
        $result = getenv('EMAIL_DOMAIN') ?: self::DEFAULT_DOMAIN;

        return $result;
    }

    /**
     * Configure option parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Send an email message');
        $parser->addArgument('to', [
            'help' => 'Destination email address (required)',
            'required' => true,
        ]);
        $parser->addOption('domain', [
            'help' => 'Domain to use for Message-Id (default: ' . $this->_getDefaultDomain() . ')',
        ]);
        $parser->addOption('subject', [
            'help' => 'Subject to use (default: ' . self::DEFAULT_SUBJECT . ')',
        ]);
        $parser->addOption('message', [
            'help' => 'Message body to use (default: ' . self::DEFAULT_BODY . ')',
        ]);

        return $parser;
    }

    /**
     * Main shell method
     *
     * @return void
     */
    public function main()
    {
        // Get the settings
        $to = $this->args[0];
        $domain = !empty($this->params['domain']) ? $this->params['domain'] : $this->_getDefaultDomain();
        $subject = !empty($this->params['subject']) ? $this->params['subject'] : self::DEFAULT_SUBJECT;
        $message = !empty($this->params['message']) ? $this->params['message'] : self::DEFAULT_BODY;

        // Send the message
        $this->out("Sending message to $to ... ", 0);
        try {
            $email = new Email();
            $email->domain($domain);
            $email->to($to);
            $email->subject($subject);
            $result = $email->send($message);
        } catch (\Exception $e) {
            $this->out("FAILED");
            $this->abort($e->getMessage());
        }

        // Print out successful result
        $this->out("OK");
        $this->hr();
        $this->out("Headers:", 1, Shell::VERBOSE);
        $this->out($result['headers'], 1, Shell::VERBOSE);
    }
}
