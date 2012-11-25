# PHP audio player

## Installation

Run in your project root:

```composer require rithis/php-player:@dev```

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$player = new SoXPlayer(new SoXPlayerStream($loop, 'mp3'));

$audio = new Audio(__DIR__ . '/sound.mp3', $loop);
$player->play($audio);

$loop->run();
```
