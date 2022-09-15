<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Helpers\Git;
use DragonCode\LaravelActions\Notifications\Basic;
use DragonCode\LaravelActions\Notifications\Beautiful;
use DragonCode\LaravelActions\Notifications\Notification;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Values\Options;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Processor
{
    use Artisan;

    protected ?Notification $notification = null;

    abstract public function handle(): void;

    public function __construct(
        protected Options          $options,
        protected InputInterface   $input,
        protected OutputInterface  $output,
        protected Config           $config,
        protected ActionRepository $repository,
        protected Git              $git
    ) {
        $this->bootNotification($this->output);
    }

    protected function bootNotification(OutputInterface $output): Notification
    {
        $this->notification = class_exists(Factory::class)
            ? new Beautiful($output)
            : new Basic($output);
    }
}
