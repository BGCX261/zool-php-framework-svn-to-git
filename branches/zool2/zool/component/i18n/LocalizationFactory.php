<?php

namespace zool\component\i18n;

use zool\i18n\MessageFormat;

use zool\i18n\Messages;

use zool\component\Component;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.i18n.localizationFactory")
 */
class LocalizationFactory extends Component{

    /** @Factory("zool.i18n.messages") */
    public function messages(){
        return Messages::instance();
    }

    /** @Factory("zool.i18n.messageFormat") */
    public function messageFormat(){
        return MessageFormat::instance();
    }

}