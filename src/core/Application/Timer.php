<?php

namespace Bleidd\Application;

final class Timer
{

    const BOOT = 'boot';
    const BEFORE_MIDDLEWARES = 'before_middlewares';
    const BEFORE_CONTROLLER = 'before_controller';
    const AFTER_CONTROLLER = 'after_controller';
    const SHUTDOWN = 'shutdown';

    /** @var array */
    protected static $times = [];

    /**
     * @param float $time1
     * @param float $time2
     * @return string
     */
    private static function formatTimeDifference(float $time1, float $time2): string
    {
        $diff = $time1 - $time2;

        if ($diff < 0.1) {
            $diff = number_format($diff * 1000, 1, '.', '') . 'ms';
        } else if ($diff >= 0.1 && $diff < 5) {
            $diff = number_format($diff, 2, '.', '') . 's';
        } else if ($diff < 15) {
            $diff = number_format($diff, 1, '.', '') . 's';
        } else {
            $diff = number_format($diff, 0, '.', '') . 's';
        }

        return $diff;
    }

    /**
     * @param string   $key
     * @param int|null $timestamp
     */
    public static function logTime(string $key, int $timestamp = null)
    {
        self::$times[$key] = $timestamp ?? microtime(true);
    }

    /**
     * @return array
     */
    public static function getTimes(): array
    {
        return self::$times;
    }

    /**
     * @return array
     */
    public static function getWellFormattedTimes(): array
    {
        if (empty(self::$times)) {
            return [];
        }

        $index = 0;
        $first = true;
        $formatted = [];
        $previous = null;
        $firstMicrotime = reset(self::$times);

        foreach (self::$times as $key => $microtime) {
            $data = [
                'time' => (new \DateTime())
                    ->setTimestamp($microtime)
                    ->format('Y-m-d H:i:s')
            ];

            if (!$first) {
                $data['time_since_start'] = self::formatTimeDifference((float) $microtime, (float) $firstMicrotime);
            }
            if ($previous && ++$index >= 2) {
                $data['time_since_previous'] = self::formatTimeDifference((float) $microtime, (float) $previous);
            }
            
            $formatted[$key] = $data;

            $first = false;
            $previous = $microtime;
        }

        return $formatted;
    }

    /**
     * @param string $key
     * @param bool   $asTimestamp
     * @return string|int|null
     */
    public static function getTime(string $key, bool $asTimestamp = false)
    {
        $timestamp = self::$times[$key] ?? null;

        if (!$timestamp) {
            return null;
        }

        return $asTimestamp ? $timestamp : (new \DateTime())
            ->setTimestamp($timestamp / 1000)
            ->format('Y-m-d H:i:s');
    }

    /**
     * @param bool $asTimestamp
     * @return string|int|null
     */
    public static function timeFromStart(bool $asTimestamp = false)
    {
        if (!isset(self::$times[self::BOOT])) {
            return null;
        }

        $microtime = microtime(true);
        $bootTime = self::$times[self::BOOT];

        return $asTimestamp ? $microtime - $bootTime : self::formatTimeDifference($microtime, $bootTime);
    }

}
