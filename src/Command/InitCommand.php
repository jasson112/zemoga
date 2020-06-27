<?php


namespace App\Command;

use App\Entity\Portfolio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        // ...
        $this
            // the name of the command (the part after "bin/console")
            ->setName('zem:seed')

            // the short description shown while running "php bin/console list"
            ->setDescription('Seed User Data')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command execute many scripts that help the server to clear all cache on the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Executing <comment>Seed</comment>',
        ]);
        try{
            $portfolio = new Portfolio();
            $portfolio->setTitle("Mr");
            $portfolio->setDescription("Lorem ipsum dolor sit amet, <br>id sollicitudin quam dictum venenatis. Aenean eleifend mauris id, dignissim turpis. In hac habitasse platea dictumst. Class aptent taciti sociosqu ad litora torquent per conubia nostra,");
            $portfolio->setName("Jhon");
            $portfolio->setLastname("Snow");
            $portfolio->setImageUrl("http://localhost:5016/images/jhon.jpg");
            $portfolio->setImagUrl("http://localhost:5016/images/jhonlow.jpg");
            $portfolio->setTwitterUserName('adultswim');
            $this->entityManager->persist($portfolio);
            $this->entityManager->flush();
            $output->writeln([
                'Successfully <comment>Seed</comment>',
            ]);
        }catch (\Exception $e){
            $output->writeln([
                '<error>Damn ! an error' . $e->getMessage() . '</error>',
            ]);
        }

    }
}