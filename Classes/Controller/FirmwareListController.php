<?php

namespace FFPI\FfpiFirmwareList\Controller;

use FFPI\FfpiFirmwareList\Domain\Repository\FirmwareVersionDetailRepository;
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

    /** @var FirmwareVersionDetailRepository */
    protected $firmwareVersionDetailRepository;

    public function injectFirmwareVersionDetailRepository(FirmwareVersionDetailRepository $firmwareVersionDetailRepository): void
    {
        $this->firmwareVersionDetailRepository = $firmwareVersionDetailRepository;
    }

    protected function initializeAction(): void
    {
        $this->folder = explode(',', $this->settings['folder']);
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
            foreach ($this->folder as $folder) {
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
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['firmwareDetails'] = $this->firmwareVersionDetailRepository->findOneByVersion($firmwareParts['firmwareVersion']);
                } else {
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['publicUrl'] = $file->getPublicUrl();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['firmwareDetails'] = $this->firmwareVersionDetailRepository->findOneByVersion($firmwareParts['firmwareVersion']);
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
            foreach ($firmwareList as $router => &$versions) {
                uksort($versions['firmware'], 'self::customVersionCompare');
            }
            unset($versions);

            //Mark best download
            foreach ($firmwareList as $router => &$versions) {
                foreach ($versions['firmware'] as $version => &$firmwareTypes) {
                    foreach (['factory', 'sysupgrade'] as $type) {
                        if (isset($firmwareTypes[$type]['firmwareParts']['stable']) && $firmwareTypes[$type]['firmwareParts']['stable']) {
                            // Setzen Sie "recommended" auf true, wenn "stable" true ist
                            $firmwareTypes[$type]['firmwareParts']['recommended'] = true;
                            break; // Beenden Sie die innere Schleife, wenn "recommended" gesetzt ist
                        }
                    }
                }
            }
            unset($firmwareTypes); // Referenz aufheben
            unset($versions); // Referenz aufheben

            $cache->set($cacheKey, $firmwareList, [], 604800); // 604800 sekunden = 1 woche
        }
        $this->view->assign('settings', $this->settings);
        $this->view->assign('firmwareList', $firmwareList);
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    protected static function customVersionCompare(string $a, string $b)
    {
        // Zuerst die Hauptversionen und Suffixe trennen
        $regex = '/^(.*?)(-exp|-beta)?([0-9]+)?$/';
        preg_match($regex, $a, $matchesA);
        preg_match($regex, $b, $matchesB);

        // Hauptversionsvergleich
        $versionCompareResult = version_compare($matchesA[1], $matchesB[1]);
        if ($versionCompareResult != 0) {
            return $versionCompareResult;
        }

        // Vergleich der Suffixe
        if ($matchesA[2] != $matchesB[2]) {
            // Hier wird eine einfache Reihenfolge definiert: exp < beta < stable
            $order = ['' => 2, '-beta' => 1, '-exp' => 0];
            return ($order[$matchesA[2]] <=> $order[$matchesB[2]]) * -1;
        }

        // Vergleich der Suffix-Nummern, falls vorhanden
        return ($matchesA[3] <=> $matchesB[3]) * -1; // Für absteigende Sortierung
    }
}
