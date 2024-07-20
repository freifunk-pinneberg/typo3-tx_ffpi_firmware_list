<?php

namespace FFPI\FfpiFirmwareList\ViewHelper;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RouterNameFallbackViewHelper extends AbstractViewHelper
{
    protected static $manufacturer = [
        '8devices' => '8devices',
        'aerohive' => 'Aerohive',
        'alfa-network' => 'ALFA Network',
        'allnet' => 'ALLNET',
        'aruba' => 'Aruba',
        'avm-fritz' => 'AVM FRITZ!',
        'buffalo' => 'Buffalo',
        'cudy' => 'Cudy',
        'd-link' => 'D-Link',
        'devolo' => 'Devolo',
        'enterasys' => 'Enterasys',
        'engenius' => 'EnGenius',
        'gl' => 'GL.iNet',
        'gl-inet' => 'GL.iNet',
        'gl.inet' => 'GL.iNet',
        'joy-it' => 'Joy-IT',
        'lemaker' => 'LeMaker',
        'linksys' => 'Linksys',
        'netgear' => 'NETGEAR',
        'nexx' => 'Nexx',
        'ocedo' => 'Ocedo',
        'onion' => 'Onion',
        'openmesh' => 'OpenMesh',
        'plasma-cloud' => 'Plasma Cloud',
        'tp-link' => 'TP-Link',
        'ubiquiti' => 'Ubiquiti',
        'ubnt' => 'Ubiquiti',
        'vocore' => 'VoCore',
        'wd' => 'Western Digital',
        'xiaomi' => 'Xiaomi',
        'zbt' => 'ZBT',
        'zyxel' => 'ZyXEL'
    ];

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('unifiedRouterIdentifier', 'string', '', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $routerName = $arguments['unifiedRouterIdentifier'];
        $count = 0;
        foreach (self::$manufacturer as $key => $value) {
            $routerName = str_replace($key . '-', $value . ' ', $routerName, $count);
            if ($count > 0) {
                break;
            }
        }
        return $routerName;
    }
}
