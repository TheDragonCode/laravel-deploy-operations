<?php

namespace Helldar\LaravelActions\Support;

use Illuminate\Support\Str;

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
        foreach ($this->available as $search => $replace) {
            if (Str::contains($value, $search)) {
                return str_replace($search, $replace, $value);
            }
        }

        return $value;
    }
}
