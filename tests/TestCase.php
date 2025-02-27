<?php
namespace LaravelFillableRelations\Tests;

use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench']);
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // set up database configuration
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        /*
        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'database' => 'fillable_relations_test',
            'host' => 'localhost',
            'username' => 'root',
        ]);
        */
    }

    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TestServiceProvider::class
        ];
    }

    /**
     * Mock the event dispatcher so all events are silenced and collected.
     *
     * @return $this
     */
    protected function withoutEvents()
    {
        $mock = Mockery::mock(Dispatcher::class);
        $mock->shouldReceive('fire', 'until');
        $this->app->instance('events', $mock);
        return $this;
    }
}