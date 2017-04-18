<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id'];

    public function comments()
    {
    	return $this->hasMany(Comment::class);		
    }

    public function user()
    {
    	return $this->belongsTo(User::class);		
    }

    public function addComment($body)
    {
    	$this->comments()->create([
            'body'    => $body,
            'user_id' => auth()->id()
        ]);
    }

    public function commentsLatest()
    {
        return $this->comments()->latest()->get();       
    }
    
    public function scopeFilter($query, $filters)
    {
        if ($month = (int) $filters['month']) {
            $query->whereMonth('created_at', $month);
        }

        if ($year = (int) $filters['year']) {
            $query->whereYear('created_at', $year);
        }
    }

    public static function archives()
    {
        return static::selectRaw('year(created_at) year, 
                                  monthName(created_at) monthName, 
                                  month(created_at) month,
                                  count(*) published')
                        ->groupBy('year', 'monthName', 'month')
                        ->orderByRaw('min(created_at) desc')
                        ->get()
                        ->toArray();        
    }
}
