<?php

namespace Helldar\LaravelActions\Support;

final class Information
{
    protected $available = [
        'Migrated:'                            => 'Done:',
        'Migrating:'                           => 'Running:',
        'Migration not found'                  => 'Action not found',
        'Migration table created successfully' => 'Action table created successfully',
        'Migration table not found'            => 'Actions table not found',
        'No migrations found'                  => 'No actions found',
        'Nothing to migrate'                   => 'Nothing to do',
    ];

    public function replace(string $value): string
    {
        return str_replace(
            array_keys($this->available),
            array_values($this->available),
            $value
        );
    }
}
