<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

use Quiborgue\Utils\StringUtils;

trait WebContext {

    protected $web_response;

    /**
     * @Given I am logged in
     * @Given que eu estou logado
     */
    public function iAmLoggedIn()
    {
        $user_class = \Config::get('auth.model');
        $user = $user_class::all()->first();
        $this->be($user);
    }

    /**
     * @When I visit :uri
     * @When eu acesso :uri
     */
    public function iVisit($uri)
    {
        $this->web_response = $this->call('GET', $uri);
    }

    /**
     * @Then I should see :pattern
     * @Then eu devo ver :pattern
     */
    public function iShouldSee($pattern)
    {
        if (!StringUtils::is_regex($pattern)) {
            $pattern = "/" . preg_quote($pattern) . "/";
        }
        
        preg_match($pattern, $this->web_response->getContent(), $matches);
        if (!$matches) {
            throw new \Exception("Could not find $pattern pattern");
        }
    }

    /**
     * @Then I should be redirected to :uri
     * @Then eu devo ser redirecionado para :uri
     */
    public function iShouldBeRedirectedTo($uri)
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

    /**
     * @Then the response should be printed
     * @Then a resposta deve ser exibida
     */
    public function theResponseShouldBePrinted()
    {
        echo $this->web_response->getContent();
    }   

}