<?php

namespace App\Console;

use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Illuminate\Database\Capsule\Manager as Capsule;

class MigrationCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this->setName('db:migrate');
        $this->setDescription('Run the database migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Running database migrations!</info>");

        Capsule::schema()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $output->writeln("<info>Database has been migrated!</info>");

        return 0;
    }

}
