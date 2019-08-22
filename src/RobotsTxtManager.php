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
    private $definedPaths = [];

    /**
     * The sitemaps defined in the package/app config.
     *
     * @var array
     */
    private $definedSitemaps = [];

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
        $this->definedPaths = config('robots-txt.paths');
        $this->definedSitemaps = config('robots-txt.sitemaps');
        $this->currentEnvironment = config('app.env');
    }

    /**
     * Build the array containing all the entries for the txt file.
     *
     * @return array
     */
    public function build(): array
    {
        $paths = $this->hasValidDefinitions($this->definedPaths) ? $this->getPaths() : $this->defaultRobot();
        $sitemaps = $this->hasValidDefinitions($this->definedSitemaps) ? $this->getSitemaps() : [];

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
     * Validates the set environment paths and the current environment
     *
     * If:
     * - The defined environments are null (missing from config)
     * - One or more enviroments are set, but have no pathss
     * - The current environment cannot be matched against the defined environments
     * then return false;
     *
     * @return boolean
     */
    protected function hasValidDefinitions(?array $definitions): bool
    {
        // The'robots-txt.paths' config path return null.
        if ($definitions === null) {
            return false;
        }

        // The'robots-txt.paths' config path return an empty array.
        if (empty($definitions)) {
            return false;
        }

        // The current environment cannot be matched against the defined environments.
        if (!array_key_exists($this->currentEnvironment, $definitions)) {
            return false;
        }
        
        return true;
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

        $agents = $this->definedPaths[$this->currentEnvironment];
        foreach ($agents as $name => $paths) {
            $entries[] = 'User-agent: ' . $name;

            foreach ($paths as $path) {
                $entries[] = 'Disallow: ' . $path ;
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

        $sitemaps = $this->definedSitemaps[$this->currentEnvironment];
        foreach ($sitemaps as $sitemap) {
            $entries[] = 'Sitemap: ' . url($sitemap);
        }

        return $entries;
    }
}
