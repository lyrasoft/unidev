<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Buffer;

/**
 * The XmlResponse class.
 *
 * @since  1.0
 */
class XmlBuffer extends AbstractBuffer
{
    /**
     * toString
     *
     * @return  string
     */
    public function toString()
    {
        $xml = new \SimpleXMLElement('<root />');

        $xml->addChild('message', $this->message);
        $xml->addChild('success', (int) $this->success);

        $data = $xml->addChild('data');

        $this->getXmlChildren($data, $this->data);

        return $xml->asXML();
    }

    /**
     * Method to build a level of the XML string -- called recursively
     *
     * @param   \SimpleXMLElement $node SimpleXMLElement object to attach children.
     * @param   object            $var  Object that represents a node of the XML document.
     *
     * @return  void
     */
    protected function getXmlChildren(\SimpleXMLElement $node, $var)
    {
        // Iterate over the object members.
        foreach ((array) $var as $k => $v) {
            if (is_scalar($v)) {
                $n = $node->addChild($k, $v);
                $n->addAttribute('type', gettype($v));
            } else {
                $n = $node->addChild($k);
                $n->addAttribute('type', gettype($v));

                static::getXmlChildren($n, $v);
            }
        }
    }

    /**
     * getMimeType
     *
     * @return  string
     */
    public function getMimeType()
    {
        return 'application/xml';
    }
}
