<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin'    => ['chief_editor', 'editor', 'reviewer', 'layout_editor'],
            'editor'   => ['editor', 'reviewer'],
            'reviewer' => ['reviewer'],
        ];

        $now = now();

        foreach ($map as $role => $caps) {
            $users = DB::table('users')->where('role', $role)->pluck('id');
            foreach ($users as $userId) {
                foreach ($caps as $cap) {
                    DB::table('editorial_capabilities')->insertOrIgnore([
                        'user_id'             => $userId,
                        'capability'          => $cap,
                        'granted_by_user_id'  => null,
                        'granted_at'          => $now,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Irréversible par design.
    }
};
