<?php

namespace Portavice\LaravelMailLogger\Monolog\Formatter;

use Monolog\Formatter\HtmlFormatter as BaseHtmlFormatter;
use Monolog\Level;
use Monolog\LogRecord;

class HtmlFormatter extends BaseHtmlFormatter
{
    protected function getLevelColor(Level $level): string
    {
        return match ($level) {
            Level::Debug => '#DDDDDD',
            Level::Info => '#28A745',
            Level::Notice => '#17A2B8',
            Level::Warning => '#FFB300',
            Level::Error => '#FF7801',
            Level::Critical => '#DC3545',
            Level::Alert => '#821722',
            Level::Emergency => '#000000',
        };
    }

    protected function getLevelTextColor(Level $level): string
    {
        return match ($level) {
            Level::Debug => '#000000',
            default => '#FFFFFF',
        };
    }

    protected function addRow(string $th, string $td = ' ', bool $escapeTd = true): string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre style="margin: 0; padding: 0;">' . htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8') . '</pre>';
        }

        return <<<HTML
<tr style="padding: 4px; text-align: left;">
    <th style="padding: 4px; text-align: left; vertical-align: top; background: #ddd; color: #000; border-bottom: 1px solid #ccc; font-family: Helvetica Neue, Helvetica, Verdana, sans-serif;" width="100">$th:</th>
    <td style="padding: 4px; text-align: left;vertical-align: top; background: #eee; color: #000; border-bottom: 1px solid #ddd;">$td</td>
</tr>
HTML;
    }

    protected function addTitle(string $title, Level $level): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

        $bgColor = $this->getLevelColor($level);
        $textColor = $this->getLevelTextColor($level);

        return <<<HTML
<h1 style="background: $bgColor; color: $textColor; padding: 5px; font-family: Helvetica Neue, Verdana, sans-serif;" class="monolog-output">
$title
</h1>
HTML;
    }

    public function format(LogRecord $record): string
    {
        $output = $this->addTitle($record->level->getName(), $record->level);
        $output .= '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="monolog-output" style="font-family: Helvetica Neue, Verdana, sans-serif;">';

        $output .= $this->addRow('Time', $this->formatDate($record->datetime));
        $output .= $this->addRow('Channel', $record->channel);
        $output .= $this->addRow('Message', $record->message);

        if (\count($record->context) > 0) {
            $output .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $output .= '<tr><td colspan="2"><h2>Context</h2></td></tr>';

            foreach ($record->context as $key => $value) {
                $output .= $this->addRow((string) $key, $this->convertToString($value));
            }
        }

        if (\count($record->extra) > 0) {
            $output .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $output .= '<tr><td colspan="2"><h2>Extra</h2></td></tr>';

            foreach ($record->extra as $key => $value) {
                $output .= $this->addRow((string) $key, $this->convertToString($value));
            }
        }

        return $output . '</table>';
    }
}
