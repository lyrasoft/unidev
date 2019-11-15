<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Command;

use Lyrasoft\Unidev\Command\Unidev\BladeoptCommand;
use Windwalker\Core\Console\CoreCommand;

/**
 * The UnidevCommand class.
 *
 * @since  1.0
 */
class UnidevCommand extends CoreCommand
{
    protected $name = 'unidev';

    protected $description = 'Unidev helpers';

    protected function init()
    {
        $this->addCommand(BladeoptCommand::class);
    }
}
