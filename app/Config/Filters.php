<?php
namespace Config;

use App\Filters\AuthFilter;
use App\Filters\ManagerFilter;
use App\Filters\UnitAccessFilter;
use App\Filters\SecurityHeaders;
use App\Filters\SuperAdminFilter;
use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    public array $aliases = [
        'auth'        => AuthFilter::class,          // Oturum kontrolü
        'manager'     => ManagerFilter::class,       // Yönetici veya süper yönetici filtre
        'unitAccess'  => UnitAccessFilter::class,    // Birim bazlı erişim
        'secHeaders'  => SecurityHeaders::class,     // Güvenlik başlıkları
        'superadmin'  => SuperAdminFilter::class,    // Sadece süper yönetici erişimi
    ];

    public array $globals = [
        'before' => ['secHeaders'],
        'after'  => [],
    ];

    public array $filters = [
    'csrf' => [
        'except' => [
            'meetings/updateParticipantStatus'
        ],
    ],
];

}
