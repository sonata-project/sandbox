<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// The app.php file is different from the original one (distributed in the Symfony Distribution)
//
//    The bootstrap.php file contains all initialisation informations, feel free to improve the
//    file to match your requirements.
//
//    The bootstrap.php file also handle kernel detection, by default there are 3 kernels:
//      - /admin => AdminKernel
//      - /api   => ApiKernel
//      - /*     => FrontKernel
//

include_once __DIR__.'/bootstrap.php';

sonata_handle('prod', false)->send();