<?php

namespace FFPI\FfpiFirmwareList\Controller;

use FFPI\FfpiFirmwareList\Domain\Repository\FirmwareVersionDetailRepository;
use FFPI\FfpiFirmwareList\Utility\FilenameUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Resource\File;
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
    /** @var array<string> $folder */
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
        // CSS zum generierten HTML hinzufügen
        $assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
        $assetCollector->addStyleSheet('FirmwareList', 'EXT:ffpi_firmware_list/Resources/Public/Css/FirmwareList.css');

        // Cache für die gesamte Firmwareliste
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheKey = 'ffpi_firmware_list_cache_' . $this->configurationManager->getContentObject()->data['uid'];;
        $cache = $cacheManager->getCache('ffpi_firmware_list_cache');

        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        if ($cache->has($cacheKey)) {
            // Daten aus dem Cache abrufen, falls verfügbar
            $firmwareList = $cache->get($cacheKey);
        } else {
            // Workaround für die sehr lange (nicht zwischengespeicherte) Generierungszeit
            set_time_limit(600);

            /** @var File[] $files */
            $files = [];
            // Alle Dateien aus allen konfigurierten Ordnern abrufen (rekursiv)
            foreach ($this->folder as $folder) {
                $folder = $resourceFactory->getFolderObjectFromCombinedIdentifier($folder);
                $files = array_merge($files, $folder->getFiles(0, 0, Folder::FILTER_MODE_USE_OWN_AND_STORAGE_FILTERS, true, 'name'));
            }
            $firmwareList = [];

            // Jede Datei verarbeiten (normalerweise etwa 250 pro Firmware-Ordner)
            foreach ($files as $file) {
                if (FilenameUtility::stringContainsArray($file->getIdentifier(), $this->blacklistedPathSegments)) {
                    // Dateien auf der Blacklist überspringen
                    continue;
                }

                // Hole einige Details über die Firmware aus dem Dateinamen
                $firmwareParts = FilenameUtility::getFirmwareParts($file->getName());
                $unifiedRouterIdentifier = FilenameUtility::createUnifiedRouterIdentifier($firmwareParts);
                if ($firmwareParts['sysupgrade']) {
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['publicUrl'] = $file->getPublicUrl();
                    //$firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['sha256'] = hash_file('sha256', $file->getForLocalProcessing(false));
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['sysupgrade']['file']['firmwareDetails'] = $this->firmwareVersionDetailRepository->findOneByVersion($firmwareParts['firmwareVersion']);
                } elseif($firmwareParts['factory']) {
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['publicUrl'] = $file->getPublicUrl();
                    //$firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['sha256'] = hash_file('sha256', $file->getForLocalProcessing(false));
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['factory']['file']['firmwareDetails'] = $this->firmwareVersionDetailRepository->findOneByVersion($firmwareParts['firmwareVersion']);
                } elseif ($firmwareParts['other']){
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['firmwareParts'] = $firmwareParts;
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['file'] = $file->toArray();
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['file']['publicUrl'] = $file->getPublicUrl();
                    //$firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['file']['md5'] = $file->getStorage()->hashFile($file, 'md5');
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['file']['sha256'] = hash_file('sha256', $file->getForLocalProcessing(false));
                    $firmwareList[$unifiedRouterIdentifier]['firmware'][$firmwareParts['firmwareVersion']]['other']['file']['firmwareDetails'] = $this->firmwareVersionDetailRepository->findOneByVersion($firmwareParts['firmwareVersion']);
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
                if (!isset($firmwareList[$unifiedRouterIdentifier]['router']['icon'])) {
                    trigger_error('Router image for ' . $unifiedRouterIdentifier . ' or ' . $firmwareParts['router'] . ' not found!', E_USER_NOTICE);
                }
            }

            ksort($firmwareList, SORT_NATURAL);
            //foreach ($firmwareList as $router => &$versions) {
            //    uksort($versions['firmware'], 'self::customVersionCompare');
            //}
            //unset($versions);

            foreach ($firmwareList as $router => &$routerData) {
                // Sortierung der Firmware-Versionen
                uksort($routerData['firmware'], function ($a, $b) use ($routerData) {
                    // Extrahieren der sortierbaren Versionsnummern für jede Firmware-Version
                    // Es wird geprüft, ob ein Eintrag für 'sysupgrade' und dann 'factory' existiert
                    $aVersionNumber = '';
                    $bVersionNumber = '';

                    if (isset($routerData['firmware'][$a]['sysupgrade']['firmwareParts']['sortableFirmwareVersionNumber'])) {
                        $aVersionNumber = $routerData['firmware'][$a]['sysupgrade']['firmwareParts']['sortableFirmwareVersionNumber'];
                    } elseif (isset($routerData['firmware'][$a]['factory']['firmwareParts']['sortableFirmwareVersionNumber'])) {
                        $aVersionNumber = $routerData['firmware'][$a]['factory']['firmwareParts']['sortableFirmwareVersionNumber'];
                    }

                    if (isset($routerData['firmware'][$b]['sysupgrade']['firmwareParts']['sortableFirmwareVersionNumber'])) {
                        $bVersionNumber = $routerData['firmware'][$b]['sysupgrade']['firmwareParts']['sortableFirmwareVersionNumber'];
                    } elseif (isset($routerData['firmware'][$b]['factory']['firmwareParts']['sortableFirmwareVersionNumber'])) {
                        $bVersionNumber = $routerData['firmware'][$b]['factory']['firmwareParts']['sortableFirmwareVersionNumber'];
                    }

                    // Vergleich der sortierbaren Versionsnummern
                    return version_compare($bVersionNumber, $aVersionNumber);
                });
            }
            unset($routerData); // Referenz aufheben


            //Mark best download
            foreach ($firmwareList as $routerKey => $router) {
                $goToNextDevice = false;
                foreach ($router['firmware'] as $firmwareKey => $firmware) {
                    foreach ($firmware as $variantKey => $variant) {
                        if ($variant['firmwareParts']['stable'] === true) {
                            $firmwareList[$routerKey]['firmware'][$firmwareKey][$variantKey]['firmwareParts']['recommended'] = true;
                            $goToNextDevice = true;
                        }
                    }
                    if ($goToNextDevice) {
                        break;
                    }
                }
            }

            $cache->set($cacheKey, $firmwareList, [], 604800); // 604800 sekunden = 1 woche
        }
        $this->view->assign('settings', $this->settings);
        $this->view->assign('firmwareList', $firmwareList);
    }
}
