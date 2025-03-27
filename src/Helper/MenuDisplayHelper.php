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

    /**
     * Databasequery to get menu-fields as array of Objects
     */
    public function getMenuItems($menuType, $access)
    {
        if (empty($menuType)) return [];

        $db = $this->getDatabase();

        // query all menus of type menuType that are published with access level
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'link', 'parent_id', 'level', 'lft', 'rgt']))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('menuType') . ' = ' . $db->quote($menuType))
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('access') . ' = ' . (int)$access)
            ->order('lft ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Databasequery to get content-fields as array of Objects
     */
    public function getArticles($categoryId, $limit, $access, $order)
    {
        if (!$categoryId) return [];

        // query all articles of category with id that are published with access level
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'introtext', 'fulltext', 'catid']))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('catid') . ' = ' . (int) $categoryId)
            ->where($db->quoteName('state') . ' = 1') // Nur veröffentlichte Artikel
            ->where($db->quoteName('access') . ' = ' . (int)$access);

        if ($order != "")
            $query->order($order);

        if ($limit > 0)
            $query->setLimit($limit);

        // echo $query->__toString() . "<br>";
        // echo "<pre>";
        // print_r($db->loadObjectList());
        // echo "</pre>";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Databasequery to get content-fields as array of Objects
     */
    public function getArticle($id, $access)
    {
        if (!$id) return [];

        $db = $this->getDatabase();

        // query the article with id that are published with access level
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'introtext', 'fulltext', 'catid']))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = ' . (int) $id)
            ->where($db->quoteName('state') . ' = 1') // Nur veröffentlichte Artikel
            ->where($db->quoteName('access') . ' = ' . (int)$access)
            ->order('title ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * get Hierarchical Numbering of Menuitems
     */
    public function addMenuNumbering($data)
    {
        $numbering = [];    // speichert laufende Nummern für jedes Kapitel
        $output = [];

        foreach ($data as $datakey => $row) {
            $level = $row->level;

            // Erhöhe die Nummer für das aktuelle Kapitel
            if (!isset($numbering[$level])) {
                $numbering[$level] = 1;
            } else {
                $numbering[$level]++;
            }

            // Entferne höhere Level, falls ein neues Kapitel beginnt
            foreach (array_keys($numbering) as $key) {
                if ($key > $level) {
                    unset($numbering[$key]);
                }
            }

            // Erzeuge die hierarchische Nummer
            $hierarchyNumber = implode('.', array_slice($numbering, 0, $level));

            // Speichere die formatierte Ausgabe der Kapitelnummer als Object-value
            $data[$datakey]->numbering = $hierarchyNumber;
        }

        return $data;
    }

    /**
     * get Hierarchical Numbering of ArticleItems
     */
    public function addArticleNumbering($articleItems)
    {
        $articleNr = 1;
        foreach ($articleItems as $key => $articleItem) {
            $articleItems[$key]->numbering = $articleNr++;
        }
        return $articleItems;
    }

    public function getMenuArticles(Registry $params, SiteApplication $app)
    {
        $allItems = [];
        $menuType = (string) $params->get('menuType', '');
        $articleLimit = (int) $params->get('articleLimit', '');
        $access = $params->get('access', '');
        $sortBy = $params->get('sortBy', '');

        // create order for Articles
        $select = array(
            'none' => '',
            'date' => 'created ASC',
            'rdate' => 'created DESC',
            'alpha' => 'title ASC',
            'ralpha' => 'title DESC',
            'alias' => 'alias ASC',
            'ralias' => 'alias DESC',
            'order' => 'id ASC',
            'rorder' => 'id DESC'
        );
        $order = (key_exists($sortBy, $select)) ? $select[$sortBy] : "";

        // return empty array if no menu is selected
        if (empty($menuType))
            return [];

        // get active menu
        $sitemenu = $app->getMenu();
        $activeMenuitem = $sitemenu->getActive();

        // get menuitems and add hierarchical Number
        $menuItems = self::getMenuItems($menuType, $access);
        $menuItems = self::addMenuNumbering($menuItems);

        foreach ($menuItems as $menuKey => $menuItem) {
            //          	$content = Uri::getInstance()."/".($menuItem->link);

            // do not show content of menuitem of active menuitem
            if ($menuItem->id == $activeMenuitem->id) continue;

            $urlQuery = [];
            $parts = parse_url($menuItem->link);
            parse_str($parts['query'], $urlQuery);

            // ignore content with filter_tags
            if (array_key_exists("filter_tag", $urlQuery)) {
                if (count($urlQuery['filter_tag']) < $params->get('countTags', '')) continue;
            }

            switch ($urlQuery['view']) {
                case "category":
                    // get all articles with the category
                    $allItems[$menuKey] = $menuItem;
                    $articleItems = self::getArticles($urlQuery['id'], $articleLimit, $access, $order);
                    $articleItems = self::addArticleNumbering($articleItems);
                    $allItems[$menuKey]->articleItems = $articleItems;
                    break;
                case "article":
                    // get article
                    $allItems[$menuKey] = $menuItem;
                    $articleItems = self::getArticle($urlQuery['id'], $access);
                    $articleItems = self::addArticleNumbering($articleItems);
                    $allItems[$menuKey]->articleItems = $articleItems;
                    break;
                default:
                    break;
            }
        }

        return $allItems;
    }
}
