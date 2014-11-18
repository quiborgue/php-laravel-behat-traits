<?php namespace Quiborgue\LaravelBehatTraits\Traits;

use Illuminate\Foundation\Testing\ApplicationTrait;

trait LaravelSetup {
    use ApplicationTrait;
    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        if ( ! $this->app)
        {
            $this->refreshApplication();
        }
    }

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;
 
        $testEnvironment = 'behat';

        return require __DIR__.'/../../../../../../../bootstrap/start.php';
    }
}