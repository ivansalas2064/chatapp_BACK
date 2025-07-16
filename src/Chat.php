<?php

namespace App;

class Chat
{
    private AI_Service_Interface $aiService;

    public function __construct(AI_Service_Interface $aiService) 
    {
        $this->aiService = $aiService;
    }

    public function getResponse(string $input): string
    {
        return $this->aiService->getResponse($input);
    }

    // Opcional: para probar por consola
    public function start()
    {
        $this->welcome();

        while ($input = $this->prompt()) {
            if ($this->exit($input)) break;
            $response = $this->getResponse($input);
            $this->output($response);
        }
    }

    private function welcome()
    {
        echo "¿En qué puedo ayudarte? Estoy para servirte 😄" . PHP_EOL;
    }

    private function prompt()
    {
        return readline("> ");
    }

    private function exit($input)
    {
        return trim($input) === "exit"; 
    }

    private function output($response)
    {
        echo $response . PHP_EOL;
    }
}
