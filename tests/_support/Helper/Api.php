<?php

namespace Helper;

use App\Service\CustomersImporter;
use Codeception\Module\Phiremock;

class Api extends \Codeception\Module
{
    /**
     * Get Phiremock module
     *
     * @return Phiremock
     * @throws \Codeception\Exception\ModuleException
     */
    public function getPhiremock(): Phiremock
    {
        return $this->getModule('Phiremock');
    }

    /**
     * Get CustomersImporter service
     *
     * @return CustomersImporter
     * @throws \Codeception\Exception\ConfigurationException
     * @throws \Codeception\Exception\ModuleException
     */
    public function getImporter(): CustomersImporter
    {
        $url = $this->getPhiremockBaseUrl();

        $importer = $this->getModule('Symfony')
            ->_getContainer()
            ->get(CustomersImporter::class);

        if ($url) {
            $importer->setBaseUrl($url);
        }

        return $importer;
    }

    /**
     * Parse api suit config file and return Phiremock base url
     *
     * @return string|null
     * @throws \Codeception\Exception\ConfigurationException
     */
    public function getPhiremockBaseUrl(): ?string
    {
        $config = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings('api', $config);

        $modules = $apiSettings['modules']['enabled'];
        if ($modules) {
            foreach ($modules as $module) {
                if (is_array($module) && key($module) == 'Phiremock') {
                    $phConfig = current($module);

                    return sprintf('http%s://%s:%d',
                        $phConfig['secure'] ? 's' : '',
                        $phConfig['host'],
                        $phConfig['port']
                    );
                }
            }
        }

        return null;
    }

}
