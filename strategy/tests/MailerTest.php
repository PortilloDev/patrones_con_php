<?php 

namespace Tests;

use Src\Mailer;

use StephaneCoinon\Mailtrap\Client;
use StephaneCoinon\Mailtrap\Model;
use StephaneCoinon\Mailtrap\Inbox;


class MailerTest extends TestCase
{

    
    function test_it_stores_the_sent_emails_in_an_array()
    {
        $mailer = new Mailer('array');

        $mailer->setSender('admin@app.com');
        
        $mailer->send('ipp_1981@hotmail.com', 'An example message', 'The content of the message');
        
        $sent = $mailer->getSent();
        $this->assertCount(1, $sent);
        $this->assertSame('ipp_1981@hotmail.com', $sent[0]['recipient']);
        $this->assertSame('An example message', $sent[0]['subject']);
        $this->assertSame('The content of the message', $sent[0]['body']);
    }


    function test_it_stores_sent_emails_in_a_log_file()
    {
        $filename = __DIR__.'/../storage/test.txt';
        //@unlink($filename);

        $mailer = new Mailer('file');

        $mailer->setSender('admin@app.com');
        $mailer->setFilename($filename);


        $mailer->send('ipp_1981@hotmail.com', 'An example message', 'The content of the message');

        $content = explode("\n" , file_get_contents($filename));

        $this->assertContains('Recipient: ipp_1981@hotmail.com', $content);
        $this->assertContains('Subject: An example message', $content);
        $this->assertContains('Body: The content of the message', $content);
    }
    
    function test_it_sends_emails_using_smtp()
    {

        // Instantiate Mailtrap API client
        $client = new Client('607b2831d73ce4cf8af179fab08f3c3e');

        // Boot API models
        Model::boot($client);

        // Fetch an inbox by its id
        $inbox = Inbox::find(1623105);

        $inbox->empty();

        $mailer = new Mailer('smtp');

        $mailer->setSender('admin@app.com');
        $mailer->setHost('smtp.mailtrap.io');                     
        $mailer->setUsername('61caba96fe089b');                      
        $mailer->setPassword('214334dd77603a');                       
        $mailer->setPort(2525);  
        
        $sent = $mailer->send('ipp_1981@hotmail.com', 'An example message', 'The content of the message');
        $this->assertTrue($sent);


        // Get the last (newest) message in an inbox
        $newestMessage = $inbox->lastMessage();

        $this->assertNotNull($newestMessage);
        $this->assertSame(['ipp_1981@hotmail.com'],$newestMessage->recipientEmails());
        $this->assertSame('An example message', $newestMessage->subject());
        $this->assertContains('The content of the message', array($newestMessage->textBody()));
    }
} 