<?php
/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace MwbExporter\Formatter\Doctrine2\Yaml\Model;

use MwbExporter\Model\Column as Base;
use MwbExporter\Writer\WriterInterface;

class Column extends Base
{
    public function write(WriterInterface $writer)
    {
        $writer
            ->write('%s:', $this->getColumnName())
            ->indent()
                ->write('type: %s', $this->getDocument()->getFormatter()->getDatatypeConverter()->getType($this))
                ->writeIf($this->isPrimary(), 'primary: true')
                ->writeIf($this->parameters->get('isNotNull') == 1, 'notnull: true')
                ->writeCallback(function($writer) {
                    if ($this->parameters->get('autoIncrement') == 1) {
                        $writer
                            ->write('generator:')
                            ->indent()
                                ->write('strategy: AUTO')
                            ->outdent()
                        ;
                    }
                })
                ->writeIf(($default = $this->parameters->get('defaultValue')) && 'NULL' !== $default, 'default: '.$default)
                ->writeCallback(function($writer) {
                    foreach ($this->node->xpath("value[@key='flags']/value") as $flag) {
                        $writer->write(strtolower($flag).': true');
                    }
                })
            ->outdent()
        ;
    }
}
