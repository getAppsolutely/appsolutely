<?php

declare(strict_types=1);

use App\Enums\NotificationTriggerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values   = array_map(fn (\BackedEnum $case) => $case->value, NotificationTriggerType::cases());
        $enumList = "'" . implode("','", array_map('addslashes', $values)) . "'";

        DB::statement("ALTER TABLE notification_rules MODIFY trigger_type ENUM({$enumList}) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE notification_rules MODIFY trigger_type VARCHAR(100) NOT NULL');
    }
};
