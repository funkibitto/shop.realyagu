<?php
namespace Realyagu\Mail;

use Phalcon\Mvc\User\Component;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_Mailer;
use Phalcon\Mvc\View;

/**
 * Realyagu\Mail\Mail
 * Sends e-mails based on pre-defined templates
 */
class Mail extends Component
{
    protected $transport;

    protected $amazonSes;

    /**
     * Send a raw e-mail via AmazonSES
     *
     * @param string $raw
     * @return bool
     */
    private function amazonSESSend($raw)
    {
        if ($this->amazonSes == null) {
            $this->amazonSes = new \AmazonSES(
                [
                    'key'    => $this->config->amazon->AWSAccessKeyId,
                    'secret' => $this->config->amazon->AWSSecretKey
                ]
            );
            @$this->amazonSes->disable_ssl_verification();
        }
        $response = $this->amazonSes->send_raw_email(
            [
                'Data' => base64_encode($raw)
            ],
            [
                'curlopts' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ]
            ]
        );
        if (!$response->isOK()) {
            $this->logger->error('Error sending email from AWS SES: ' . $response->body->asXML());
        }
        return true;
    }

    /**
     * Applies a template to be used in the e-mail
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function getTemplate($name, $params)
    {
        $parameters = array_merge([
            'publicUrl' => $this->config->application->publicUrl
        ], $params);

        return $this->view->getRender('emailTemplates', $name, $parameters, function ($view) {
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });

        return $view->getContent();
    }

    /**
     * @param array $to
     * @param string $subject
     * @param string $name
     * @param array $params
     * @return bool|int
     * @throws Exception
     */
    public function send($to, $subject, $name, $params)
    {
        // Settings
        $mailSettings = $this->config->mail;
        $template = $this->getTemplate($name, $params);
        // Create the message
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom([
                $mailSettings->fromEmail => $mailSettings->fromName
            ])
            ->setBody($template, 'text/html');
        if (isset($mailSettings) && isset($mailSettings->smtp)) {
            if (!$this->transport) {
                $this->transport = \Swift_SmtpTransport::newInstance(
                    $mailSettings->smtp->server,
                    $mailSettings->smtp->port,
                    $mailSettings->smtp->security
                )
                    ->setUsername($mailSettings->smtp->username)
                    ->setPassword($mailSettings->smtp->password)
                    ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false)));
            }
            // Create the Mailer using your created Transport
            $mailer = \Swift_Mailer::newInstance($this->transport);
            return $mailer->send($message);
        } else {
            return $this->amazonSESSend($message->toString());
        }
    }
}
