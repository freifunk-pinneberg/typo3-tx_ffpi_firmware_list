<?php

namespace FFPI\FfpiFirmwareList\ViewHelper;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RouterNameFallbackViewHelper extends AbstractViewHelper
{
    protected static $manufacturer = [
        'tp-link' => 'TP-Link',
        'ubiquiti' => 'Ubiquiti',
        'ubnt' => 'Ubiquiti',
        'alfa-network' => 'ALFA Network',
        'allnet' => 'ALLNET',
        'buffalo' => 'Buffalo',
        'd-link' => 'D-Link',
        'linksys' => 'Linksys',
        'netgear' => 'NETGEAR',
        'openmesh' => 'OpenMesh',
        'avm-fritz' => 'AVM FRITZ!',
        'gl-inet' => 'GL.iNet',
        'zyxel' => 'ZyXEL',
        'lemaker' => 'LeMaker',
        'wd' => 'Western Digital'
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