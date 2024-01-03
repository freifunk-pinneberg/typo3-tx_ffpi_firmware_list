<?php

namespace FFPI\FfpiFirmwareList\Controller;

use FFPI\FfpiFirmwareList\Utility\FilenameUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Page\AssetCollector;
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
    /** @var Folder $folder */
    protected $folder;

    /** @var array $blacklistedPathSegments */
    protected $blacklistedPathSegments = [];

    protected function initializeAction(): void
    {
        $this->folder = explode(',',$this->settings['folder']);
        $this->blacklistedPathSegments = array_map('trim', explode(',', $this->settings['blacklistet_path_segments']));
    }

    public function listAction(): void
    {
        $assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
        $assetCollector->addStyleSheet('FirmwareList', 'EXT:ffpi_firmware_list/Resources/Public/Css/FirmwareList.css');

        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheKey = 'ffpi_firmware_list_cache_' . $this->configurationManager->getContentObject()->data['uid'];;
        $cache = $cacheManager->getCache('ffpi_firmware_list_cache');

        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        if ($cache->has($cacheKey)) {
            $firmwareList = $cache->get($cacheKey);
        } else {
            $files = [];
            foreach ($this->folder as $folder){
                $folder = $resourceFactory->getFolderObjectFromCombinedIdentifier($folder);
                $files = array_merge($files, $folder->getFiles(0, 0, Folder::FILTER_MODE_USE_OWN_AND_STORAGE_FILTERS, true, 'name'));
            }
            $firmwareList = [];

            foreach ($files as $file) {
                if (FilenameUtility::stringContainsArray($file->getIdentifier(), $this->blacklistedPathSegments)) {
                    continue;
                }

                $firmwareParts = FilenameUtility::getFirmwareParts($file->getName());
                $unifiedRouterIdentifier = FilenameUtility::createUnifiedRouterIdentifier($firmwareParts);
                if ($firmwareParts['sysupgrade']) {
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['publicUrl'] = $file->getPublicUrl();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                } else {
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['publicUrl'] = $file->getPublicUrl();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                }
                $firmwareList[$unifiedRouterIdentifier]['router']['router'] = $firmwareParts['router'];
                $firmwareList[$unifiedRouterIdentifier]['router']['routerVersion'] = $firmwareParts['routerVersion'];

                $paths = [
                    'EXT:ffpi_firmware_list/Resources/Public/DevicePictures/' . $firmwareParts['router'] . '-' . $firmwareParts['routerVersion'] . '.svg',
                    'EXT:ffpi_firmware_list/Resources/Public/DevicePictures/' . $firmwareParts['router'] . '-' . str_replace('.', '-', $firmwareParts['routerVersion']) . '.svg',
                    'EXT:ffpi_firmware_list/Resources/Public/DevicePictures/' . $firmwareParts['router'] . '.svg',
                    'EXT:ffpi_firmware_list/Resources/Public/DevicePictures/' . $unifiedRouterIdentifier . '.svg',
                ];

                foreach ($paths as $t3IconPath) {
                    $iconPath = GeneralUtility::getFileAbsFileName($t3IconPath);
                    if (!empty($iconPath) && file_exists($iconPath)) {
                        $firmwareList[$unifiedRouterIdentifier]['router']['icon'] = $t3IconPath;
                        break; // Stoppt die Schleife, wenn eine Datei gefunden wurde
                    }
                }
            }

            ksort($firmwareList, SORT_NATURAL);
            $cache->set($cacheKey, $firmwareList, [], 604800); // 604800 sekunden = 1 woche
        }
        $this->view->assign('settings', $this->settings);
        $this->view->assign('firmwareList', $firmwareList);
    }
}
