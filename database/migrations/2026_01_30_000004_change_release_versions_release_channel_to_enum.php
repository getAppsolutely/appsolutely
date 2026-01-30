<?php

declare(strict_types=1);

use App\Enums\ReleaseChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values   = array_map(fn (\BackedEnum $case) => $case->value, ReleaseChannel::cases());
        $enumList = "'" . implode("','", array_map('addslashes', $values)) . "'";

        DB::statement("ALTER TABLE release_versions MODIFY release_channel ENUM({$enumList}) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE release_versions MODIFY release_channel VARCHAR(255) NULL');
    }
};
