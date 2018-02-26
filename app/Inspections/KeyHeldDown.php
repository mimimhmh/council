<?php

namespace app\Inspections;

class KeyHeldDown
{
    public function detect($body)
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception('Spam detected in your input.');
        }
    }
}