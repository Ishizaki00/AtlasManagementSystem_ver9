<?php
namespace App\Searchs;

use App\Models\Users\User;

class AllUsers implements DisplayUsers {

  public function resultUsers($keyword, $category, $updown, $gender, $role, $subjects) {
    if (is_null($gender)) {
      $gender = ['1', '2', '3'];
    } else {
      $gender = [$gender];
    }

    if (is_null($role)) {
      $role = ['1', '2', '3', '4'];
    } else {
      $role = [$role];
    }

    $query = User::with('subjects')
      ->whereIn('sex', $gender)
      ->whereIn('role', $role);

    if (!is_null($subjects)) {
      $query->whereHas('subjects', function ($q) use ($subjects) {
        $q->where('subjects.id', $subjects);
      });
    }

    $updown = strtolower(trim($updown)); // ← trimで空白除去も追加！

if (!in_array($updown, ['asc', 'desc'])) {
    $updown = 'asc'; // ← デフォルト値
}

    return $query->orderBy('id', strtolower($updown))->get();

  }
}
