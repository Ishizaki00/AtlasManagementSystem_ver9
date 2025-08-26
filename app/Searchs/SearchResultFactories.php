<?php
namespace App\Searchs;

use App\Models\Users\User;

class SearchResultFactories{

  // 改修課題：選択科目の検索機能
  public function initializeUsers($keyword, $category, $updown, $gender, $role, $subjects){
    if($category == 'name'){
      if(is_null($subjects)){
        $searchResults = new SelectNames();
      }else{
        $searchResults = new SelectNameDetails();
      }
      return $searchResults->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }else if($category == 'id'){
      if(is_null($subjects)){
        $searchResults = new SelectIds();
      }else{
        $searchResults = new SelectIdDetails();
      }
      return $searchResults->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }else{
      $allUsers = new AllUsers();
    return $allUsers->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }
  }
}
class SelectNameDetails {
    public function resultUsers($keyword, $category, $updown, $gender, $role, $subjects)
    {
        $query = User::query()->with('subjects');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('over_name', 'like', "%$keyword%")
                  ->orWhere('under_name', 'like', "%$keyword%")
                  ->orWhere('over_name_kana', 'like', "%$keyword%")
                  ->orWhere('under_name_kana', 'like', "%$keyword%");
            });
        }

        if (!empty($gender)) {
            $query->where('sex', $gender);
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($subjects)) {
            $query->whereHas('subjects', function ($q) use ($subjects) {
                $q->whereIn('subjects.id', $subjects);
            });
        }

        return $query->orderBy('id', $updown)->get();
    }
}
