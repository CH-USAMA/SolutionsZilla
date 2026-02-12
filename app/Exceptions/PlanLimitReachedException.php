<?php

namespace App\Exceptions;

use Exception;

class PlanLimitReachedException extends Exception
{
    protected $feature;

    public function __construct(string $feature, string $message = "Plan limit reached.")
    {
        parent::__construct($message);
        $this->feature = $feature;
    }

    public function getFeature(): string
    {
        return $this->feature;
    }
}
