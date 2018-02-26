<?php

namespace App\Inspections;

class InvalidKeywords
{
    protected $invalidKeywords = [
        'Yahoo Customer Support',
    ];

    /**
     * @param $body
     * @throws \Exception
     */
    public function detect($body)
    {
        foreach ($this->invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new \Exception('Spam detected in your input.');
            }
        }
    }
}