<?php

namespace Configen\Command;

use Cilex\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigenCommand extends Command
{

    const SYMFONY_VARS = '{{symfony_vars}}';

    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate vhost config files from a Symfony2 parameters.yml')
            ->addArgument('path', InputArgument::REQUIRED, 'parameters.yml path')
            ->addArgument('type', InputArgument::OPTIONAL, '[ vhost, envvars ]')
            ->addOption('in-template', null, InputOption::VALUE_OPTIONAL, 'If set, the task will replace '.self::SYMFONY_VARS.' in the template');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('path');
        if ( ! file_exists($filePath) || ! is_readable($filePath))
        {
            $output->writeln(sprintf('%s does not exist or is not readable', $filePath));

            return;
        }

        // transform
        $originalParameters = Yaml::parse(file_get_contents($filePath));
        $originalParameters = $originalParameters['parameters'];
        $parameters = [];
        foreach($originalParameters as $param => $value)
        {
            $param = 'SYMFONY__'.strtoupper(str_replace('.', '__', $param));

            $parameters[$param] = $value;
        }

        switch($input->getArgument('type'))
        {
            case 'envvars':
                $file = $this->loopParameters($parameters, function($key, $val){
                    return 'export '.$key.'='.$val."\n";
                });
                break;

            case 'vhost':
            default:
                $file = $this->loopParameters($parameters, function($key, $val){
                        return 'SetEnv  '.$key.' '.$val."\n";
                    });
        }

        $template = $input->getOption('in-template');
        if ($template)
        {
            if ( ! file_exists($template) || ! is_readable($template))
            {
                $output->writeln(sprintf('%s does not exist or is not readable', $template));

                return;
            }

            $file = str_replace(self::SYMFONY_VARS, $file, file_get_contents($template));
        }

        $output->write($file);
    }


    /**
     * @param array $parameters
     * @param \Closure $transform
     *
     * @return string
     */
    private function loopParameters(array $parameters = [], \Closure $transform)
    {
        $file = '';
        foreach($parameters as $param => $value)
        {
            $file .= $transform($param, $value);
        }

        return $file;
    }
}
