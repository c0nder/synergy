<?php

    namespace App\Logger;

    use App\Database;

    class MysqlLogger implements LoggerInterface
    {
        private $db;

        public function __construct(Database $db)
        {
            $this->db = $db;
        }

        private function interpolate(string $message, array $context = [])
        {
            $replace = [];
            foreach ($context as $key => $val) {
                if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                    $replace['{' . $key . '}'] = $val;
                }
            }

            return strtr($message, $replace);
        }

        public function emergency($message, array $context = [])
        {
            $this->log(LogLevel::DEBUG, $message, $context);
        }

        public function alert($message, array $context = [])
        {
            $this->log(LogLevel::ALERT, $message, $context);
        }

        public function critical($message, array $context = [])
        {
            $this->log(LogLevel::CRITICAL, $message, $context);
        }

        public function error($message, array $context = [])
        {
            $this->log(LogLevel::ERROR, $message, $context);
        }

        public function warning($message, array $context = [])
        {
            $this->log(LogLevel::WARNING, $message, $context);
        }

        public function notice($message, array $context = [])
        {
            $this->log(LogLevel::NOTICE, $message, $context);
        }

        public function info($message, array $context = [])
        {
            $this->log(LogLevel::INFO, $message, $context);
        }

        public function debug($message, array $context = [])
        {
            $this->log(LogLevel::DEBUG, $message, $context);
        }

        public function log($level, $message, array $context = [])
        {
            $interpolatedMessage = $this->interpolate($message, $context);

            $this->db->insert('log', [
                'level' => $level,
                'message' => $interpolatedMessage,
                'date' => date('Y-m-d H:i:s')
            ]);
        }
    }