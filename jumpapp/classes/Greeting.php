<?php

namespace Jump;

/**
 * Choose the appropriate greeting word for the time of day, so if the current
 * hour is 04:00 this will return "morning" etc.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Greeting {
    private array $greetings;

    public function __construct() {
        $this->greetings = [
            0 => 'morning',
            12 => 'afternoon',
            16 => 'evening',
            19 => 'night'
        ];
    }

    /**
     * Select the appropriate greeting word based on the time of day and
     * what has been defined in $this->greetings.
     *
     * @return string The greeting word selected.
     */
    public function get_greeting(): string {
        krsort($this->greetings);
        foreach ($this->greetings as $key => $value) {
            if (date('H', time()) >= $key) {
                return $value;
            }
        }
    }
}