## Laravel Behat Traits

### Usage:

#### Add to composer.json
```
"require-dev": {
    "behat/behat": "3.0.*",
    "quiborgue/utils": "dev-master",
    "quiborgue/laravel-behat-traits": "dev-master"
},
```

#### Edit your FeatureContext
```
<?php
require_once __DIR__.'/../../bootstrap/autoload.php';

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Quiborgue\LaravelBehatTraits\Traits\LaravelSetup;
use Quiborgue\LaravelBehatTraits\Traits\RestContext;
use Quiborgue\LaravelBehatTraits\Traits\WebContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    use LaravelSetup;
    use RestContext;
    use WebContext;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }
}
```

#### Available steps
```
RestContext |  When /^(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
RestContext |  When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with parameters:$/
RestContext |  Then the response status should be :code
RestContext |  Then the JSON response should be:
RestContext |  Then the JSON response should be an array with :count elements
WebContext  | Given I am logged in
WebContext  |  When I visit :uri
WebContext  |  Then I should see :text
```