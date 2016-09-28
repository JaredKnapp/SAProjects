<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SendEmails extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

		$this->load->model('Email_model', 'emailmodel');
        $this->load->library('email');
    }

    public function release($sendall = false){

		$emails = $this->emailmodel->get('date_sent IS NULL');

        foreach($emails as $email){

            $this->email->clear();

            $this->email->from($email['from']);
            $this->email->to($email['to']);
            $this->email->cc($email['cc']);
            $this->email->subject($email['subject']);
            $this->email->message($email['body']);

            if ($this->email->send())
            {
                $this->emailmodel->markSent($email['id']);
            }

        }
    }
}