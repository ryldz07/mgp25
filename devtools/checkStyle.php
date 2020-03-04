<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

/*
 * This tool checks all PHP files for additional codestyle compliance.
 *
 * It is meant to be used together with PHP-CS-Fixer to look for additional
 * problems that aren't handled by that project (yet).
 *
 * Currently it checks the following style rules:
 *
 * - Variable and function underscore prefix must match their visibility (public
 *   members never have a prefix, but private/protected always have a prefix).
 *
 * - Function parameter formatting. In case of incorrect style, it outputs code
 *   to show how those functions should be formatted.
 *
 * Tip: Execute this script with ANY extra argument, to hide all valid files and
 * focus only on the files with problems.
 * For example: "php devtools/checkStyle.php x".
 */

$onlyShowInvalidFiles = isset($argv[1]); // Hide valid files if argv[1] is set.
$styleChecker = new styleChecker(
    __DIR__.'/../',
    [ // List of all subfolders to inspect.
        'devtools',
        'examples',
        'src',
        'tests',
    ],
    $onlyShowInvalidFiles
);
$badFiles = $styleChecker->run();
if (!empty($badFiles)) {
    // Exit with non-zero code to signal that there are problems.
    exit(1);
}

class styleChecker
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
    }

    /**
     * Process single file.
     *
     * @param string $filePath
     *
     * @return bool TRUE if the file has codestyle problems, otherwise FALSE.
     */
    private function _processFile(
        $filePath)
    {
        $hasProblems = false;
        $hasVisibilityProblems = false;
        $fileName = basename($filePath);
        $inputLines = @file($filePath);
        $outputLines = [];

        if ($inputLines === false) {
            // We were unable to read the input file. Ignore if broken symlink.
            if (is_link($filePath)) {
                return false; // File is okay, since the symlink is invalid.
            } else {
                echo "- {$filePath}: UNABLE TO READ FILE!".PHP_EOL;

                return true; // File has problems...
            }
        }

        foreach ($inputLines as $line) {
            // Function arguments on separate lines.
            if (preg_match('/^(.*?(?:(?:final|static)\s+)*(?:public|private|protected)(?:\s+(?:final|static))*\s+function\s+.+?)\((.+)\)(.*)$/', $line, $matches)) {
                $hasProblems = true;

                $funcstart = $matches[1];
                $params = $matches[2];
                $trail = $matches[3];
                $params = explode(', ', $params);

                $outputLines[] = $funcstart.'('.PHP_EOL;
                for ($i = 0, $len = count($params); $i < $len; ++$i) {
                    $newline = '        '.$params[$i];
                    if ($i == ($len - 1)) {
                        $newline .= ')'.PHP_EOL;
                    } else {
                        $newline .= ','.PHP_EOL;
                    }
                    $outputLines[] = $newline;
                }
                // } else {
                //     $outputLines[] = $line;
            }

            // Appropriate public, private and protected member prefixes.
            if (preg_match('/^\s*(?:(?:final|static)\s+)*(public|private|protected)(?:\s+(?:final|static))*\s+(function|\$)\s*&?([^;\(\s]+)/', $line, $matches)) {
                $visibility = &$matches[1]; // public, private, protected
                $type = &$matches[2]; // $, function
                $name = &$matches[3]; // Member name

                if ($visibility == 'public') {
                    if ($name[0] == '_' && (
                        $name != '__construct'
                        && $name != '__destruct'
                        && $name != '__call'
                        && $name != '__get'
                        && $name != '__set'
                        && $name != '__isset'
                        && $name != '__unset'
                        && $name != '__invoke'
                        && $name != '__toString'
                    )) {
                        $hasProblems = true;
                        $hasVisibilityProblems = true;
                        echo "- {$filePath}: BAD PUBLIC NAME:".trim($matches[0]).PHP_EOL;
                    }
                } else { // private, protected
                    if ($name[0] != '_') {
                        $hasProblems = true;
                        $hasVisibilityProblems = true;
                        echo "- {$filePath}: BAD PRIVATE/PROTECTED NAME:".trim($matches[0]).PHP_EOL;
                    }
                }
            }
        }

        $newFile = implode('', $outputLines);
        if (!$hasProblems) {
            if (!$this->_onlyShowInvalidFiles) {
                echo "  {$filePath}: Already formatted correctly.\n";
            }
        } elseif (!$hasVisibilityProblems) {
            // Has problems, but no visibility problems. Output fixed file.
            echo "- {$filePath}: Has function parameter problems:\n";
            echo $newFile;
        } else {
            echo "- {$filePath}: Had member visibility problems.\n";
        }

        return $hasProblems;
    }

    /**
     * Process all *.php files in given path.
     *
     * @return string[] An array with all files that have codestyle problems.
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
