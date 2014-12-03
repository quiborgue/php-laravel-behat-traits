<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Quiborgue\Utils\ArrayUtils;

trait RestContext {
    protected $rest_response;

    /**
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
     * @When /^(?:eu )?envio uma requisição ([A-Z]+) para "([^"]+)"$/
     */
    public function iSendARequestTo($method, $uri) {
        $this->rest_response = $this->call($method, $uri);
    }

    /**
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with parameters:$/
     * @When /^eu envio uma requisião ([A-Z]+) para "([^"]+)" com os parametros:$/
     */
    public function iSendARequestToWithTheFollowing($method, $uri, TableNode $params) {
        $this->rest_response = $this->call($method, $uri, $params->getRowsHash());
    }

    /**
     * @Then the response status should be :code
     * @Then o código da resposta deve ser :code
     */
    public function theResponseStatusShouldBe($code)
    {
        if ($this->rest_response->getStatusCode() != $code) {
            throw new \Exception("Expected $code but got {$this->rest_response->getStatusCode()}");
        }
    }

    /**
     * @Then the JSON response should be:
     * @Then a resposta JSON deve ser:
     */
    public function theJsonResponseShouldBe(PyStringNode $string)
    {
        $json_given = json_decode($string, true);
        if (!$json_given) {
            throw new \Exception("Following given JSON is malformed:\n" . print_r($string, true));
        }

        $response = $this->rest_response->getContent();
        $json_expected = json_decode($response, true);
        if (!$json_expected) {
            throw new \Exception("Following expected JSON is malformed:\n" . print_r($response, true));
        }

        $diff = ArrayUtils::diff_assoc_recursive($json_expected, $json_given);
        if (count($diff) > 0) {
            throw new \Exception("Following differs between jsons:\n" . print_r($diff, true));
        }
    }

    /**
     * @Then the JSON response should be an array with :count elements
     * @Then a resposta JSON deve ser um array com :count elementos
     */
    public function theJsonResponseShouldBeAnArrayWithElements($count)
    {
        $response = $this->rest_response->getContent();
        $json_expected = json_decode($response, true);
        if (!$json_expected) {
            throw new \Exception("Following expected JSON is malformed:\n" . print_r($response, true));
        }

        if (!is_array($json_expected)) {
            throw new \Exception("Expected json is not array:\n" . json_encode($json_expected));
        }

        $given_count = count($json_expected);
        if ($given_count != $count) {
            throw new \Exception("Expected json array does not have $count elements. $given_count elements found.");
        }
    }
}