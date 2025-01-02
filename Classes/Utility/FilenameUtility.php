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
        $regex = '/^gluon-(?:[a-zA-Z]{2,10})-((\d+\.\d+\.?\d*)*(?:-|\+)?((beta|exp|experimental|\d*?)\d*)?)-(.*?)-?(v\d\.?\d*|rev-\w?\d+|xm|xw)?-?(sysupgrade|bootloader|factory_fw|factory_fw30|factory_fw35|kernel|rootfs)?(\..{2,7})$/';
        preg_match($regex, $filename, $filenameParts);

        $firmwareParts = [
            'fullName' => $filename,
            'firmwareVersion' => $filenameParts[1],
            'firmwareVersionNumber' => $filenameParts[2],
            'sortableFirmwareVersionNumber' => self::convertVersionNumber($filenameParts[1]),
            'firmwareVersionAddition' => $filenameParts[3],
            'router' => $filenameParts[5],
            'routerVersion' => $filenameParts[6],
            'sysupgrade' => ($filenameParts[7] == 'sysupgrade'),
            'factory' => ($filenameParts[7] == ''),
            'other' => ($filenameParts[7] !== 'sysupgrade' && $filenameParts[7] !== ''),
            'beta' => ($filenameParts[4] == 'beta'),
            'firmwareType' => empty($filenameParts[7]) ? 'factory' : $filenameParts[7],
            'experimental' => ($filenameParts[4] == 'exp' || $filenameParts[4] == 'experimental'),
            'stable' => (empty($filenameParts[4])),
            'fileType' => $filenameParts[8],
        ];
        return $firmwareParts;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function createUnifiedRouterFileName(string $fileName): string
    {
        $fileName = preg_replace('/tp-link-archer-c6-v2-eu-ru-jp\.bin$/', 'tp-link-archer-c6-v2.bin',  $fileName);
        $fileName = preg_replace('/tp-link-tl-wr842n-v3\.bin$/', 'tp-link-tl-wr842n-nd-v3.bin',  $fileName);
        return $fileName;
    }

    /**
     * @param array $firmwareParts
     * @return string
     */
    public static function createUnifiedRouterIdentifier(array $firmwareParts): string
    {
        $firmwareParts['router'] = str_replace('ubnt-', 'ubiquiti-', $firmwareParts['router']);
        $firmwareParts['router'] = str_replace('ubiquiti-erx', 'ubiquiti-edgerouter-x', $firmwareParts['router']);
        $firmwareParts['router'] = preg_replace('/^zbt-/', 'zbtlink-', $firmwareParts['router']);
        $firmwareParts['router'] = preg_replace('/^gl.inet/', 'gl-inet', $firmwareParts['router']);
        $firmwareParts['router'] = preg_replace('/^gl-/', 'gl-inet-', $firmwareParts['router']);
        return $firmwareParts['router'] . $firmwareParts['routerVersion'];
    }

    /**
     * @param string $haystack
     * @param string[] $needles
     * @return bool
     */
    public static function stringContainsArray(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $oldFormat
     * @return string
     */
    public static function convertVersionNumber(string $oldFormat): string
    {
        // Ersetzen von '+' mit '-' für konsistente Behandlung von Suffixen
        $version = str_replace('+', '-', $oldFormat);

        // Aufspalten in Hauptversion und Suffix
        $parts = explode('-', $version);
        $numbers = explode('.', $parts[0]);

        // Ergänzen der Hauptversionsnummern auf 3 Stellen
        while (count($numbers) < 3) {
            $numbers[] = '0';
        }

        // Hinzufügen der Suffix-Daten
        if (isset($parts[1])) {
            $suffix = preg_replace('/[^a-zA-Z]/', '', $parts[1]);
            $suffixNumber = preg_replace('/[^0-9]/', '', $parts[1]) ?: '0';

            // Bestimmen der Suffix-Priorität
            $order = ['exp' => '0', 'beta' => '1'];
            $numbers[] = $order[$suffix] ?? '2'; // Standardwert ist 2 für unbekannten suffix
            $numbers[] = $suffixNumber;
        } else {
            // Fügt '3.0' für stabile Versionen ohne Suffix hinzu
            $numbers[] = '3';
            $numbers[] = '0';
        }

        return implode('.', $numbers);

    }
}
