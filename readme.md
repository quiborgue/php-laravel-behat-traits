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