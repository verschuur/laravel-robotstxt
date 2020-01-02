<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt;

class RobotsTxtManager
{
    /**
     * The paths defined in the package/app config.
     *
     * @var array
     */
    private $definedPaths = null;

    /**
     * The sitemaps defined in the package/app config.
     *
     * @var array
     */
    private $definedSitemaps = null;

    /**
     * The current application environment
     *
     * @var string
     */
    private $currentEnvironment = null;

    public function __construct()
    {
        /**
         * Note that the config is originally loaded from this package's config file,
         * but a config file can be published to the Laravel config dir.
         * If so, due to the nature of the config setup and Larvel's config merge,
         * the original config gets completely overwritten.
         */
        $this->currentEnvironment = config('app.env');
        $this->definedPaths = config('robots-txt.environments.'.$this->currentEnvironment.'.paths');
        $this->definedSitemaps = config('robots-txt.environments.'.$this->currentEnvironment.'.sitemaps');
    }

    /**
     * Build the array containing all the entries for the txt file.
     *
     * @return array
     */
    public function build(): array
    {
        $paths = ($this->definedPaths) ? $this->getPaths() : $this->defaultRobot();
        $sitemaps = ($this->definedSitemaps) ? $this->getSitemaps() : [];

        return array_merge($paths, $sitemaps);
    }

    /**
     * Returns 'Disallow /' as the default for every robot
     *
     * @return array user agent and disallow string
     */
    protected function defaultRobot(): array
    {
        return ['User-agent: *', 'Disallow: /'];
    }

    /**
     * Assemble all the defined paths from the config.
     *
     * Loop through all the defined paths,
     * creating an array which matches the order of the path entries in the txt file
     *
     * @return array
     */
    protected function getPaths(): array
    {
        // For each user agent, get the user agent name and the paths for the agent,
        // adding them to the array
        $entries = [];

        foreach ($this->definedPaths as $agent => $paths) {
            $entries[] = 'User-agent: ' . $agent;

            $entries = \array_merge($entries, $this->parsePaths('disallow', $paths));
            $entries = \array_merge($entries, $this->parsePaths('allow', $paths));
        }

        return $entries;
    }

    /**
     * Parse defined paths into sitemap entries
     *
     * @param string $directive The directive name (disallow/allow)
     * @param array $paths Array of all the paths
     * @return array Array containing the sitemap entries
     */
    protected function parsePaths(string $directive, array $paths): array
    {
        $entries = [];

        if (array_key_exists($directive, $paths)) {
            foreach ($paths[$directive] as $path) {
                $entries[] = sprintf('%s: %s', ucfirst($directive), $path);
            }
        }

        return $entries;
    }
    
     /**
     * Assemble all the defined sitemaps from the config.
     *
     * Loop through all the defined sitemaps,
     * creating an array which matches the order of the sitemap entries in the txt file
     *
     * @return array
     */
    protected function getSitemaps(): array
    {
        $entries = [];

        foreach ($this->definedSitemaps as $sitemap) {
            // Sitemaps should always use a absolute url.
            // Combinding the sitemap paths with Laravel's url() function will do nicely.
            $entries[] = 'Sitemap: ' . url($sitemap);
        }
        
        return $entries;
    }
}
