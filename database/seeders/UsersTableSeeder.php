<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Users\User;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        'over_name' => '石﨑',
        'under_name' => '貴幸',
        'over_name_kana' => 'イシザキ',
        'under_name_kana' => 'タカユキ',
        'mail_address' => 'griranse@gmail.com',
        'sex' => '1',
        'birth_day' => '1998-01-06',
         'role' => '1',
        'password' => bcrypt('zef2efn7'), // ハッシュ化されたパスワード
        ]);
    }
}
