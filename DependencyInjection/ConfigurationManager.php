<?php
/**
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *    
 * Since 2K10 until today
 *  
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *  
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *  
 * File           ConfigurationManager.php
 * Updated the    16/05/16 11:01
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\DependencyInjection;

/**
 * Class ConfigurationManager
 * @package SpiritDev\Bundle\DBoxPortalBundle\DependencyInjection
 */
class ConfigurationManager {

    /**
     * @var string
     */
    private $templateName = null;

    /**
     * ConfigurationManager constructor.
     * @param $template
     */
    public function __construct($template) {
        $this->templateName = $template;
    }

    /**
     * @return string
     */
    public function getTemplateName() {
        return $this->templateName;
    }

    /**
     * @param $templateName
     */
    public function setTemplateName($templateName) {
        $this->templateName = $templateName;
    }


}