<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\Dashboard\Entities\MailPhoto;

class RegistrationSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    protected $content;
    protected $title;
    protected $mail_from;
    protected $mail_to;
    protected $user;


    /**
     * Create a new message instance.
     *
     * @param $arrParams
     */
    public function __construct($arrParams)
    {
        $this->content = $arrParams['content'];
        $this->title = $arrParams['title'];
        $this->mail_from = $arrParams['from'];
        $this->mail_to = $arrParams['to'];
        $this->user = $arrParams['user'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.system.registration_successful')
            ->with('content', $this->content)
            ->from($this->mail_from, $this->user->name)
            ->replyTo($this->mail_from, $this->user->name)
            ->subject($this->title);

//        //-- Check if necessary to add some images to email as attachment
//        $attachments = MailPhoto::where('user_id', Auth::id())->get();
//
//        if (!empty($attachments)) {
//            foreach ($attachments as $attachment) {
//                $email->attach(public_path('storage/'.$attachment->path));
//            }
//        }

        return $email;
    }
}
