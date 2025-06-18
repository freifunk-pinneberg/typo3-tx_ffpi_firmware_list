<?php

namespace FFPI\FfpiFirmwareList\Utility;

class FilenameUtility
{

    /**
     * Gluon creates filename like gluon-ffpi-1.1.0-beta20240912-tp-link-cpe210-v2.bin
     * This function tries to extract all the informationen out of the filename
     *
     * @param string $filename
     * @return array{
     *     fullName: string,
     *     firmwareVersion: string|null,
     *     firmwareVersionNumber: string|null,
     *     sortableFirmwareVersionNumber: string|null,
     *     firmwareVersionAddition: string|null,
     *     router: string|null,
     *     routerVersion: string|null,
     *     sysupgrade: bool,
     *     factory: bool,
     *     other: bool,
     *     beta: bool,
     *     firmwareType: string,
     *     experimental: bool,
     *     stable: bool,
     *     fileType: string|null
     * } An associative array containing parsed firmware details.
     */
    public static function getFirmwareParts(string $filename): array
    {
        $regex = '/^gluon-(?:[a-zA-Z]{2,10})-((\d+\.\d+\.?\d*)*(?:-|\+)?((beta|exp|experimental|\d*?)\d*)?)-(.*?)-?(v\d\.?\d*|rev-\w?\d+|xm|xw)?-?(sysupgrade|bootloader|factory_fw|factory_fw30|factory_fw35|kernel|rootfs|recovery)?(\..{2,7})$/';
        preg_match($regex, $filename, $filenameParts);

        $firmwareParts = [
            'fullName' => $filename,
            'firmwareVersion' => $filenameParts[1] ?? null,
            'firmwareVersionNumber' => $filenameParts[2] ?? null,
            'sortableFirmwareVersionNumber' => self::convertVersionNumber($filenameParts[1] ?? '0'),
            'firmwareVersionAddition' => $filenameParts[3] ?? null,
            'router' => $filenameParts[5] ?? '',
            'routerVersion' => $filenameParts[6] ?? null,
            'sysupgrade' => ($filenameParts[7] == 'sysupgrade'),
            'factory' => ($filenameParts[7] == ''),
            'other' => ($filenameParts[7] !== 'sysupgrade' && $filenameParts[7] !== ''),
            'beta' => ($filenameParts[4] == 'beta'),
            'firmwareType' => empty($filenameParts[7]) ? 'factory' : $filenameParts[7],
            'experimental' => ($filenameParts[4] == 'exp' || $filenameParts[4] == 'experimental'),
            'stable' => (empty($filenameParts[4])),
            'fileType' => $filenameParts[8] ?? null,
        ];

        if (empty($firmwareParts['routerVersion']) && in_array($firmwareParts['fileType'], ['.vmdk', '.vdi'], true)) {
            // .vmdk  → routerVersion = 'vmdk'
            // .vdi   → routerVersion = 'vdi'
            $firmwareParts['routerVersion'] = ltrim($firmwareParts['fileType'], '.');
        }

        return $firmwareParts;
    }

    /**
     * Different OpenWRT/Gluon versions, and different maintainers have a different naming convention. We try to unify it.
     * This function is called early to handel cases where the getFirmwareParts() regex struggles with bad filenames
     *
     * @param string $fileName
     * @return string
     */
    public static function createUnifiedRouterFileName(string $fileName): string
    {
        $fileName = preg_replace('/tp-link-archer-c6-v2-eu-ru-jp(-sysupgrade)?\.bin$/', 'tp-link-archer-c6-v2$1.bin', $fileName);
        $fileName = preg_replace('/tp-link-tl-wr842n-v3(-sysupgrade)?\.bin$/', 'tp-link-tl-wr842n-nd-v3$1.bin', $fileName);
        $fileName = preg_replace('/n-nd-v2(-sysupgrade)?\.bin$/', 'nd-v2$1.bin', $fileName);
        $fileName = preg_replace('/n-nd-v3(-sysupgrade)?\.bin$/', 'nd-v3$1.bin', $fileName);
        $fileName = preg_replace('/n-nd-v4(-sysupgrade)?\.bin$/', 'nd-v4$1.bin', $fileName);
        $fileName = preg_replace('/cpe210-v1\.0(-sysupgrade)?\.bin$/', 'cpe210-v1$1.bin', $fileName);
        $fileName = preg_replace('/cpe210-v2\.0(-sysupgrade)?\.bin$/', 'cpe210-v2$1.bin', $fileName);
        $fileName = preg_replace('/cpe210-v3\.0(-sysupgrade)?\.bin$/', 'cpe210-v3$1.bin', $fileName);
        $fileName = preg_replace('/cpe220-v1\.0(-sysupgrade)?\.bin$/', 'cpe220-v1$1.bin', $fileName);
        $fileName = preg_replace('/cpe220-v2\.0(-sysupgrade)?\.bin$/', 'cpe220-v2$1.bin', $fileName);
        $fileName = preg_replace('/cpe220-v3\.0(-sysupgrade)?\.bin$/', 'cpe220-v3$1.bin', $fileName);
        return $fileName;
    }

    /**
     * Different OpenWRT/Gluon versions, and different maintainers have a different naming convention. We try to unify it.
     * This function is called later to ensure proper grouping of files, and to have a base for the router svg images.
     *
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
