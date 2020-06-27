<?php


namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        // ...
        $this
            // the name of the command (the part after "bin/console")
            ->setName('impulse:performance')

            // the short description shown while running "php bin/console list"
            ->setDescription('Execute performance scripts for APCU and OPCache')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command execute many scripts that help the server to clear all cache on the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Executing <comment>apcu_clear_cache</comment>',
        ]);
        try{
            apcu_clear_cache();
            $output->writeln([
                'Command executed with status: <info>OK</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }

        $output->writeln([
            'Executing <comment>opcache_reset</comment>',
        ]);

        try{
            opcache_reset();
            $output->writeln([
                'Command executed with status: <info>OK</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }

        $output->writeln([
            'Executing <comment>service php-fmp restart</comment>',
        ]);

        try{
            $out = shell_exec('sudo service php-fpm restart');
            $output->writeln([
                '<info>' . $out . '</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }
        //

        $output->writeln([
            'Executing service <comment>sudo php /var/cachetool.phar apcu:cache:clear --fcgi=/var/run/php-fpm/www.sock</comment>',
        ]);

        try{
            $out = shell_exec('sudo php /var/cachetool.phar apcu:cache:clear --fcgi=/var/run/php-fpm/www.sock');
            $output->writeln([
                '<info>' . $out . '</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }

        $output->writeln([
            'Executing service <comment>sudo php -d memory_limit=512M /var/www/vhosts/impulsetravel.co/web/bin/console cache:warmup --env=prod --no-debug</comment>',
        ]);

        try{
            $out = shell_exec('sudo php -d memory_limit=512M /var/www/vhosts/impulsetravel.co/web/bin/console cache:warmup --env=prod --no-debug');
            $output->writeln([
                '<info>' . $out . '</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }

        $output->writeln([
            'Executing service <comment>sudo service httpd restart</comment>',
        ]);

        try{
            $out = shell_exec('sudo service httpd restart');
            $output->writeln([
                '<info>' . $out . '</info>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error</error>',
            ]);
        }

    }
}