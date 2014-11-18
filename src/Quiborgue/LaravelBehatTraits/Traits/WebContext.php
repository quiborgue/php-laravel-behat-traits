<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

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

    /**
     * @Then I should be on :arg1
     */
    public function iShouldBeOn($uri)
    {
        $response = $this->client->getResponse();

        if (!is_a($response, 'Illuminate\Http\RedirectResponse')) {
            throw new \Exception("Response is not Illuminate\Http\RedirectResponse");
        }

        $expected = $this->app['url']->to($uri);
        $got = $response->headers->get('Location');
        if ($expected != $got) {
            throw new \Exception("Expected $expected got $got");
        }
    }

}