<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\RedirectResponse;

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
        \Artisan::call('db:seed');
    }
    
    /**
     * @Given /^clear model "([^"]+)"$/
     */
    public function clearModel($model)
    {
        $model = studly_case($model);
        $list = $model::truncate();
    }


    /**
     * @Then /^the following "([^"]+)" should be stored:$/
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
            foreach ($model_hash as $k => $v) {
                preg_match($v, $item->$k, $matches);

                if (!$matches) {
                    throw new \Exception("Could not find $k = $v for $model.");
                }
            }
        }
    }

    /**
     * @Then /^show all from "([^"]+)"$/
     */
    public function showAllFrom($model)
    {
        $model = studly_case($model);
        $list = $model::all();

        echo json_encode($list);
    }

    /**
     * @Then /^"([^"]+)" count should be exactly (\d+)$/
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