<?php

namespace FFPI\FfpiFirmwareList\Controller;

use FFPI\FfpiFirmwareList\Utility\FilenameUtility;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Simple Firmware List without Router Management
 *
 * Class FirmwareListController
 * @package FFPI\FfpiFirmwareList\Controller
 */
class FirmwareListController extends ActionController
{
    /** @var Folder */
    protected $folder;

    protected $blacklistedPathSegments = [];

    protected function initializeAction()
    {
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $this->folder = $resourceFactory->getFolderObjectFromCombinedIdentifier($this->settings['folder']);
        $this->blacklistedPathSegments = array_map('trim', explode(',', $this->settings['blacklistet_path_segments']));
    }

    public function listAction()
    {
        $files = $this->folder->getFiles(0, 0, Folder::FILTER_MODE_USE_OWN_AND_STORAGE_FILTERS, true, 'name');
        $firmwareList = [];

        // Factory Files
        foreach ($files as $file) {
            if (FilenameUtility::stringContainsArray($file->getIdentifier(), $this->blacklistedPathSegments)) {
                continue;
            }

            $firmwareParts = FilenameUtility::getFirmwareParts($file->getName());
            $unifiedRouterIdentifier = FilenameUtility::createUnifiedRouterIdentifier($firmwareParts);
            if ($firmwareParts['sysupgrade']) {
                $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['firmwareParts'] = $firmwareParts;
                $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file'] = $file;
                $firmwareList[$unifiedRouterIdentifier]['router']['router'] = $firmwareParts['router'];
                $firmwareList[$unifiedRouterIdentifier]['router']['routerVersion'] = $firmwareParts['routerVersion'];
            } else {
                $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['firmwareParts'] = $firmwareParts;
                $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file'] = $file;
                $firmwareList[$unifiedRouterIdentifier]['router']['router'] = $firmwareParts['router'];
                $firmwareList[$unifiedRouterIdentifier]['router']['routerVersion'] = $firmwareParts['routerVersion'];
            }
        }

        ksort($firmwareList,  SORT_NATURAL);

        $this->view->assign('settings', $this->settings);
        $this->view->assign('firmwareList', $firmwareList);
    }
}