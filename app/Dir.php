<?php

    namespace App;

    class Dir
    {
        private $path;

        public function __construct(string $path)
        {
            $this->path = $path;

            $this->checkExistence();
        }

        private function checkExistence()
        {
            if (!file_exists($this->path)) {
                throw new \RuntimeException('Folder ' . $this->path . ' does not exist');
            }
        }

        public function getAllFiles()
        {
            return $this->getFiles($this->path);
        }

        private function getFiles(string $path): array
        {
            $files = [];

            $dirContent = scandir($path);
            foreach ($dirContent as $filename) {
                if (!in_array($filename, [".", ".."])) {
                    $fullPath = $path . DIRECTORY_SEPARATOR . $filename;

                    if (is_dir($fullPath)) {
                        $files = array_merge($files, $this->getFiles($fullPath));
                    } else {
                        $files[$filename] = $fullPath;
                    }
                }
            }

            return $files;
        }
    }
