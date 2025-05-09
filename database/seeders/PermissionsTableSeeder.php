<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'pet_create',
            ],
            [
                'id'    => 18,
                'title' => 'pet_edit',
            ],
            [
                'id'    => 19,
                'title' => 'pet_show',
            ],
            [
                'id'    => 20,
                'title' => 'pet_delete',
            ],
            [
                'id'    => 21,
                'title' => 'pet_access',
            ],
            [
                'id'    => 22,
                'title' => 'booking_create',
            ],
            [
                'id'    => 23,
                'title' => 'booking_edit',
            ],
            [
                'id'    => 24,
                'title' => 'booking_show',
            ],
            [
                'id'    => 25,
                'title' => 'booking_delete',
            ],
            [
                'id'    => 26,
                'title' => 'booking_access',
            ],
            [
                'id'    => 27,
                'title' => 'pet_review_create',
            ],
            [
                'id'    => 28,
                'title' => 'pet_review_edit',
            ],
            [
                'id'    => 29,
                'title' => 'pet_review_show',
            ],
            [
                'id'    => 30,
                'title' => 'pet_review_delete',
            ],
            [
                'id'    => 31,
                'title' => 'pet_review_access',
            ],
            [
                'id'    => 32,
                'title' => 'chat_create',
            ],
            [
                'id'    => 33,
                'title' => 'chat_edit',
            ],
            [
                'id'    => 34,
                'title' => 'chat_show',
            ],
            [
                'id'    => 35,
                'title' => 'chat_delete',
            ],
            [
                'id'    => 36,
                'title' => 'chat_access',
            ],
            [
                'id'    => 37,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 38,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 39,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 40,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 41,
                'title' => 'support_create',
            ],
            [
                'id'    => 42,
                'title' => 'support_edit',
            ],
            [
                'id'    => 43,
                'title' => 'support_show',
            ],
            [
                'id'    => 44,
                'title' => 'support_delete',
            ],
            [
                'id'    => 45,
                'title' => 'support_access',
            ],
            [
                'id'    => 46,
                'title' => 'email_log_create',
            ],
            [
                'id'    => 47,
                'title' => 'email_log_edit',
            ],
            [
                'id'    => 48,
                'title' => 'email_log_show',
            ],
            [
                'id'    => 49,
                'title' => 'email_log_delete',
            ],
            [
                'id'    => 50,
                'title' => 'email_log_access',
            ],
            [
                'id'    => 51,
                'title' => 'spam_ip_create',
            ],
            [
                'id'    => 52,
                'title' => 'spam_ip_edit',
            ],
            [
                'id'    => 53,
                'title' => 'spam_ip_show',
            ],
            [
                'id'    => 54,
                'title' => 'spam_ip_delete',
            ],
            [
                'id'    => 55,
                'title' => 'spam_ip_access',
            ],
            [
                'id'    => 56,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
