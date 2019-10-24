<?php

    /**
     * @file
     *
     * This file holds the widget for the yes-no question type, to edit it's default values.
     *
     * Features:
     * - YES/NO preselection
     * - EM integration to insert an em expression like {TOKEN:ATTRIBUTE_6}. At this state there is no validation implemented. Attributes must hold Y or N.
     *
     * DEV MEMO:
     * Validation could be difficult cause if you using tokens and you don't had setup a working token dataset
     *
     * For this feature you need editDefaultvalues.php, database.php, adminstyle.css
     */

    class yesNo_defaultvalue_widget extends CWidget
    {
        public $widgetOptions;

        //init() method is called automatically before all others
        public function init()
        {
            /*you can set initial default values and other stuff here.
             * it's also a good place to register any CSS or Javascript your
             * widget may need. */

        }

        public function run()
        {

            $qtproperties = $this->widgetOptions['qtproperties'];
            $questionrow = $this->widgetOptions['questionrow'];
            $langopts = $this->widgetOptions['langopts'];
            $language = $this->widgetOptions['language'];
            $defaultValues =  $this->widgetOptions['langopts'][$language][$questionrow['type']][0];

            $emfield_css = '';
            $emValue = '';
            $select = '';
            $sEmfield_css_class = '';

            // prepare variables for prefilling the form
            if(!is_null ($defaultValues))
            {
                $sDefaultValue = $defaultValues;
                if(($sDefaultValue == 'N') || ($sDefaultValue == 'Y') || ($sDefaultValue == '') ){ //|| 'Y' || NULL)){
                $select = $defaultValues;
            }else{
                $select = '*#free#*'; // Use an unusuable value for all answers
                $emValue = $defaultValues;
            }
            }

            if($questionrow['type'] == Question::QT_Y_YES_NO_RADIO) // do we need this?
            {
                $sElement_id = 'defaultanswerscale_0_' . $language;

                $aList = array(
                    'N'    => gT('No','unescaped'),
                    'Y'    => gT('Yes','unescaped'),
                    '*#free#*'   => gT('Free text, allow expression','unescaped')
                );

                $aHtmlOptions = array(
                    'empty'    => gT('<No default value>'),
                    'class'    => $sElement_id . ' form-control',
                    'onchange' => '// show EM Value Field
                                   if ($(this).val() == "*#free#*"){
                                       $("#"+$(this).closest("select").attr("id")+ "_free").prop("disabled",false).removeClass("hidden");
                                   }else{
                                       $("#"+$(this).closest("select").attr("id")+ "_free").prop("disabled",true).addClass("hidden");
                                   } '
                );
                echo CHtml::dropDownList($sElement_id, $select, $aList, $aHtmlOptions);

                echo CHtml::textArea ($sElement_id, $emValue,array(
                        'id'    => $sElement_id . '_free',
                        'class' => 'form-control'.(empty($emValue) ? " hidden" : ""),
                        'disabled' => empty($emValue),
                        'rows' => 1,
                    ));
            }
        }
    }
