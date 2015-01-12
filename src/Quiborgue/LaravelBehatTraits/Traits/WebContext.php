<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

use Quiborgue\Utils\StringUtils;

trait WebContext {

    protected $webResponse;

    /**
     * @Given I am logged in
     * @Given que eu estou logado
     */
    public function iAmLoggedIn()
    {
        $userClass = \Config::get('auth.model');
        $user = $userClass::all()->first();
        $this->be($user);
    }

    /**
     * @Given I am logged in as :email
     * @Given que eu estou logado como :email
     */
    public function queEuEstouLogadoComo($email)
    {
        $userClass = \Config::get('auth.model');
        $user = $userClass::where('email','=',$email)->first();
        $this->be($user);
    }

    /**
     * @When I visit :uri
     * @When eu acesso :uri
     */
    public function iVisit($uri)
    {
        $this->webResponse = $this->call('GET', $uri);
    }

    /**
     * @Then I should see :pattern
     * @Then eu devo ver :pattern
     */
    public function iShouldSee($pattern)
    {
        if (!StringUtils::isRegex($pattern)) {
            $pattern = "/" . preg_quote($pattern) . "/";
        }
        
        preg_match($pattern, $this->webResponse->getContent(), $matches);
        if (!$matches) {
            throw new \Exception("Could not find $pattern pattern");
        }
    }

    /**
     * @Then I should not see :pattern
     * @Then eu não devo ver :pattern
     */
    public function iShouldNotSee($pattern)
    {
        if (!StringUtils::isRegex($pattern)) {
            $pattern = "/" . preg_quote($pattern) . "/";
        }
        
        preg_match($pattern, $this->webResponse->getContent(), $matches);
        if ($matches) {
            throw new \Exception("Found $pattern pattern");
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
        echo $this->webResponse->getContent();
    }

    /**
     * @Then I should see :pattern inside :css
     * @Then eu devo ver :pattern em :css
     */
    public function iShouldSeeInside($pattern, $css)
    {
        $crawler = new Crawler($this->webResponse->getContent());
        $text = $crawler->filter($css)->text();

        if (!StringUtils::isRegex($pattern)) {
            $pattern = "/" . preg_quote($pattern) . "/";
        }

        preg_match($pattern, $text, $matches);
        if (!$matches) {
            throw new \Exception("Could not find $pattern pattern");
        }
    }

}