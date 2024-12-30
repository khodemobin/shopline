<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (! function_exists('get_value_enums')) {
    /**
     * Get value from enums file.
     *
     *
     * @return array
     */
    function get_value_enums(array $data)
    {
        $values = [];

        foreach ($data as $value) {
            $values[] = $value->value;
        }

        return $values;
    }
}

if (! function_exists('startWith')) {
    /**
     * Check start with character.
     *
     *
     * @return bool
     */
    function startWith(string $string, string $startString)
    {
        return str_starts_with($string, $startString);
    }
}

if (! function_exists('updateDatabaseTableSequence')) {
    function updateDatabaseTableSequence($tableName = null): bool
    {
        $sequenceNames = [];

        if ($tableName) {
            $sn = strtolower($tableName).'_id_seq';
            $sql = <<<'SQL'
                select sequence_schema, sequence_name
                from information_schema.sequences
                where sequence_name = ? ;
            SQL;
            $checkExists = DB::selectOne($sql, [$sn]);
            if (! empty($checkExists->sequence_name)) {
                $sequenceNames = [$sn];
            }
        } else {
            $sql = <<<'SQL'
                select sequence_schema, sequence_name
                from information_schema.sequences
            SQL;
            $sequences = DB::select($sql);
            $sequenceNames = Arr::pluck($sequences, 'sequence_name');
        }

        foreach ($sequenceNames as $sequenceName) {
            $tableName = Str::before($sequenceName, '_id_seq');
            if (! str_contains($tableName, 'telescope')) {
                $max = DB::table($tableName)->max('id') + 1;
                DB::statement("ALTER SEQUENCE $sequenceName RESTART WITH {$max}");
            }
        }

        return true;
    }
}
