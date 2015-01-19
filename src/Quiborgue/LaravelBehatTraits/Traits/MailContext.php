<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

use Quiborgue\Utils\StringUtils;
use Mockery;

trait MailContext {
    protected $mock;
    protected $error = null;

    /**
     * @Then an email should be sent to :to with the following subject :subject and text:
     * @Then um email deve ser enviado para :to com o seguinte assunto :subject e texto:
     */
    public function anEmailShouldBeSentToWithTheFollowingText($to, $subject, PyStringNode $body) {
        $this->mock = Mockery::mock('Swift_Mailer');
        $this->app['mailer']->setSwiftMailer($this->mock);

        if (!StringUtils::isRegex($body)) {
            $body = "/" . preg_quote($body) . "/";
        }

        $this->mock->shouldReceive('send')->once()
            ->andReturnUsing(function(\Swift_Message $msg) use ($to, $subject, $body,  &$result) {
                $matches = null;

                if (!StringUtils::isRegex($subject)) {
                    $subject = "/" . preg_quote($subject) . "/";
                }
                preg_match($subject, $msg->getSubject(), $matches);
                if (!$matches) {
                    $this->error = new \Exception("Subject should be $subject but got " . $msg->getSubject());
                }

                $tos = array_keys($msg->getTo());

                foreach ($tos as $eto) {
                    if (!StringUtils::isRegex($to)) {
                        $to = "/" . preg_quote($to) . "/";
                    }
                    preg_match($to, $eto, $matches);
                    if (!$matches) {
                        $this->error = new \Exception("To should be $to but got " . $eto);
                    }    
                }
                
                preg_match($body, StringUtils::clearText($msg->getBody()), $matches);
                if (!$matches) {
                    $this->error = new \Exception("Body should be $body but got " . StringUtils::clearText($msg->getBody()));
                }
            });
    }

    /**
     * @AfterScenario
     */
    public function setDownMail()
    {
        if ($this->error) {
            throw $this->error;
        }
    }
}