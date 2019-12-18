<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt\Tests;

use Verschuur\Laravel\RobotsTxt\Controllers\RobotsTxtController;
use Orchestra\Testbench\TestCase;

class RobotsTxtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }
    /**
     * Test that given an environment of 'production', it returns the default allow all
     */
    public function test_has_default_response_for_production_env()
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
    public function test_has_default_response_for_non_production_env()
    {
        $this->app['config']->set('app.env', 'staging');

        $response = $this->get('/robots.txt');

        $response->assertSeeText('User-agent: *' . PHP_EOL . 'Disallow: /');
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
    }

    /**
     * Test that custom paths will overwrite the defaults
     */
    public function test_shows_custom_set_paths()
    {
        $paths = [
            '*' => [
                'disallow' => [
                    '/foobar',
                ],
                'allow' => [
                    '/fizzbuzz'
                ]
            ]
        ];

        $this->setConfig($paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *'. PHP_EOL,
            'Disallow: /foobar' . PHP_EOL,
            'Allow: /fizzbuzz']);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
    }

    /**
     * Test that given multiple user agents, it will return multiple user agent entries
     */
    public function test_shows_multiple_user_agents()
    {
        $bots = [
            'bot1' => [],
            'bot2' => []
        ];

        $this->setConfig($bots);

        $this->app['config']->set('app.env', 'production');
        $this->app['config']->set('robots-txt.environments.production.paths', $bots);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: bot1',
            'User-agent: bot2'
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple paths for a user agent,
     * it will return multiple path entries for a single user agent entry
     */
    public function test_shows_multiple_paths_per_agent()
    {
        $paths = [
            'bender' => [
                'disallow' => [
                    '/foobar',
                    '/barfoo',
                ],
                'allow' => [
                    '/fizzbuzz',
                    '/buzzfizz'
                ]
            ]
        ];

        $this->setConfig($paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: bender' . PHP_EOL ,
            'Disallow: /foobar' . PHP_EOL,
            'Disallow: /barfoo' . PHP_EOL,
            'Allow: /fizzbuzz' . PHP_EOL,
            'Allow: /buzzfizz' . PHP_EOL,
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple paths for multiple user agents,
     * it will return multiple path entries for multiple user agent entries
     */
    public function test_shows_multiple_paths_for_multiple_agents()
    {
        $paths = [
            'bender' => [
                'disallow' => [
                    '/foobar',
                    '/barfoo',
                ],
                'allow' => [
                    '/fizzbuzz',
                    '/buzzfizz'
                ]
            ],
            'flexo' => [
                'disallow' => [
                    '/fizzbuzz',
                    '/buzzfizz'
                ],
                'allow' => [
                    '/foobar',
                    '/barfoo',
                ]
            ]
        ];

        $this->setConfig($paths);

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: bender' . PHP_EOL ,
            'Disallow: /foobar' . PHP_EOL,
            'Disallow: /barfoo' . PHP_EOL,
            'Allow: /fizzbuzz' . PHP_EOL,
            'Allow: /buzzfizz' . PHP_EOL,

            'User-agent: flexo' . PHP_EOL ,
            'Disallow: /fizzbuzz' . PHP_EOL,
            'Disallow: /buzzfizz' . PHP_EOL,
            'Allow: /foobar' . PHP_EOL,
            'Allow: /barfoo' . PHP_EOL,
        ]);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    /**
     * Test that given multiple environments, it returns the correct path for the given environment
     */
    public function test_shows_correct_paths_for_multiple_environments()
    {
        $environments = [
            'production' => [
                'paths' => [
                    '*' => [
                        'disallow' => [
                            '/foobar'
                        ]
                    ],
                ]
            ],
            'staging' => [
                'paths' => [
                    '*' => [
                        'allow' => [
                            '/barfoo'
                        ]
                    ],
                ]
            ]
        ];

        
        $this->app['config']->set('robots-txt.environments',  $environments);

        // Test env #1
        $this->app['config']->set('app.env', 'production');

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Disallow: /foobar',
        ]);
        $response->assertDontSeeText('Allow: /barfoo' . PHP_EOL);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);

        // Test env #2
        $this->app['config']->set('app.env', 'staging');

        $response = $this->get('/robots.txt');

        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Allow: /barfoo'
        ]);
        $response->assertDontSeeText('Disallow: /foobar' . PHP_EOL);
        $response->assertDontSeeText('Disallow:  ' . PHP_EOL);
        $response->assertDontSeeText('Disallow: /' . PHP_EOL);
    }

    public function test_output_content_type_is_text_plain_utf_eight()
    {
        $response = $this->get('/robots.txt');
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_shows_sitemaps()
    {
        $sitemaps = [
            'sitemap-foo.xml',
            'sitemap-bar.xml',
        ];

        $this->setConfig($sitemaps, 'sitemaps');

        $response = $this->get('/robots.txt');
        $response->assertSeeTextInOrder([
            'User-agent: *' . PHP_EOL,
            'Disallow: ' . PHP_EOL,
            'Sitemap: http://localhost/sitemap-foo.xml' . PHP_EOL,
            'Sitemap: http://localhost/sitemap-bar.xml'
        ]);
    }

    protected function setConfig(array $data, string $section = 'paths', string $env = 'production')
    {
        $this->app['config']->set('app.env', $env);
        $this->app['config']->set('robots-txt.environments.production.' . $section, $data);
    }

    protected function getPackageProviders($app)
    {
        return [\Verschuur\Laravel\RobotsTxt\Providers\RobotsTxtProvider::class];
    }
}
