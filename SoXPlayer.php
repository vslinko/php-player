<?php

namespace Rithis\Player;

use Evenement\EventEmitter;

class SoXPlayer extends EventEmitter implements PlayerInterface
{
    /**
     * @var \Rithis\Player\SoXPlayerStream
     */
    private $sox;

    /**
     * @var \Rithis\Player\AudioStream|null
     */
    private $nowPlaying;

    public function __construct(SoXPlayerStream $sox)
    {
        $this->sox = $sox;
    }

    public function play(AudioStream $audio)
    {
        if ($audio->getFormat() != $this->getFormat()) {
            throw new \RuntimeException("Unsupported song format");
        }

        $play = function () use ($audio) {
            $audio->pipe($this->sox, ['end' => false]);

            $audio->on('end', function ($audio) {
                $this->emit('end', [$audio]);
            });
        };

        if ($this->nowPlaying) {
            $this->nowPlaying->removeAllListeners();
            $this->nowPlaying->on('close', $play);
            $this->stop();
        } else {
            $play();
        }

        $this->nowPlaying = $audio;
    }

    public function getFormat()
    {
        return $this->sox->getFormat();
    }
}
