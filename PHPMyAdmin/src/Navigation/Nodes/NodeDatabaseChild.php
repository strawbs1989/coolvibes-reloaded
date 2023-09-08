<?php
/**
 * Functionality for the navigation tree
 */

declare(strict_types=1);

namespace PhpMyAdmin\Navigation\Nodes;

use PhpMyAdmin\ConfigStorage\Features\NavigationItemsHidingFeature;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Url;

use function __;

/**
 * Represents a node that is a child of a database node
 * This may either be a concrete child such as table or a container
 * such as table container
 */
abstract class NodeDatabaseChild extends Node
{
    /**
     * Returns the type of the item represented by the node.
     *
     * @return string type of the item
     */
    abstract protected function getItemType(): string;

    /**
     * Returns HTML for control buttons displayed infront of a node
     *
     * @return string HTML for control buttons
     */
    public function getHtmlForControlButtons(NavigationItemsHidingFeature|null $navigationItemsHidingFeature): string
    {
        $ret = '';
        if ($navigationItemsHidingFeature !== null) {
            $params = [
                'hideNavItem' => true,
                'itemType' => $this->getItemType(),
                'itemName' => $this->realName,
                'dbName' => $this->realParent()->realName,
            ];

            $ret = '<span class="navItemControls">'
                . '<a href="' . Url::getFromRoute('/navigation') . '" data-post="'
                . Url::getCommon($params, '', false) . '"'
                . ' class="hideNavItem ajax">'
                . Generator::getImage('hide', __('Hide'))
                . '</a></span>';
        }

        return $ret;
    }
}
