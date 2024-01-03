<?php

namespace FFPI\FfpiFirmwareList\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Kevin Quiatkowski <kevin@pinneberg.freifunk.net>
 *
 *  All rights reserved
 *
 *  You may use, distribute and modify this code under the
 *  terms of the GNU General Public License Version 3
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class FirmwareVersionDetail extends AbstractEntity
{
    /**
     * @var string
     */
    protected $version = '';

    /**
     * @var string
     */
    protected $gluonRelease = '';

    /**
     * @var string
     */
    protected $openwrtRelease = '';

    /**
     * @var bool
     */
    protected $hasSecurityIssues = false;

    /**
     * @var string
     */
    protected $additionalNotes = '';

    /**
     * @var string
     */
    protected $git = '';

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getGluonRelease(): string
    {
        return $this->gluonRelease;
    }

    /**
     * @param string $gluonRelease
     */
    public function setGluonRelease(string $gluonRelease): void
    {
        $this->gluonRelease = $gluonRelease;
    }

    /**
     * @return string
     */
    public function getOpenwrtRelease(): string
    {
        return $this->openwrtRelease;
    }

    /**
     * @param string $openwrtRelease
     */
    public function setOpenwrtRelease(string $openwrtRelease): void
    {
        $this->openwrtRelease = $openwrtRelease;
    }

    /**
     * @return bool
     */
    public function isHasSecurityIssues(): bool
    {
        return $this->hasSecurityIssues;
    }

    /**
     * @param bool $hasSecurityIssues
     */
    public function setHasSecurityIssues(bool $hasSecurityIssues): void
    {
        $this->hasSecurityIssues = $hasSecurityIssues;
    }

    /**
     * @return string
     */
    public function getAdditionalNotes(): string
    {
        return $this->additionalNotes;
    }

    /**
     * @param string $additionalNotes
     */
    public function setAdditionalNotes(string $additionalNotes): void
    {
        $this->additionalNotes = $additionalNotes;
    }

    /**
     * @return string
     */
    public function getGit(): string
    {
        return $this->git;
    }

    /**
     * @param string $git
     */
    public function setGit(string $git): void
    {
        $this->git = $git;
    }


}
