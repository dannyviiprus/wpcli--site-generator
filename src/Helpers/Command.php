<?php
namespace Dvpz\Helpers;

use mikehaertl\shellcommand\Command as ShellCommand;

class Command {
    /**
     * @param array $cmds
     *
     * @return array
     */
    public function cmdExcute(array $cmds) {
        if (sizeof($cmds) <= 0) {
            exit;
        }

        $output = [];

        foreach ($cmds as $cmd) {
            if (!$cmd instanceof \mikehaertl\shellcommand\Command) {
                continue;
            }

            //count excute time
            $start = microtime(true);

            if ($cmd->execute()) {
                $output[] = [
                    'cmd' => $cmd->getExecCommand(),
                    'execTime' => (microtime(true) - $start) . 's',
                    'output' => $cmd->getOutput(),
                ];
            } else {
                $output[] = [
                    'cmd' => $cmd->getExecCommand(),
                    'execTime' => (microtime(true) - $start) . 's',
                    'output' => $cmd->getError(),
                ];
            }
        }

        return $output;
    }

    /**
     * @param $cmd
     * @param array $args
     *
     * @return \mikehaertl\shellcommand\Command
     */
    public function getCommand($cmd, $args = [])
    {
        $command = new ShellCommand($cmd);

        if (sizeof($args) > 0) {
            foreach ($args as $key => $value) {
                $command->addArg('--' . $key . '=', $value);
            }
        }

        return $command;
    }

    /**
     * @param $numberOfsite
     * @param $rootDir
     */
    public function cleanUp($rootDir)
    {
        $rootPath = ABSPATH . $rootDir;

        if (!file_exists($rootPath)) {
            return [];
        }

        $siteToDelete = array_filter(scandir($rootPath), function ($item) use ($rootPath) {
            return is_dir($rootPath . '/' . $item) && !in_array($item, ['.', '..']);
        });

        $wpCli = $this->getWpCliPath();
        $output = [];

        foreach ($siteToDelete as $site) {
            $dbCleanCommand = $this->getCommand("$wpCli db drop --yes", ['path' => "$rootPath/$site"]);
            $output[] =  $this->cmdExcute([$dbCleanCommand]);
        }

        (new Common())->deleteDirectory($rootPath);

        return $output;
    }

    public function getWpCliPath()
    {
        return CSG_PLUGIN_DIR . 'vendor/bin/wp';
    }
}
