<?php
namespace srag\Plugins\VimpPageComponent\InteractiveVideo;

use srag\Plugins\ViMP\UIComponents\Player\VideoPlayer;
use vpcoOwnVideosTableGUI;
use xvmpTableGUI;
use ilObjPluginDispatchGUI;
use ilObjViMPGUI;
use xvmpOwnVideosGUI;
use ilVimpPageComponentPlugin;
use ilViMPPlugin;
use ilTemplate;
use ilLinkButton;

/**
 * Class ViMPOwnVideosTableGUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ViMPOwnVideosTableGUI extends vpcoOwnVideosTableGUI
{
    /**
     * @var string
     */
    protected $select_cmd;

    /**
     * ViMPOwnVideosTableGUI constructor.
     * @param        $parent_gui
     * @param        $parent_cmd
     * @param string $show_filtered_cmd
     */
    public function __construct($parent_gui, $parent_cmd, $show_filtered_cmd = 'showFilteredOwnVideos', $select_cmd = 'add')
    {
        global $DIC;
        $this->select_cmd = $select_cmd;
        $ilUser = $DIC['ilUser'];
        $ilCtrl = $DIC['ilCtrl'];
        $id = 'xvmp_own_' . $_GET['ref_id'] . '_' . $ilUser->getId();
        $this->setId($id);
        $this->setPrefix($id);
        $this->setFormName($id);
        $ilCtrl->saveParameter($parent_gui, $this->getNavParameter());

        xvmpTableGUI::__construct($parent_gui, $parent_cmd);

        $this->setDisableFilterHiding(true);
        $this->tpl_global->addOnLoadCode('xoctWaiter.init("waiter");');
        $base_link = $this->ctrl->getLinkTarget($this->parent_obj,'', '', true);
        $this->tpl_global->addOnLoadCode('VimpSearch.base_link = "'.$base_link.'";');
        VideoPlayer::loadVideoJSAndCSS(false);

        $base_link = $this->ctrl->getLinkTargetByClass(array(ilObjPluginDispatchGUI::class, ilObjViMPGUI::class, xvmpOwnVideosGUI::class),'', '', true);
        $this->tpl_global->addOnLoadCode('VimpContent.ajax_base_url = "'.$base_link.'";');

        $this->pl = new ilVimpPageComponentPlugin();
        $this->vimp_pl = ilViMPPlugin::getInstance();
        $this->setRowTemplate($this->pl->getDirectory() . '/templates/' . static::ROW_TEMPLATE);

        $this->setFormAction($this->ctrl->getFormAction($this->parent_obj));

        if ($DIC->ctrl()->getCmd() !== $show_filtered_cmd) {
            $this->tpl = new ilTemplate("tpl.own_videos_table.html", true, true, $this->vimp_pl->getDirectory());
            $this->tpl->setVariable('TABLE_CONTENT_HIDDEN', 'hidden');
            $this->tpl->setCurrentBlock('xvmp_show_videos_button');
            $this->tpl->setVariable(
                'SHOW_VIDEOS_LINK',
                $this->ctrl->getLinkTarget(
                    $this->parent_obj,
                    $show_filtered_cmd
                )
            );
            $this->tpl->setVariable('SHOW_VIDEOS_LABEL', $this->vimp_pl->txt('btn_show_own_videos'));
            $this->tpl->parseCurrentBlock();
        }
    }

    /**
     * @param $a_set
     *
     * @return string
     */
    protected function getAddButton($a_set): string
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption('add');
        $this->ctrl->setParameter($this->parent_obj, 'mid', $a_set['mid']);
        $button->setUrl($this->ctrl->getLinkTarget($this->parent_obj, $this->select_cmd));
        return $button->getToolbarHTML();
    }

}