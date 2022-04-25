<?php

declare(strict_types=1);

namespace CommissionGenerator\App;

use CommissionGenerator\App\Config\Configuration;
use CommissionGenerator\App\Config\DotenvConfigLoader;
use CommissionGenerator\Command\CalculateCommissionsCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application.
 */
class Application extends SymfonyApplication
{
    /**
     * @return int
     *
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $directories = [__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR];
        $locator = new FileLocator($directories);

        $loader = new DotenvConfigLoader($locator);
        $configValues = $loader->load($locator->locate('.env'));

        Configuration::getInstance()->load($configValues);

        $this->add(new CalculateCommissionsCommand());

        return parent::run();
    }
}
