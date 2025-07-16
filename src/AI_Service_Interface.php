<?php

namespace App;

interface AI_Service_Interface
{
    public function getResponse(string $input): string;
}