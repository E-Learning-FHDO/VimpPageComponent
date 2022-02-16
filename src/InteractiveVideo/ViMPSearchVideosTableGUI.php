<?php
namespace srag\Plugins\VimpPageComponent\InteractiveVideo;

use srag\Plugins\ViMP\UIComponents\Player\VideoPlayer;
use vpcoSearchVideosTableGUI;
use xvmpSearchVideosTableGUI;
use ilObjPluginDispatchGUI;
use ilObjViMPGUI;
use xvmpOwnVideosGUI;
use ilVimpPageComponentPlugin;
use ilLinkButton;

/**
 * Class ViMPSearchVideosTableGUI
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ViMPSearchVideosTableGUI extends vpcoSearchVideosTableGUI
{
    /**
     * @var string
     */
    protected $select_cmd;

    /**
     * ViMPSearchVideosTableGUI constructor.
     * @param        $parent_gui
     * @param        $parent_cmd
     * @param string $select_cmd
     */
    public function __construct($parent_gui, $parent_cmd, $select_cmd = 'add')
    {
        $this->select_cmd = $select_cmd;
        xvmpSearchVideosTableGUI::__construct($parent_gui, $parent_cmd);
        VideoPlayer::loadVideoJSAndCSS(false);
        $base_link = $this->ctrl->getLinkTargetByClass(array(ilObjPluginDispatchGUI::class, ilObjViMPGUI::class, xvmpOwnVideosGUI::class),'', '', true);
        $this->tpl_global->addOnLoadCode('VimpContent.ajax_base_url = "'.$base_link.'";');

        $this->pl = new ilVimpPageComponentPlugin();
        $this->setRowTemplate($this->pl->getDirectory() . '/templates/' . static::ROW_TEMPLATE);

        $this->setFormAction($this->ctrl->getFormAction($this->parent_obj));
    }

    /**
     * @param $a_set
     * @return string
     */
    protected function getAddButton($a_set)
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption('add');
        $this->ctrl->setParameter($this->parent_obj, 'mid', $a_set['mid']);
        $button->setUrl($this->ctrl->getLinkTarget($this->parent_obj, $this->select_cmd));
        return $button->getToolbarHTML();
    }

}