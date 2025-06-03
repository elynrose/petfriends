<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [ 'id' => 1,  'name' => 'user_management_access', 'guard_name' => 'web'],
            [ 'id' => 2,  'name' => 'permission_create', 'guard_name' => 'web'],
            [ 'id' => 3,  'name' => 'permission_edit', 'guard_name' => 'web'],
            [ 'id' => 4,  'name' => 'permission_show', 'guard_name' => 'web'],
            [ 'id' => 5,  'name' => 'permission_delete', 'guard_name' => 'web'],
            [ 'id' => 6,  'name' => 'permission_access', 'guard_name' => 'web'],
            [ 'id' => 7,  'name' => 'role_create', 'guard_name' => 'web'],
            [ 'id' => 8,  'name' => 'role_edit', 'guard_name' => 'web'],
            [ 'id' => 9,  'name' => 'role_show', 'guard_name' => 'web'],
            [ 'id' => 10, 'name' => 'role_delete', 'guard_name' => 'web'],
            [ 'id' => 11, 'name' => 'role_access', 'guard_name' => 'web'],
            [ 'id' => 12, 'name' => 'user_create', 'guard_name' => 'web'],
            [ 'id' => 13, 'name' => 'user_edit', 'guard_name' => 'web'],
            [ 'id' => 14, 'name' => 'user_show', 'guard_name' => 'web'],
            [ 'id' => 15, 'name' => 'user_delete', 'guard_name' => 'web'],
            [ 'id' => 16, 'name' => 'user_access', 'guard_name' => 'web'],
            [ 'id' => 17, 'name' => 'pet_create', 'guard_name' => 'web'],
            [ 'id' => 18, 'name' => 'pet_edit', 'guard_name' => 'web'],
            [ 'id' => 19, 'name' => 'pet_show', 'guard_name' => 'web'],
            [ 'id' => 20, 'name' => 'pet_delete', 'guard_name' => 'web'],
            [ 'id' => 21, 'name' => 'pet_access', 'guard_name' => 'web'],
            [ 'id' => 22, 'name' => 'booking_create', 'guard_name' => 'web'],
            [ 'id' => 23, 'name' => 'booking_edit', 'guard_name' => 'web'],
            [ 'id' => 24, 'name' => 'booking_show', 'guard_name' => 'web'],
            [ 'id' => 25, 'name' => 'booking_delete', 'guard_name' => 'web'],
            [ 'id' => 26, 'name' => 'booking_access', 'guard_name' => 'web'],
            [ 'id' => 27, 'name' => 'pet_review_create', 'guard_name' => 'web'],
            [ 'id' => 28, 'name' => 'pet_review_edit', 'guard_name' => 'web'],
            [ 'id' => 29, 'name' => 'pet_review_show', 'guard_name' => 'web'],
            [ 'id' => 30, 'name' => 'pet_review_delete', 'guard_name' => 'web'],
            [ 'id' => 31, 'name' => 'pet_review_access', 'guard_name' => 'web'],
            [ 'id' => 32, 'name' => 'chat_create', 'guard_name' => 'web'],
            [ 'id' => 33, 'name' => 'chat_edit', 'guard_name' => 'web'],
            [ 'id' => 34, 'name' => 'chat_show', 'guard_name' => 'web'],
            [ 'id' => 35, 'name' => 'chat_delete', 'guard_name' => 'web'],
            [ 'id' => 36, 'name' => 'chat_access', 'guard_name' => 'web'],
            [ 'id' => 37, 'name' => 'user_alert_create', 'guard_name' => 'web'],
            [ 'id' => 38, 'name' => 'user_alert_show', 'guard_name' => 'web'],
            [ 'id' => 39, 'name' => 'user_alert_delete', 'guard_name' => 'web'],
            [ 'id' => 40, 'name' => 'user_alert_access', 'guard_name' => 'web'],
            [ 'id' => 41, 'name' => 'support_create', 'guard_name' => 'web'],
            [ 'id' => 42, 'name' => 'support_edit', 'guard_name' => 'web'],
            [ 'id' => 43, 'name' => 'support_show', 'guard_name' => 'web'],
            [ 'id' => 44, 'name' => 'support_delete', 'guard_name' => 'web'],
            [ 'id' => 45, 'name' => 'support_access', 'guard_name' => 'web'],
            [ 'id' => 46, 'name' => 'email_log_create', 'guard_name' => 'web'],
            [ 'id' => 47, 'name' => 'email_log_edit', 'guard_name' => 'web'],
            [ 'id' => 48, 'name' => 'email_log_show', 'guard_name' => 'web'],
            [ 'id' => 49, 'name' => 'email_log_delete', 'guard_name' => 'web'],
            [ 'id' => 50, 'name' => 'email_log_access', 'guard_name' => 'web'],
            [ 'id' => 51, 'name' => 'spam_ip_create', 'guard_name' => 'web'],
            [ 'id' => 52, 'name' => 'spam_ip_edit', 'guard_name' => 'web'],
            [ 'id' => 53, 'name' => 'spam_ip_show', 'guard_name' => 'web'],
            [ 'id' => 54, 'name' => 'spam_ip_delete', 'guard_name' => 'web'],
            [ 'id' => 55, 'name' => 'spam_ip_access', 'guard_name' => 'web'],
            [ 'id' => 56, 'name' => 'profile_password_edit', 'guard_name' => 'web'],
        ];

        Permission::insert($permissions);
    }
}
