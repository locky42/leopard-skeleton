<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCommand
 *
 * This class extends the base Command class and is responsible for handling
 * the generation of specific resources or functionality within the application.
 *
 * @package Commands
 */
class GenerateCommand extends Command
{
    /**
     * The default name for the command.
     *
     * This property defines the name used to invoke the command
     * from the command-line interface. It is typically used to
     * register the command within the application.
     *
     * @var string
     */
    protected static $defaultName = 'app:generate-command';

    /**
     * Constructor for the GenerateCommand class.
     *
     * Initializes the command and sets up any necessary dependencies or configurations.
     */
    public function __construct()
    {
        parent::__construct(self::$defaultName); // Explicitly set the command name
    }

    /**
     * Configures the command by defining its name, description, and other settings.
     *
     * This method is called automatically by the Symfony Console component
     * to set up the command's metadata and options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generates a new console command.')
            ->setHelp('This command allows you to generate a new console command file.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the command (e.g., "app:my-command").');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input  The input interface instance.
     * @param OutputInterface $output The output interface instance.
     * 
     * @return int The command exit code. Returns 0 on success, or an error code on failure.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        // Ensure the name follows the "namespace:command" format
        if (!preg_match('/^[a-z]+:[a-z0-9\-]+$/', $name)) {
            $output->writeln('<error>Invalid command name. Use the format "namespace:command" (e.g., "app:my-command").</error>');
            return Command::FAILURE;
        }

        // Convert the command name to a valid class name
        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', explode(':', $name)[1]))) . 'Command';
        $filePath = __DIR__ . "/$className.php";

        if (file_exists($filePath)) {
            $output->writeln("<error>The command file already exists: $filePath</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class $className extends BaseCommand
{
    protected static \$defaultName = '$name';

    public function __construct()
    {
        parent::__construct(self::\$defaultName); // Explicitly set the command name
    }

    protected function configure(): void
    {
        \$this
            ->setDescription('Description for $name.')
            ->setHelp('Help for $name.');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output): int
    {
        \$output->writeln('Command $name executed!');
        return self::SUCCESS;
    }
}
PHP;

        file_put_contents($filePath, $template);
        $output->writeln("<info>Command file created: $filePath</info>");

        return Command::SUCCESS;
    }
}
