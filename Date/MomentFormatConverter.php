<?php

namespace Symbio\OrangeGate\AdminBundle\Date;

use Sonata\CoreBundle\Exception\InvalidParameterException;

class MomentFormatConverter extends \Sonata\CoreBundle\Date\MomentFormatConverter
{
    /**
     * @var array This defines the mapping between PHP date format (key) and moment.js date format (value)
     */
    private $phpMomentMapping = array(
        "yyyy-MM-dd'T'HH:mm:ssZZZZZ" => 'YYYY-MM-DDTHH:mm:ssZZ', // 2014-05-14T13:55:01+02:00
        "yyyy-MM-dd HH:mm"           => 'YYYY-MM-DD HH:mm',      // 2014-05-14
        "yyyy-MM-dd"                 => 'YYYY-MM-DD',            // 2014-05-14
    );

    public function convert($format)
    {
        if (!array_key_exists($format, $this->phpMomentMapping)) {
            throw new InvalidParameterException(sprintf("PHP Date format '%s' is not a convertible moment.js format; please add it to the 'Symbio\OrangeGate\AdminBundle\Date\MomentFormatConverter' class by submitting a pull request if you want it supported.", $format));
        }

        return $this->phpMomentMapping[$format];
    }
}