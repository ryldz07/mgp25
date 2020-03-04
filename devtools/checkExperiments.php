<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

/*
 * This tool checks all PHP files to ensure experiments are whitelisted.
 *
 * The isExperimentEnabled() and getExperimentParam() functions require
 * that the queried experiment be whitelisted in the StorageHandler.
 *
 * This script ensures that all experiments used within the code are
 * whitelisted inside of the StorageHandler's EXPERIMENT_KEYS constant.
 *
 * Tip: Execute this script with ANY extra argument, to hide all valid files and
 * focus only on the files with problems.
 * For example: "php devtools/checkExperiments.php x".
 */

$onlyShowInvalidFiles = isset($argv[1]); // Hide valid files if argv[1] is set.
$checkExperiments = new checkExperiments(
    __DIR__.'/../',
    [ // List of all subfolders to inspect.
        'devtools',
        'examples',
        'src',
        'tests',
    ],
    $onlyShowInvalidFiles
);
$problemFiles = $checkExperiments->run();
if (!empty($problemFiles)) {
    // Exit with non-zero code to signal that there are problems.
    exit(1);
}

class checkExperiments
{
    /**
     * @var string
     */
    private $_baseDir;

    /**
     * @var string[]
     */
    private $_inspectFolders;

    /**
     * @var bool
     */
    private $_onlyShowInvalidFiles;

    /**
     * @var array
     */
    private $_experimentWhitelist;

    /**
     * Constructor.
     *
     * @param string   $baseDir
     * @param string[] $inspectFolders
     * @param bool     $onlyShowInvalidFiles
     */
    public function __construct(
        $baseDir,
        array $inspectFolders,
        $onlyShowInvalidFiles)
    {
        $this->_baseDir = realpath($baseDir);
        if ($this->_baseDir === false) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid path.', $baseDir));
        }
        $this->_inspectFolders = $inspectFolders;
        $this->_onlyShowInvalidFiles = $onlyShowInvalidFiles;
        $this->_experimentWhitelist = \InstagramAPI\Settings\StorageHandler::EXPERIMENT_KEYS;
    }

    /**
     * Process single file.
     *
     * @param string $filePath
     *
     * @return bool TRUE if the file uses an un-whitelisted experiment, otherwise FALSE.
     */
    private function _processFile(
        $filePath)
    {
        $hasProblems = false;
        $processedExperiments = [];
        $inputLines = @file($filePath);

        if ($inputLines === false) {
            // We were unable to read the input file. Ignore if broken symlink.
            if (is_link($filePath)) {
                return false; // File is okay, since the symlink is invalid.
            } else {
                echo "- {$filePath}: UNABLE TO READ FILE!".PHP_EOL;

                return true; // File has problems...
            }
        }

        if (preg_match_all('/->(?:getExperimentParam|isExperimentEnabled)\((?:\n {1,})?(?:\'|")(\w{1,})(?:\'|")/', implode('', $inputLines), $matches)) {
            foreach ($matches[1] as $match) {
                $experimentName = $match;
                if (!strpos($experimentName, 'Experiment') !== false && !in_array($experimentName, $processedExperiments)) {
                    array_push($processedExperiments, $match);
                    if (in_array($experimentName, $this->_experimentWhitelist)) {
                        if (!$this->_onlyShowInvalidFiles) {
                            echo "  {$filePath}: Uses whitelisted experiment: {$experimentName}.\n";
                        }
                    } else {
                        $hasProblems = true;
                        echo "- {$filePath}: Uses un-whitelisted experiment: {$experimentName}.\n";
                    }
                }
            }
        }

        return $hasProblems;
    }

    /**
     * Process all *.php files in given path.
     *
     * @return string[] An array with all files that have un-whitelisted experiment usage.
     */
    public function run()
    {
        $filesWithProblems = [];
        foreach ($this->_inspectFolders as $inspectFolder) {
            $directoryIterator = new RecursiveDirectoryIterator($this->_baseDir.'/'.$inspectFolder);
            $recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
            $phpIterator = new RegexIterator($recursiveIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($phpIterator as $filePath => $dummy) {
                $hasProblems = $this->_processFile($filePath);
                if ($hasProblems) {
                    $filesWithProblems[] = $filePath;
                }
            }
        }

        return $filesWithProblems;
    }
}
