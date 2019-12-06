<?php

namespace App\Feature;

use Cake\Core\Configure;
use RuntimeException;

class Factory
{
    public const FEATURE_INTERFACE = 'App\\Feature\\FeatureInterface';
    public const FEATURE_SUFFIX = 'Feature';

    protected static $initialized = false;

    protected static $defaultOptions = ['name' => 'Base', 'active' => true];

    /**
     * Initialize feature.
     *
     * @return void
     */
    public static function init(): void
    {
        if (static::$initialized) {
            return;
        }
        // set factory as initialized.
        static::$initialized = true;

        $features = Configure::read('Features');

        foreach ($features as $feature => $options) {
            $config = static::getConfig($feature);
            $class = static::getFeatureClass($config);
            $feature = new $class($config);
            $feature->isActive() ? $feature->enable() : $feature->disable();
        }
    }

    /**
     * Get feature method.
     *
     * @param string $name Feature name
     * @return \App\Feature\FeatureInterface
     */
    public static function get(string $name): \App\Feature\FeatureInterface
    {
        if (!static::$initialized) {
            static::init();
        }

        $config = static::getConfig($name);
        $class = static::getFeatureClass($config);

        return new $class($config);
    }

    /**
     * Features list getter.
     *
     * @param string $type Feature type
     * @return mixed[]
     */
    public static function getList(string $type = ''): array
    {
        $features = Configure::read('Features');

        if (empty($features)) {
            return [];
        }

        $result = [];
        /**
         * @var string $feature
         */
        foreach (array_keys($features) as $feature) {
            if ($type && 0 !== strpos($feature, $type)) {
                continue;
            }

            $config = static::getConfig($feature);
            $class = static::getFeatureClass($config);

            $result[] = new $class($config);
        }

        return $result;
    }

    /**
     * Feature Config getter method.
     *
     * @param string $feature Feature name
     * @return \App\Feature\Config
     */
    protected static function getConfig(string $feature): \App\Feature\Config
    {
        $options = Configure::read('Features.' . $feature);

        if (!empty($options)) {
            $options['name'] = $feature;
        }

        if (empty($options)) {
            $options = static::$defaultOptions;
        }

        return new Config($options);
    }

    /**
     * Feature class name getter.
     *
     * @param \App\Feature\Config $config Config instance
     * @return string
     */
    protected static function getFeatureClass(Config $config): string
    {
        $name = explode(DS, $config->get('name'));
        $name = implode('\\', $name);

        $class = __NAMESPACE__ . '\\Type\\' . $name . static::FEATURE_SUFFIX;
        if (!class_exists($class)) {
            throw new RuntimeException(
                'Class [' . $class . '] does not exist.'
            );
        }

        if (!in_array(static::FEATURE_INTERFACE, class_implements($class))) {
            throw new RuntimeException(
                'Feature class [' . $class . '] does not implement [' . static::FEATURE_INTERFACE . '].'
            );
        }

        return $class;
    }
}
