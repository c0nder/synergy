<?php

    namespace App;

    class Config
    {
        private $config;

        private static $instance = null;

        private function __construct()
        {
            $this->setConfig();
        }

        private function setConfig()
        {
            if (!file_exists('settings.php')) {
                throw new \Exception('File config not found');
            }

            $this->config = include "settings.php";
        }

        public static function getInstance()
        {
            if (static::$instance === null) {
                static::$instance = new static();
            }

            return static::$instance;
        }

        public function get(string $key)
        {
            if (strpos($key, '.') === false) {
                return $this->config[$key] ?? null;
            }

            $pathKeys = explode('.', $key);

            $data = $this->config[array_shift($pathKeys)] ?? [];
            foreach ($pathKeys as $key) {
                if (isset($data[$key])) {
                    $data = $data[$key];
                }
            }

            return $data;
        }
    }