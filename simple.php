<?php

interface Logger
{
    public function log($message);
}

class OutputLogger implements Logger
{
    public function log($message)
    {
        echo $message . "\n";
    }
}

abstract class LoggerDecorator implements Logger
{
    /**
     * @var Logger
     */
    private $realLogger;

    public function __construct(Logger $realLogger)
    {
        $this->realLogger = $realLogger;
    }

    public function log($message)
    {
        $this->realLogger->log($message);
    }
}

class CurrentTimeLogger extends LoggerDecorator
{
    public function log($message)
    {
        $currentTime = (new \DateTime())->format('Y-m-d H:i:s');
        $message = '[' . $currentTime . ']: ' . $message;
        parent::log($message);
    }
}

class JsonFormattedLogger extends LoggerDecorator
{
    public function log($message)
    {
        $output = [
            'message' => $message
        ];
        parent::log(json_encode($output, JSON_PRETTY_PRINT));
    }
}

function logHello(Logger $logger)
{
    $logger->log("Hello World!");
}

logHello(new OutputLogger());

logHello(
    new CurrentTimeLogger(
        new OutputLogger()
    )
);

logHello(
    new CurrentTimeLogger(
        new JsonFormattedLogger(
            new OutputLogger()
        )
    )
);
