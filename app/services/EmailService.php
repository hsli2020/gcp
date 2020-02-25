<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class EmailService extends Injectable
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new \PHPMailer();

        $this->mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

#       $this->mailer->SMTPDebug = 3;
        $this->mailer->isSMTP();
        $this->mailer->Host = '10.6.200.200';
        $this->mailer->Port = 25;
        $this->mailer->SMTPAuth = false;
        $this->mailer->SMTPSecure = false;
        $this->mailer->From = "OMS@greatcirclesolar.ca";
        $this->mailer->FromName = "Great Circle Solar";
        $this->mailer->isHTML(true);
    }

    public function getErrorInfo()
    {
        return $this->mailer->ErrorInfo();
    }

    public function send($recepient, $subject, $body, $filename=null)
    {
        $mailer = $this->mailer;

       #$mailer->clearAddresses();
        $mailer->clearAllRecipients();
        $mailer->clearAttachments();

        $mailer->addAddress($recepient);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
       #$mailer->AltBody = "Please find the Daily Report in attachment.";

        if ($filename) {
            $mailer->addAttachment($filename, basename($filename));
        }

        return $mailer->send();
    }
}
