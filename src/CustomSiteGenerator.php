<?php

namespace Dvpz;

use Dvpz\Helpers\Command;
use Dvpz\Helpers\Common;
use Formr\Formr;

class CustomSiteGenerator {
    private $commonHelper;
    private $commandHelper;

    public function __construct()
    {
        $this->commonHelper = new Common();
        $this->commandHelper = new Command();
    }

    public function getForm()
    {
        return new Formr('bootstrap', 'nowrap');
    }

    public function getGeneratedSites()
    {
        $scanDir = ABSPATH . $this->commonHelper->getRootFolderName();

        if (!file_exists($scanDir)) {
            return [];
        }

        return array_filter(scandir($scanDir), function($item) use ($scanDir) {
            return is_dir($scanDir . '/' . $item) && !in_array($item, array('.', '..'));
        });
    }

    public function cleanSite()
    {
        return $this->commandHelper->cleanUp($this->commonHelper->getRootFolderName());
    }

    public function generateSite($form)
    {
        $numberOfSite = $form->post('numberOfSite', 'Number Of Site', 'gte[1]|lte[5]');
        $cleanSite = isset($_POST['clean']);

        if ($cleanSite) {
            return $this->cleanSite();
        }

        if (is_array($form->errors()) && sizeof($form->errors()) > 0) {
            return $form->errors();
        }

        $rootDir = $this->commonHelper->getRootFolderName();
        $sitePrefix = $this->commonHelper->getSubdirPrefix();
        $siteUrlPlaceholder = $this->commonHelper->getUrlPlaceHolder();
        $tempPath = ABSPATH . 'tmp';
        $version = '6.1.1';
        $locale = 'en_US';
        $wp = $this->commandHelper->getWpCliPath();

        if (!file_exists(ABSPATH . $rootDir)) {
            mkdir(ABSPATH . $rootDir);
        }

        $this->commonHelper->deleteDirectory($tempPath);

        mkdir($tempPath);

        $result = [];
        $downloaded = file_exists("{$tempPath}/wordpress-{$version}-{$locale}.tar.gz");
        $downloadCommand = [];

        for ($i = 1; $i <= $numberOfSite; $i++) {
            $sitename = $sitePrefix . time();
            $sitePath = ABSPATH . $rootDir . "/$sitename";

            mkdir($sitePath);

            if (!$downloaded) {
                $downloadCommand = $this->commandHelper->getCommand("$wp core download", [
                    'version' => $version,
                    'path' => $tempPath,
                    'extract' => false
                ]);
            }

            $extractCommand = $this->commandHelper->getCommand("tar -xvf {$tempPath}/wordpress-{$version}-{$locale}.tar.gz --strip-components=1 -C {$sitePath} wordpress/");

            $configCommand = $this->commandHelper->getCommand("$wp config create --skip-check", [
                'dbname' => "{$sitename}_db",
                'path' => $sitePath,
            ]);

            $siteUrl = str_replace('{PLACE_HOLDER}', $sitename, $siteUrlPlaceholder);

            /* $configUpdateCommand = $this->commandHelper->getCommand('$wp config set SITE_URL $siteUrl'); */

            $dbcommand = $this->commandHelper->getCommand("$wp db create", ['path' => $sitePath]);

            $installCommand = $this->commandHelper->getCommand("$wp core install --skip-email --skip-themes --skip-plugins --skip-packages", [
                'url' => $siteUrl,
                'title' => $sitename,
                'admin_email' => "$sitename@localhost.com",
                'path' => $sitePath
            ]);

            if (!$downloaded) {
                $result[] = $this->commandHelper->cmdExcute([$downloadCommand]);
                $downloaded = true;
            }

            $result[] = $this->commandHelper->cmdExcute([
                $extractCommand,
                $configCommand,
                $dbcommand,
                $installCommand,
            ]);
        }

        $this->commonHelper->deleteDirectory($tempPath);

        return $result;
    }

}
