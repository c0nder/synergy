<?php

    namespace App;

    use App\Logger\LoggerInterface;

    class CSVReader
    {
        private $logger;
        private $path = null;
        private $delimiter = ',';

        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

        public function load(string $path)
        {
            if (!file_exists($path) || !is_readable($path)) {
                $this->logger->alert("File '{path}' not found or not readable", ['path' => $path]);
            } else {
                $this->path = $path;
            }
        }

        public function setDelimiter(string $delimiter)
        {
            $this->delimiter = $delimiter;
        }

        public function iterate()
        {
            if ($this->path === null) {
                return [];
            }

            $header = null;
            if (($handle = fopen($this->path, 'r')) !== false)
            {
                while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false)
                {
                    if(!$header)
                        $header = $row;
                    else
                        yield array_combine($header, $row);
                }
                fclose($handle);
            }
        }
    }
