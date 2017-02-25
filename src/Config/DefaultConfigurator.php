<?php

namespace Brendt\Image\Config;

use Brendt\Image\Exception\InvalidConfigurationException;
use Brendt\Image\ResponsiveFactory;
use Brendt\Image\Scaler\AbstractScaler;
use Brendt\Image\Scaler\FileSizeScaler;
use Brendt\Image\Scaler\Scaler;
use Brendt\Image\Scaler\WidthScaler;

class DefaultConfigurator implements ResponsiveFactoryConfigurator
{

    /**
     * The default config
     *
     * @var array
     */
    protected $config = [
        'driver'       => 'gd',
        'publicPath'   => './',
        'sourcePath'   => './',
        'enableCache'  => false,
        'optimize'     => false,
        'scaler'       => 'filesize',
        'stepModifier' => 0.5,
        'minFileSize'  => 5000,
        'minWidth'     => 150,
    ];

    /**
     * ResponsiveFactoryConfigurator constructor.
     *
     * @param array $config
     *
     * @throws InvalidConfigurationException
     */
    public function __construct(array $config = []) {
        if (isset($config['driver']) && !in_array($config['driver'], ['gd', 'imagick'])) {
            throw new InvalidConfigurationException('Invalid driver. Possible drivers are `gd` and `imagick`');
        }

        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param ResponsiveFactory $factory
     *
     * @return void
     */
    public function configure(ResponsiveFactory $factory) {
        /** @var AbstractScaler $scaler */
        switch ($this->config['scaler']) {
            case 'filesize':
                $scaler = new FileSizeScaler($this);
                break;
            case 'width':
            default:
                $scaler = new WidthScaler($this);
                break;
        }

        $factory
            ->setDriver($this->config['driver'])
            ->setPublicPath($this->config['publicPath'])
            ->setSourcePath($this->config['sourcePath'])
            ->setEnableCache($this->config['enableCache'])
            ->setOptimize($this->config['optimize'])
            ->setScaler($scaler);
    }

    /**
     * @param Scaler $scaler
     *
     * @return Scaler
     */
    public function configureScaler(Scaler $scaler) {
        $scaler
            ->setMinFileSize($this->config['minFileSize'])
            ->setMinWidth($this->config['minWidth'])
            ->setStepModifier($this->config['stepModifier']);

        return $scaler;
    }

    /**
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key) {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}