<?php

namespace Jump;

class Greeting {
    private array $greetings;

    public function __construct() {
        $this->greetings = [
            03 => 'morning',
            12 => 'afternoon',
            16 => 'evening',
            19 => 'night'
        ];
    }

    public function get_greeting(): string {
        krsort($this->greetings);
        foreach ($this->greetings as $key => $value) {
            if (date('H', time()) >= $key) {
                return $value;
            }
        }
    }
}