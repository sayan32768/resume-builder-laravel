<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $columns = Schema::getColumnListing('users');

            $toDrop = collect([
                'token',
                'otp',
                'otpExpiry',
                'email_verified_at',
                'remember_token',
            ])->filter(fn($col) => in_array($col, $columns))->toArray();

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable();
            $table->string('otp')->nullable();
            $table->dateTime('otpExpiry')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });
    }
};
