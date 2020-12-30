<?php
    require_once "vendor/autoload.php";

    use App\{
        Config,
        Database,
        Dir,
        CSVReader,
        Logger\MysqlLogger
    };

    /** @var Config $config */
    $config = Config::getInstance();

    /** @var Database $db */
    $db = Database::getInstance($config);

    $importDir = new Dir($config->get('import_folder'));
    $importFiles = $importDir->getAllFiles();

    $logger = new MysqlLogger($db);
    $reader = new CSVReader($logger);

    foreach ($importFiles as $filename => $path) {
        $reader->load($path);

        foreach ($reader->iterate() as $data) {
            $db->insert('data', $data);
        }

        $logger->info('Finished parsing file {path}', ['path' => $path]);
    }
