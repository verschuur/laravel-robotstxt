<?php

namespace Verschuur\Laravel\RobotsTxt\Tests;

use Verschuur\Laravel\RobotsTxt\Controllers\RobotsTxtController;
use Verschuur\Laravel\RobotsTxt\RobotsTxtProvider;
use Orchestra\Testbench\TestCase;

class RobotsTxtTest extends TestCase
{

    /**
     * Test that given an environment of 'production', it returns the default allow all
     */
    public function testItReturnsDefaultResponseForProductionEnv()
    {
        config(['app.env' => 'production']);
        $this->visit('/robots.txt')
             ->see('User-agent: *')
             ->see('Disallow: ')
             ->dontSee('Disallow: /'  . PHP_EOL);
    }

    /**
     * Test that given any other environment than 'production', it returns the default allow none
     */
    public function testItReturnsDefaultResponseForNonProductionEnv()
    {
        config(['app.env' => 'staging']);
        $this->visit('/robots.txt')
             ->see('User-agent: *' . PHP_EOL . 'Disallow: /')
             ->dontSee('Disallow:  ');
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

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: *'. PHP_EOL . 'Disallow: /foobar')
             ->dontSee('Disallow:  ');
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

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: bot1' . PHP_EOL)
             ->see('User-agent: bot2' . PHP_EOL)
             ->dontSee('Disallow:  ')
             ->dontSee('Disallow: /' . PHP_EOL);
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

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: *' . PHP_EOL . 'Disallow: /foobar' . PHP_EOL . 'Disallow: /barfoo')
             ->dontSee('Disallow:  ')
             ->dontSee('Disallow: /' . PHP_EOL);
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

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: *' . PHP_EOL . 'Disallow: /foobar' . PHP_EOL . 'Disallow: /barfoo')
             ->see('User-agent: bot1' . PHP_EOL . 'Disallow: /helloworld' . PHP_EOL . 'Disallow: /sorryicantdothatdave')
             ->dontSee('Disallow:  ')
             ->dontSee('Disallow: /' . PHP_EOL);
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

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: *' . PHP_EOL . 'Disallow: /foobar')
             ->dontSee('User-agent: *' . PHP_EOL . 'Disallow: /barfoo')
             ->dontSee('Disallow:  ')
             ->dontSee('Disallow: /' . PHP_EOL);

        config([
            'app.env' => 'staging',
            'robots-txt.paths' => $paths
        ]);

        $this->visit('/robots.txt')
             ->see('User-agent: *' . PHP_EOL . 'Disallow: /barfoo')
             ->dontSee('User-agent: *' . PHP_EOL . 'Disallow: /foobar')
             ->dontSee('Disallow:  ')
             ->dontSee('Disallow: /' . PHP_EOL);
    }
}
