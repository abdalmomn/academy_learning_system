<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
<<<<<<< HEAD
use Spatie\Permission\PermissionRegistrar;
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        //create roles
        $guestRole = Role::create(['name' => 'guest']);
        $womanRole = Role::create(['name' => 'woman']);
        $childRole = Role::create(['name' => 'child']);
        $teacherRole = Role::create(['name' => 'teacher']);
<<<<<<< HEAD
        $supervisorRole = Role::create(['name' => 'supervisor']);
=======
        $supervisorRole = Role::create(['name' => 'Supervisor']);
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        $adminRole = Role::create(['name' => 'admin']);

        //all app permissions
        $guest_permission = [
            'show_home_page', 'search_for_teacher_or_course','show_categories','filter_categories','show_all_courses','sign_up',
            'show_FAQ'
        ];
        $woman_permission = array_merge($guest_permission, [
            'show_course_information', 'show_leader_board','show_teacher_profile','sign_in','reset_password',
            're-send_code', 'email_verification','choose_interests','logout','show_my_courses','purchase_course',
            'show_video', 'rate_course','comment_on_videos','show_all_comments','delete_comment','take_quick_mcq_quiz',
            'take_mcq_quiz', 'project_submission', 'receive_certificate', 'show_my_enthusiasm', 'request_to_join_training_team',
            'record_attendance', 'show_user_statistics', 'show_my_profile', 'edit_my_profile', 'delete_my_account',
            'show_my_certificates', 'show_my_to_do_list','edit_my_to_do_list', 'delete_my_to_do_list', 'create_to_do_list',
            'change_language','show_my_wallet_points', 'use_promo_code','show_my_result','show_my_transactions',
        ]);
        $child_permission = array_merge($woman_permission,[
            'show_parents_control_panel',
            'use_parent_secret_key',
        ]);
        $teacher_permissions = [
            'delete_course', 'edit_course', 'sign_in', 'sign_up',
            'add_course', 'approve_for_create_course', 'show_insights', 'delete_video', 'edit_video', 'add_video', 'create_mcq_quiz',
            'create_project_quiz', 'delete_quiz', 'edit_quiz',  'upload_results', 'upload_certifications',  'add_my_academy_certifications',
            'create_promo_code',
        ];
        $supervisor_permission = array_merge($teacher_permissions, [
            'restrict comments', 'choose_best_student',
            'distribute_course_supervisor', 'restrict_course_student_count', 'create_supervisor_account',
            'send_notification', 'scheduling_course_time', 'send_alerts', 'ban_account', 'ban_student_from_course',
            'show_all_course_teachers', 'show_all_course_students'
        ]);
        $permissions = array_merge($woman_permission,$child_permission,$supervisor_permission,[
            'show_all_courses', 'show_all_active_courses', 'show_all_expired_courses', 'show_all_on_wait_courses', 'show_all_banned_users',
            'notifications_for_daily_enthusiasm', 'notifications_for_points', 'notifications_for_payments', 'notifications_for_achievements',
            'notifications_for_absence', 'notifications_for_adding_new_courses', 'notifications_for_to_do_lists', 'notifications_for_new_updates',
            'notifications_for_study_time', 'notifications_for_email_verification', 'notifications_for_quizzes_results',
            'notifications_for_certifications', 'notifications_for_alerts', 'add_category', 'edit_category', 'delete_category',
            'add_interest', 'delete_interest', 'show_interests', 'update_interest',
        ]);

        //create permissions
        foreach ($permissions as $permission){
            Permission::findOrCreate($permission,'web');
        }
<<<<<<< HEAD
        // another way to create permissions
//        $permission = Permission::make($permissions);
//        $permission->saveOrFail();

        //assign permissions to roles
=======

        //assign permissions to roels
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        $adminRole->syncPermissions($permissions);
        $womanRole->givePermissionTo($woman_permission);
        $childRole->givePermissionTo($child_permission);
        $teacherRole->givePermissionTo($teacher_permissions);
        $supervisorRole->givePermissionTo($supervisor_permission);
        $guestRole->givePermissionTo($guest_permission);

        //create admin with role and permissions
        $adminUser = User::query()->create([
            'username' => 'admin',
            'email' => 'admin@mail.com',
            'password' => 'admin',
<<<<<<< HEAD
=======
            'role' => 'admin'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $adminUser->assignRole($adminRole);
        $permissions =$adminRole->permissions()->pluck('name')->toArray();
        $adminUser->givePermissionTo($permissions);
        $adminUser['token'] =$adminUser->createToken("token")->plainTextToken;

        //create supervisor with role and permissions
        $supervisorUser = User::query()->create([
            'username' => 'supervisor',
            'email' => 'supervisor@mail.com',
            'password' => 'supervisor',
<<<<<<< HEAD
=======
            'role' => 'supervisor'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $supervisorUser->assignRole($supervisorRole);
        $permissions =$supervisorRole->permissions()->pluck('name')->toArray();
        $supervisorUser->givePermissionTo($permissions);
        $supervisorUser['token'] =$supervisorUser->createToken("token")->plainTextToken;


        //create woman user with role and permissions
        $womanUser = User::query()->create([
            'username' => 'woman',
            'email' => 'woman@mail.com',
            'password' => 'woman',
<<<<<<< HEAD
=======
            'role' => 'woman'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $womanUser->assignRole($womanRole);
        $permissions =$womanRole->permissions()->pluck('name')->toArray();
        $womanUser->givePermissionTo($permissions);
        $womanUser['token'] =$womanUser->createToken("token")->plainTextToken;



        //create child user with role and permissions
        $childUser = User::query()->create([
            'username' => 'child',
            'email' => 'child@mail.com',
            'password' => 'child',
<<<<<<< HEAD
=======
            'role' => 'child'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $childUser->assignRole($childRole);
        $permissions =$childRole->permissions()->pluck('name')->toArray();
        $childUser->givePermissionTo($permissions);
        $childUser['token'] =$childUser->createToken("token")->plainTextToken;


        //create guest user with role and permissions
        $guestUser = User::query()->create([
            'username' => 'guest',
            'email' => 'guest@mail.com',
            'password' => 'guest',
<<<<<<< HEAD
=======
            'role' => 'guest'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $guestUser->assignRole($guestRole);
        $permissions =$guestRole->permissions()->pluck('name')->toArray();
        $guestUser->givePermissionTo($permissions);
        $guestUser['token'] =$guestUser->createToken("token")->plainTextToken;


        //create teacher user with role and permissions
        $teacherUser = User::query()->create([
            'username' => 'teacher',
            'email' => 'teacher@mail.com',
            'password' => 'teacher',
<<<<<<< HEAD
=======
            'role' => 'teacher'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);

        $teacherUser->assignRole($teacherRole);
        $permissions =$teacherRole->permissions()->pluck('name')->toArray();
        $teacherUser->givePermissionTo($permissions);
        $teacherUser['token'] = $teacherUser->createToken("token")->plainTextToken;

    }
}
