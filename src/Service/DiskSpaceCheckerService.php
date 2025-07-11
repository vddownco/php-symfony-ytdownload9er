<?php

declare(strict_types=1);

namespace App\Service;

use App\Helper\Helper;

class DiskSpaceCheckerService
{
    public function getFreeSpace(): array
    {
        $free  = disk_free_space('/');
        $total = disk_total_space('/');
        $used  = $total - $free;

        return [
            'free'       => Helper::formatBytes($free),
            'used'       => Helper::formatBytes($used),
            'total'      => Helper::formatBytes($total),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }
}
