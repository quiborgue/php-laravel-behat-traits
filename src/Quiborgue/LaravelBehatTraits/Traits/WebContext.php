<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

trait WebContext {
    protected $web_response;

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        $user = new User;
     
        $this->be($user);
    }

    /**
     * @When I visit :uri
     */
    public function iVisit($uri)
    {
        $this->web_response = $this->call('GET', $uri);
    }

    /**
     * @Then I should see :text
     */
    public function iShouldSee($text)
    {
        $crawler = new Crawler($this->web_response->getContent());
     
        if (!count($crawler->filterXpath("//text()[. = '{$text}']"))) {
            throw new \Exception("Text '$text' was not found.");
        }
    }
}