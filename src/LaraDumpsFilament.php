<?php

declare(strict_types = 1);

namespace LaraDumpsFilament;

use LaraDumps\LaraDumps\LaraDumps;
use LaraDumps\LaraDumpsCore\Actions\Dumper;
use LaraDumps\LaraDumpsCore\Payloads\DumpPayload;
use LaraDumpsFilament\Debuggers\BaseDebug;

class LaraDumpsFilament
{
    public static function makeFrame(): array
    {
        $frame = array_values(
            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        )[4] ?? [];

        return [
            'file' => data_get($frame, 'file'),
            'line' => data_get($frame, 'line'),
        ];
    }

    public static function dump(BaseDebug $debug, string $label, string $color): LaraDumps
    {
        $laradumps = new LaraDumps();

        [$pre, $id] = Dumper::dump($debug);

        $payload = new DumpPayload($pre, $debug, variableType: gettype($debug), screen: 'Filament');
        $payload->setDumpId($id);
        $payload->setFrame(self::makeFrame());
        $payload->toScreen();

        $laradumps->send($payload, withFrame: false);

        $laradumps->label($label);
        $laradumps->color($color);

        return $laradumps;
    }
}
