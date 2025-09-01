# Console Commands

Leopard Skeleton supports console commands through the `symfony/console` component. This allows you to perform various tasks such as clearing the cache, running tests, database migrations, and more.  
Command classes inherit from `Symfony\Component\Console\Command\Command` for simple tasks, or from `App\Commands\BaseCommand` for deeper integration with the project, where you can use containers and other framework functionality.

## Main Commands

### List of Available Commands
To view all available commands, run:
```bash
./console list
```

### Executing a Command
```bash
./console app:command
```

### Creating a New Command
The following command generates a new class for a console command:
```bash
./console app:generate-command app:<command_name>
```
