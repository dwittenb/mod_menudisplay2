<?php

/***
 * @package     mod_menudisplay2
 * @copyright   Copyright (C) 2025 https://github.com/dwittenb/mod_menudisplay2, All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author     	Didi
 * @link 		https://github.com/dwittenb/mod_menudisplay2
 ***/

namespace Didi\Module\Menudisplay\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
// use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use stdClass;

class MenuDisplayHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    public function getMenuItems($menuType, $access)
    {
        if (empty($menuType)) return [];

        $db = $this->getDatabase();

        // query all menus of type menuType that are published with access level
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'link', 'level']))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('menutype') . ' = ' . $db->quote($menuType))
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('access') . ' = ' . (int)$access)
            ->order('lft ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getArticles($categoryId, $limit, $access)
    {
        if (!$categoryId) return [];

        // query all articles of category with id that are published with access level
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'introtext', 'catid']))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('catid') . ' = ' . (int) $categoryId)
            ->where($db->quoteName('state') . ' = 1') // Nur veröffentlichte Artikel
            ->where($db->quoteName('access') . ' = ' . (int)$access)
            ->order('created DESC');
        if ($limit != -1)
            $query->setLimit($limit);

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getArticle($id, $access)
    {
        if (!$id) return [];

        $db = $this->getDatabase();

        // query the article with id that are published with access level
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'introtext', 'catid']))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = ' . (int) $id)
            ->where($db->quoteName('state') . ' = 1') // Nur veröffentlichte Artikel
            ->where($db->quoteName('access') . ' = ' . (int)$access)
            ->order('created DESC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getMenuArticles(Registry $params, SiteApplication $app)
    {
        $menuType = (string) $params->get('menutype', '');
        $articleLimit = (int) $params->get('article_limit', '');
        $access = $params->get('access', '');

        $allItems = [];
        if (empty($menuType))
            return [];

        // get active menu
        $sitemenu = $app->getMenu();
        $activeMenuitem = $sitemenu->getActive();

        $menuItems = self::getMenuItems($menuType, $access);

        foreach ($menuItems as $menuKey => $menuItem) {
            //          	$content = Uri::getInstance()."/".($menuItem->link);

            // do not show content of menuitem of active menuitem
            if ($menuItem->id == $activeMenuitem->id) continue;

            $urlQuery = [];
            $parts = parse_url($menuItem->link);
            parse_str($parts['query'], $urlQuery);

            // ignore content with filter_tags
            if (array_key_exists("filter_tag", $parts)) {
                if (count($parts['filter_tag']) < 5) continue;
            }

            switch ($urlQuery['view']) {
                case "category":
                    // get all articles with the category
                    $allItems[$menuKey] = $menuItem;
                    $allItems[$menuKey]->articleItems = self::getArticles($urlQuery['id'], $articleLimit, $access);
                    break;
                case "article":
                    // get article
                    $allItems[$menuKey] = $menuItem;
                    $allItems[$menuKey]->articleItems = self::getArticle($urlQuery['id'], $access);
                    break;
                default:
                    break;
            }
        }
        return $allItems;
    }
}
