<?php 

namespace Src;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer 
{
    protected $sender;

    protected $sent;
    
    protected $transport;
    
    protected $filename;

    protected $host;

    protected $port;
    
    protected $username;

    protected $password;

    public function __construct($transport = 'smtp')
    {
        $this->transport = $transport;
    }


    public function setSender($email)
    {
        $this->sender = $email;
    }

    public function setFilename($name)
    {
        $this->filename = $name;
    }

    public function setHost($value)
    {
        $this->host = $value;
    }

    public function setPort($value)
    {
        $this->port = $value;
    }

    public function setUsername($value)
    {
        $this->username = $value;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }


    public function send($recipient, $subject, $body)
    {
        if ($this->transport == 'smtp') {

            $mail = new PHPMailer(True);
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->host;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->username;                       //SMTP username
            $mail->Password   = $this->password;                       //SMTP password
            $mail->Port       = $this->port;                 


            //Recipients
            $mail->setFrom($this->sender);
            $mail->addAddress($recipient);     
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $body;
            
            return $mail->send();

        }

        if ($this->transport == 'array') {
            $data = [
                0 =>[
                    'recipient'=> $recipient,
                    'subject'  => $subject,
                    'body'     => $body,
                ]
                ];
            $this->sent = $data;
        
        }
            
        if ($this->transport == 'file') {

            $data = [
                'New Email',
                "Recipient: {$recipient}",
                "Subject: {$subject}",
                "Body: {$body} "
            ];

            file_put_contents($this->filename, "\n\n".implode("\n", $data), FILE_APPEND);
            
            $data = [
                0 =>[
                    'recipient'=> $recipient,
                    'subject'  => $subject,
                    'body'     => $body,
                ]
                ];
            $this->sent = $data;
            return $this->sent;
        
        }
        

    }

    public function getSent()
    {
        return $this->sent;
    }
}

