<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // mobile
            if (!Schema::hasColumn('users', 'mobile_verify_code')) {
                $table->string('mobile_verify_code')->nullable()->default(null)->after('phone');
            }

            if (!Schema::hasColumn('users', 'mobile_verified_at')) {
                $table->timestamp('mobile_verified_at')->nullable()->default(null)->after('mobile_verify_code');
            }

            if (!Schema::hasColumn('users', 'mobile_attempts_left')) {
                $table->tinyInteger('mobile_attempts_left')->nullable()->default(null)->after('mobile_verified_at');
            }

            if (!Schema::hasColumn('users', 'mobile_verify_code_sent_at')) {
                $table->timestamp('mobile_verify_code_sent_at')->nullable()->default(null)->after('mobile_attempts_left');
            }
            if (!Schema::hasColumn('users', 'mobile_last_attempt_date')) {
                $table->timestamp('mobile_last_attempt_date')->nullable()->default(null)->after('mobile_verify_code_sent_at');
            }



            // email
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->default(null)->after('email');
            }

            if (!Schema::hasColumn('users', 'email_verify_code')) {
                $table->string('email_verify_code')->nullable()->default(null)->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'email_attempts_left')) {
                $table->tinyInteger('email_attempts_left')->nullable()->default(null)->after('email_verify_code');
            }

            if (!Schema::hasColumn('users', 'email_verify_code_sent_at')) {
                $table->timestamp('email_verify_code_sent_at')->nullable()->default(null)->after('email_attempts_left');
            }
            if (!Schema::hasColumn('users', 'email_last_attempt_date')) {
                $table->timestamp('email_last_attempt_date')->nullable()->default(null)->after('email_verify_code_sent_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile_verify_code');
            $table->dropColumn('mobile_verified_at');
            $table->dropColumn('mobile_attempts_left');
            $table->dropColumn('mobile_verify_code_sent_at');

            $table->dropColumn('email_verified_at');
            $table->dropColumn('email_verify_code');
            $table->dropColumn('email_attempts_left');
            $table->dropColumn('email_verify_code_sent_at');
            $table->dropColumn('email_last_attempt_date');

        });
    }
};
