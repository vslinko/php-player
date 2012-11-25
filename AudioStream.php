<?php

namespace Rithis\Player;

use React\EventLoop\LoopInterface,
    React\Stream\Stream;

use getID3;

class AudioStream extends Stream
{
    /**
     * @var string
     */
    private $format;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $artist;

    public function __construct($file, LoopInterface $loop)
    {
        $this->parseAudioInfo($file);

        parent::__construct(fopen($file, 'r'), $loop);
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getArtist()
    {
        return $this->artist;
    }

    private function parseAudioInfo($file)
    {
        $info = (new getID3())->analyze($file);

        if (!isset($info['audio']['dataformat'])) {
            throw new \RuntimeException("Can't determine song format");
        }

        if (isset($info['id3v2']['comments']['title'])) {
            $this->title = implode(', ', $info['id3v2']['comments']['title']);
        } else if (isset($info['id3v1']['title'])) {
            $this->title = $info['id3v1']['title'];
        }

        if (isset($info['id3v2']['comments']['artist'])) {
            $this->artist = implode(', ', $info['id3v2']['comments']['artist']);
        } else if (isset($info['id3v1']['artist'])) {
            $this->artist = $info['id3v1']['artist'];
        }

        $this->format = $info['audio']['dataformat'];
    }

    public function __toString()
    {
        if ($this->artist && $this->title) {
            return sprintf('%s - %s', $this->artist, $this->title);
        } else if ($this->title) {
            return $this->title;
        } else {
            return null;
        }
    }
}
