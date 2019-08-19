<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt\Tests;

use Verschuur\Laravel\RobotsTxt\Controllers\RobotsTxtController;
use Orchestra\Testbench\TestCase;

class RobotsTxtTest extends TestCase
{
    /**
     * Test that given an environment of 'production', it returns the default allow all
     */
    public function testItReturnsDefaultResponseForProductionEnv()
    {
        $this->app['config']->set('app.env', 'production');

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *',
            'Disallow: '
        ]);
        $response->assertDontSeeText('Disallow: /'  . PHP_EOL);
    }

    /**
     * Test that given any other environment than 'production', it returns the default allow none
     */
    public function testItReturnsDefaultResponseForNonProductionEnv()
    {
        $this->app['config']->set('app.env', 'staging');

        $response = $this->get('/robots.txt');

        $response->assertSeeText('User-agent: *' . PHP_EOL . 'Disallow: /');
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
    }

    /**
     * Test that custom paths will overwrite the defaults
     */
    public function testItShowCustomSetPaths()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                ]
            ]
        ];

        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeText('User-agent: *'. PHP_EOL . 'Disallow: /foobar');
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
    }

    /**
     * Test that given multiple user agents, it will return multiple user agent entries
     */
    public function testItShowMultipleUserAgents()
    {
        $paths = [
            'production' => [
                'bot1' => [],
                'bot2' => []
            ]
        ];

        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: bot1' . PHP_EOL,
            'User-agent: bot2' . PHP_EOL
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple paths for a user agent,
     * it will return multiple path entries for a single user agent entry
     */
    public function testItShowMultiplePathsPerAgent()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                    '/barfoo'
                ],
            ]
        ];

        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL ,
            'Disallow: /foobar' . PHP_EOL,
            'Disallow: /barfoo'
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple paths for multiple user agents,
     * it will return multiple path entries for multiple user agent entries
     */
    public function testItShowMultiplePathsForMultipleAgents()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                    '/barfoo'
                ],
                'bot1' => [
                    '/helloworld',
                    '/sorryicantdothatdave'
                ]
            ]
        ];

        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Disallow: /foobar' . PHP_EOL,
            'Disallow: /barfoo' . PHP_EOL,
            'User-agent: bot1' . PHP_EOL,
            'Disallow: /helloworld' . PHP_EOL,
            'Disallow: /sorryicantdothatdave' . PHP_EOL
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple environments, it returns the correct path for the given environment
     */
    public function testItShowsCorrectPathsForMultipleEnvironments()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                ]
            ],
            'staging' => [
                '*' => [
                    '/barfoo'
                ]
            ]
        ];

        // Test env #1
        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Disallow: /foobar' . PHP_EOL
        ]);
        $response->assertDontSeeText('Disallow: /barfoo' . PHP_EOL);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);

        // Test env #2
        $this->app['config']->set('app.env', 'staging');
        $this->app['config']->set('robots-txt.paths', $paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Disallow: /barfoo' . PHP_EOL
        ]);
        $response->assertDontSeeText('Disallow: /foobar' . PHP_EOL);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL); 
    }

    protected function getPackageProviders($app)
    {
        return [\Verschuur\Laravel\RobotsTxt\RobotsTxtProvider::class];
    }
}
