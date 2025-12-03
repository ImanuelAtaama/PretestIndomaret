<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class NewUserMail extends Mailable
{
    public $user;
    public $plainPassword;

    public function __construct(User $user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Selamat Datang!')
                    ->view('emails.new_user')
                    ->with([
                        'user' => $this->user,
                        'plainPassword' => $this->plainPassword
                    ]);
    }
}

