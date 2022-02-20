<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Transport_EsmtpTransport;

use App\Mail\Mailer;
use Exception;

class SendMail
{
    protected $from;

    protected $to;

    protected $cc;

    protected $bcc;

    protected $subject;

    protected $template;

    protected $attachment;

    protected $attachmentName;

    protected $isAttachFromStorage;

    protected $storageDisk;

    protected $mailFromAddress;

    protected $mailFromName;

    protected $logo;

    protected $isCosRequestConfig;

    protected $configBackup;

    protected $data;

    /**
     * $data
     * to (array of email) Not Empty
     * cc (array of email) Can be Empty
     * bcc (array of email) Can be Empty
     * from (string of email) Can be Empty
     * data (array) Can be Empty
     * subject (string) Required
     * template (string) Not Required
     */

    public function __construct($data)
    {
        $this->from = $data->from ?? '';

        $this->to = $data->to;

        $this->cc = $data->cc ?? [];

        $this->bcc = $data->bcc ?? [];

        $this->data = $data->data ?? (object)[];

        $this->template = $data->template;

        $this->subject = $data->subject ?? env('MAIL_FROM_NAME', '');

        $this->attachment = $data->attachment ?? '';

        $this->attachmentName = $data->attachmentName ?? '';

        $this->isAttachFromStorage = $data->isAttachFromStorage ?? false;

        $this->storageDisk = $data->storageDisk ?? 'local';

        $this->mailFromAddress = $data->mailFromAddress ?? env('MAIL_FROM_ADDRESS', '');

        $this->mailFromName = $data->mailFromName ?? env('MAIL_FROM_NAME', '');

        $this->logo = $data->logo ?? env('MAIL_LOGO_PATH', '/images/51talklogo.jpg');
    }

    public function send()
    {
        try {
            if (isset($this->data->mailFromAddress)) {
                unset($this->data->mailFromAddress);
            }

            if (isset($this->data->mailFromName)) {
                unset($this->data->mailFromName);
            }

            $mailData = (object) [
                'template' => $this->template,
                'subject' => $this->subject,
                'data' => $this->data,
                'logo' => $this->logo,
            ];


            /**Set Mail From Name from the declared value or config */
            $mailData->mailFromAddress = $this->mailFromAddress;
            $mailData->mailFromName = $this->mailFromName;

            if ($this->attachment != "") {
                $mailData->attachment = $this->attachment;
                $mailData->isAttachFromStorage = $this->isAttachFromStorage;
                $mailData->storageDisk = $this->storageDisk;

                if ($this->attachmentName != "") {
                    $mailData->attachmentName = $this->attachmentName;
                }
            }

            $mailer = new Mailer($mailData);
            Mail::to($this->to)
                ->cc($this->cc)
                ->bcc($this->bcc)
                ->send($mailer);

            return (object)[
                'success' => true,
                'message' => 'Success Sending.',
                'stack_trace' => 'N/A'
            ];
        } catch (Exception $e) {
            return (object)[
                'success' => false,
                'message' => $e->getMessage(),
                'stack_trace' => $e
            ];
        }
    }
}
