<?php

namespace App\Command;

use App\Apis\Salesforce\SalesforceApi;
use App\Services\Mapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetSchemaCommand extends Command
{
    protected static $defaultName = 'schema';
    private $connection;
    private $em;
    private $api;
    public function __construct(string $name = null, Connection $connection, EntityManagerInterface $em, SalesforceApi $api)
    {
        parent::__construct($name);
        $this->connection = $connection;
        $this->em = $em;
        $this->api = $api;
    }
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('object', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $node = (object)Mapper::get()[$input->getArgument('object')] ?? null;
        if($node == null)
            throw new \Exception('No map for that object');

        $schema = $this->api->describe($node->salesforce_name);
        $schema = $schema->getBody();
        $schema = json_decode($schema);

        $fields = [];
        foreach($schema->fields as $field)
        {
            $fields[] = [
                'name'  =>  $field->name,
                'label' =>  $field->label,
                'type'  =>  $field->type,
                'updateable' => $field->updateable
            ];
        }
        file_put_contents('src/schema/' . $node->salesforce_name . '.json', json_encode($fields));
        return 0;
    }
}
