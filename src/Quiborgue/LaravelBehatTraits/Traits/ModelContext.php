<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

use Quiborgue\Utils\StringUtils;

trait ModelContext {
    /**
     * @static
     * @BeforeScenario
     */
    public static function setUpDb()
    {
        if(\Schema::hasTable('migrations')) {
            \Artisan::call('migrate:reset');
        }
        \Artisan::call('migrate');
    }
    
    /**
     * @Given /^clear model "([^"]+)"$/
     * @Given /^que o modelo "([^"]+)" está vazio$/
     */
    public function clearModel($model)
    {
        $model = studly_case($model);
        $list = $model::truncate();
    }

    /**
     * @Given /^database is seeded$/
     * @Given /^que o banco de dados foi semeado$/
     */
    public function seedModel()
    {
        \Artisan::call('db:seed');
    }


    /**
     * @Then /^the following "([^"]+)" should be stored:$/
     * @Then /^o modelo "([^"]+)" deve ser armazenado:$/
     */
    public function theFollowingModelShouldBeStored($model, TableNode $model_information)
    {
        $model_hash = $model_information->getRowsHash();

        $model = studly_case($model);
        $list = $model::all();

        if (count($list) == 0) {
            throw new \Exception("Could not find any $model.");
        }

        foreach ($list as $item) {
            foreach ($model_hash as $k => $pattern) {
                $k_array = explode(".", $k);
                $value = $item;
                foreach ($k_array as $attr) {
                    $value = $value->$attr;
                }

                if (!StringUtils::isRegex($pattern)) {
                    $pattern = "/".preg_quote($pattern)."/";
                }

                preg_match($pattern, $value, $matches);

                if (!$matches) {
                    throw new \Exception("Could not find $k = $pattern for $model.");
                }
            }
        }
    }

    /**
     * @Then /^show all from model "([^"]+)"$/
     * @Then /^liste o conteudo do modelo "([^"]+)"$/
     */
    public function showAllFrom($model)
    {
        $model = studly_case($model);
        $list = $model::all();

        echo json_encode($list);
    }

    /**
     * @Then /^"([^"]+)" count should be exactly (\d+)$/
     * @Then /^a contagem do modelo "([^"]+)" deve ser exatamente (\d+)$/
     */
    public function modelCountShouldBeExactly($model, $count)
    {
        $model = studly_case($model);
        $list = $model::all();
        if (count($list) != $count) {
            throw new \Exception("Model count is " . count($list) . " but expected $count.");
        }
    }
}