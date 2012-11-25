<?php

namespace Rithis\Player;

use React\EventLoop\LoopInterface,
    React\Stream\Stream;

class SoXPlayerStream extends Stream
{
    /**
     * @var resource
     */
    private $process;

    /**
     * @var string
     */
    private $format;

    public function __construct(LoopInterface $loop, $format = 'mp3')
    {
        $pipes = [];

        $this->process = proc_open("play -qv .5 -t$format -", [['pipe', 'r']], $pipes);

        parent::__construct($pipes[0], $loop);

        $this->format = $format;
    }

    public function handleClose()
    {
        if (is_resource($this->process)) {
            posix_kill(proc_get_status($this->process)['pid'], SIGKILL);
        }

        parent::handleClose();
    }

    public function getFormat()
    {
        return $this->format;
    }
}
