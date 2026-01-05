<?php

declare (strict_types=1);
namespace VendorPatches202601\Entropy\Console\Output;

/**
 * @api used in many ways
 */
final class OutputPrinter
{
    /**
     * @readonly
     * @var bool
     */
    private $useColors;
    /**
     * @readonly
     * @var bool
     */
    private $isSilent;
    public function __construct(?bool $useColors = null)
    {
        $this->useColors = $useColors ?? $this->isTty();
        // avoid printing to stdout during unit tests
        $this->isSilent = \defined('PHPUNIT_COMPOSER_INSTALL');
    }
    /**
     * Handle color background and foreground tags in the text
     * e.g. <fg=green>text</>, <bg=red>text</>
     */
    public function writeln(string $text, int $newlineCount = 0) : void
    {
        if ($this->isSilent) {
            return;
        }
        $coloredText = $this->colorize($text);
        \fwrite(\STDOUT, $coloredText . \PHP_EOL);
        if ($newlineCount !== 0) {
            $this->newline($newlineCount);
        }
    }
    public function yellow(string $text) : void
    {
        $this->writeln($this->color($text, 'yellow'));
    }
    public function green(string $string) : void
    {
        $this->writeln($this->color($string, 'green'));
    }
    public function orangeBackground(string $text) : void
    {
        $this->writeln($this->background($text, 'orange'));
    }
    public function greenBackground(string $text) : void
    {
        $this->writeln($this->background($text, 'green'));
    }
    public function redBackground(string $text) : void
    {
        $this->writeln($this->background($text, 'red'));
    }
    public function newline(int $count = 1) : void
    {
        if ($this->isSilent) {
            return;
        }
        \fwrite(\STDOUT, \str_repeat(\PHP_EOL, $count));
    }
    private function color(string $text, string $type) : string
    {
        if (!$this->useColors) {
            return $text;
        }
        switch ($type) {
            case 'green':
                return "\x1b[32m{$text}\x1b[0m";
            case 'yellow':
                return "\x1b[33m{$text}\x1b[0m";
            case 'red':
                return "\x1b[31m{$text}\x1b[0m";
            case 'cyan':
                return "\x1b[36m{$text}\x1b[0m";
            default:
                return $text;
        }
    }
    private function background(string $text, string $type) : string
    {
        $text = $this->pad($text);
        if (!$this->useColors) {
            return $text;
        }
        switch ($type) {
            case 'green':
                return "\x1b[42;30m{$text}\x1b[0m";
            case 'yellow':
            case 'orange':
                return "\x1b[43;30m{$text}\x1b[0m";
            case 'red':
                return "\x1b[41;30m{$text}\x1b[0m";
            default:
                return $text;
        }
    }
    private function pad(string $text) : string
    {
        return ' ' . $text . ' ';
    }
    private function isTty() : bool
    {
        if (\function_exists('stream_isatty')) {
            return @\stream_isatty(\STDOUT);
        }
        // Fallback: respect NO_COLOR if present
        return \getenv('NO_COLOR') === \false;
    }
    private function colorize(string $text) : string
    {
        $matches = [];
        // foreground colors: <fg=green>text</>
        if (\preg_match_all('~<fg=(green|yellow|red|cyan)>(.*?)</>~su', $text, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = \str_replace($match[0], $this->color($match[2], $match[1]), $text);
            }
        }
        // background colors: <bg=green>text</>
        if (\preg_match_all('/<bg=(green|yellow|red|cyan|orange)>(.*?)<\\/>/', $text, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = \str_replace($match[0], $this->background($match[2], $match[1]), $text);
            }
        }
        return $text;
    }
}
