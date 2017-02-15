<?php
/**
 * Description
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2015 Denis Chenu <http://www.sondages.pro>
 * @license GPL v3
 * @version 0.0.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
class ActivatePluginCommand extends CConsoleCommand
{

    /**
     * @var string $defaultAction
     * @see http://www.yiiframework.com/doc/api/1.1/CConsoleCommand#defaultAction-detail
     */
    public $defaultAction='activate';
    /**
     * Activate a plugin by name
     * @param string $name The plugin Class name
     * @return void
     */
    public function actionActivate($classname)
    {
        $oPluginManager = \Yii::app()->pluginManager;
        $aDiscoveredPlugins = $oPluginManager->scanPlugins();
        if(!array_key_exists($classname,$aDiscoveredPlugins)) {
            echo "Plugin {$classname} are not in your plugin directory\n";
            return 1;
        }

        $oPlugin=Plugin::model()->find("name=:name",array(":name"=>$classname));
        /* If plugin is not installed : just install it */
        if(!$oPlugin) {
            $oPlugin = new Plugin();
            $oPlugin->name   = $classname;
            $oPlugin->active = 0;
            $oPlugin->save();
        }
        /* Activate the plugin with the event beforeActivate */
        if ($oPlugin->active == 0)
        {
            /* Load the plugin and dispatch beforeActivate event */
            App()->getPluginManager()->loadPlugin($oPlugin->name, $oPlugin->id);
            $result = App()->getPluginManager()->dispatchEvent(new PluginEvent('beforeActivate',$this), $oPlugin->name);
            if ($result->get('success', true)) {
                $oPlugin->active = 1;
                if(!$oPlugin->save()){
                    return 1; /* This must not happen */
                }
            } else {
                echo $result->get('message', 'Failed to activate the plugin.')."\n";
                return 1;
            }
        } else {
            // No error if try to activate an already activated plugin
        }
    }

}
