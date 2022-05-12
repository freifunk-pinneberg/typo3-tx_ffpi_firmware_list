<?php

namespace FFPI\FfpiFirmwareList\Utility;

class FilenameUtility
{

    /**
     * @param string $filename
     * @return array
     */
    public static function getFirmwareParts(string $filename): array
    {
        $regex = '/^gluon-ffpi-((\d+\.\d+\.?\d*)*-?((beta|exp|experimental)\d*)?)-(.*?)-?(v\d\.?\d*|rev-\w?\d+|xm|xw)?-?(sysupgrade)?(\..{2,7})$/';
        preg_match($regex, $filename, $filenameParts);

        $firmwareParts = [
            'firmwareVersion' => $filenameParts[1],
            'firmwareVersionNumber' => $filenameParts[2],
            'firmwareVersionAddition' => $filenameParts[3],
            'router' => $filenameParts[5],
            'routerVersion' => $filenameParts[6],
            'sysupgrade' => ($filenameParts[7] == 'sysupgrade'),
            'beta' => ($filenameParts[6] == 'beta'),
            'experimental' => ($filenameParts[6] == 'exp' || $filenameParts[6] == 'experimental'),
            'fileType' => $filenameParts[8],
        ];
        return $firmwareParts;
    }

    /**
     * @param array $firmwareParts
     * @return string
     */
    public static function createUnifiedRouterIdentifier(array $firmwareParts): string
    {
        $firmwareParts['router'] = str_replace('ubnt', 'ubiquiti', $firmwareParts['router']);
        return $firmwareParts['router'] . $firmwareParts['routerVersion'];
    }

    /**
     * @param string $haystack
     * @param string[] $needles
     * @return bool
     */
    public static function stringContainsArray(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle){
            if (str_contains($haystack, $needle)){
                return true;
            }
        }
        return false;
    }
}