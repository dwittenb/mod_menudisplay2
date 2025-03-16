<?php

/***
 * @package     mod_menudisplay2
 * @copyright   Copyright (C) 2025 https://github.com/dwittenb/mod_menudisplay2, All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author     	Didi
 * @link 		https://github.com/dwittenb/mod_menudisplay2
 ***/

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

if (!empty($items)) :
?>
    <div class="mod-menu">
        <!-- Seitentitel -->
        <?php if ($params->get('showTitle', '1')) { ?>
            <h2>
                <?php echo $params->get('titleText', '');    ?>
            </h2>
        <?php } ?>

        <!-- Seiteninhalte -->
        <?php
        // prepare nesting Depth
        $nestingDepth = $params->get('nestingDepth', '1');

        foreach ($items as $menuItem) :

            // dont show menuitems greater than depth
            if ($menuItem->level > $nestingDepth) break;
        ?>
            <!-- show menuitem as Headline with link -->
            <div style="margin: <?php echo $params->get('marginTop', ''); ?>px 0px 0px 0px ">
                <h3>
                    <a href="<?php echo Route::_($menuItem->link); ?>">
                        <!-- create the hierarchical Number 3.2.3 -->
                        <?php
                        echo $menuItem->numbering;
                        echo " " . htmlspecialchars($menuItem->title, ENT_QUOTES, 'UTF-8');
                        ?>
                    </a>
                </h3>
            </div>

            <?php
            // begin show articles
            $articleNr = 1;
            foreach ($menuItem->articleItems as $articleItem) :
                $href = Route::_('index.php?option=com_content&view=article&id=' . $articleItem->id . '&catid=' . $articleItem->catid);
            ?>
                <div style="margin: <?php echo $params->get('marginTop', ''); ?>px 0px 0px 0px ">
                    <div class="article">
                        <h4>
                            <a href="<?= $href; ?>">
                                <?php
                                echo $menuItem->numbering . "." . $articleNr;
                                echo " " . $articleItem->title;
                                ?>
                            </a>
                        </h4>
                        <div style="margin: 0px 0px 20px <?php echo (int)$params->get('marginLeft', '') + ($menuItem->level + 1) * (int)$params->get('marginLeft', ''); ?>px">
                            <?php
                            echo $articleItem->introtext;
                            echo $articleItem->fulltext;
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                $articleNr++;
            endforeach;
            // end show articles
            ?>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>Keine Menüeinträge gefunden.</p>
<?php endif; ?>

<!-- Copyright -->
<?php if ($params->get('showCopyright', '1')) { ?>
    <hr>
    <h7>
        <?php echo $params->get('copyrightText', ''); ?>
    </h7>
    <hr>
<?php } ?>