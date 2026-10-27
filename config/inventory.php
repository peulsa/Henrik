<?php

return [
    'item_types' => [
        'supply' => 'Insumo',
        'spare_part' => 'Repuesto',
        'equipment' => 'Equipo',
    ],
    'tracking_types' => [
        'bulk' => 'Por cantidad',
        'serialized' => 'Por serie / etiqueta',
    ],
    'location_types' => [
        'site' => 'Sede',
        'building' => 'Edificio',
        'floor' => 'Piso',
        'office' => 'Oficina',
        'warehouse' => 'Bodega',
        'zone' => 'Zona',
    ],
    'movement_types' => [
        'receive' => 'Recepción',
        'deliver' => 'Entrega',
        'transfer' => 'Traslado',
        'install' => 'Instalación',
        'remove' => 'Retiro',
        'adjust_in' => 'Ajuste de entrada',
        'adjust_out' => 'Ajuste de salida',
    ],
    'asset_statuses' => [
        'in_stock' => 'En bodega',
        'assigned' => 'Asignado',
        'repair' => 'En reparación',
        'retired' => 'Dado de baja',
    ],
];
