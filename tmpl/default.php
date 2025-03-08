<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

if (!empty($items)) :
?>
    <div class="mod-menu">
        <h2>
            <?php echo $params->get('title', ''); ?>
        </h2>
        <?php
        for ($i = 1; $i < 10; $i++) $levelNr[$i] = 0;

        foreach ($items as $menuItem) :
            // initialize levelNr
            $levelNr[$menuItem->level]++;
            $levelNr[$menuItem->level + 1] = 0;
            $nr = [];
        ?>
            <div style="margin: <?php echo $params->get('margin', ''); ?>px 0px 0px 0px ">
                <h3>
                    <a href="<?php echo Route::_($menuItem->link); ?>">
                        <?php
                        for ($i = 1; $i <= $menuItem->level; $i++) {
                            $nr[$i] = $levelNr[$i];
                        }
                        echo implode(".", $nr) . " " . htmlspecialchars($menuItem->title, ENT_QUOTES, 'UTF-8');
                        ?>
                    </a>
                </h3>
                <?php
                foreach ($menuItem->articleItems as $articleItem) :
                    // initialize levelNr
                    $levelNr[$menuItem->level + 1]++;
                    $levelNr[$menuItem->level + 2] = 0;
                    $nr = [];
                ?>
                    <div class="article">
                        <h4>
                            <a href="<?php echo Route::_('index.php?option=com_content&view=article&id=' . $articleItem->id . '&catid=' . $articleItem->catid); ?>">
                                <?php
                                for ($i = 1; $i <= $menuItem->level + 1; $i++) {
                                    $nr[$i] = $levelNr[$i];
                                }
                                echo implode(".", $nr) . " " . $articleItem->title;
                                ?>
                            </a>
                        </h4>
                        <div style="margin: 0px 0px 20px <?php echo (int)$params->get('margin', '') + ($menuItem->level + 1) * (int)$params->get('margin', ''); ?>px">
                            <?php echo $articleItem->introtext; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>Keine Menüeinträge gefunden.</p>
<?php endif; ?>
<hr>
<h7><?php echo $params->get('copyright', ''); ?></h7>
<hr>