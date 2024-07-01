<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): QueryBuilder | Builder{
        return $query->where("title","like","%". $title ."%");
    }
    
    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null):QueryBuilder | Builder{
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter( $q, $from, $to)
            ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null):QueryBuilder | Builder{
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter( $q, $from, $to)
            ], 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null):QueryBuilder | Builder{
        return $query->withReviewsCount()
            ->orderBy("reviews_count","desc");
    }

    public function scopeBestRated(Builder $query, $from = null, $to = null):QueryBuilder | Builder{
        return $query->withAvgRating()
            ->orderBy('reviews_avg_rating','desc');
    }
    public function scopeMinReviews(Builder $query, int $numReviews):QueryBuilder | Builder {
        return $query->having('reviews_count','>=', $numReviews);
    }

    public function scopePopularLastMonth(Builder $query): Builder | QueryBuilder {
        return $query->popular(now()->subMonths(), now())
            ->bestRated(now()->subMonths(1), now())
            ->minReviews(2);
        }
    public function scopePopularLast6Month(Builder $query): Builder | QueryBuilder {
        return $query->popular(now()->subMonths(6), now())
            ->bestRated(now()->subMonths(6), now())
            ->minReviews(2);
        }     

    public function scopeBestRatedLastMonth(Builder $query): Builder | QueryBuilder {
        return $query->bestRated(now()->subMonths(), now())
            ->popular(now()->subMonths(), now())
            ->minReviews(2);
        }  
    public function scopeBestRatedLast6Month(Builder $query): Builder | QueryBuilder {
        return $query->bestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(2);
        }  

    private function dateRangeFilter(Builder $query, $from = null, $to = null){
        if ($from && !$to){
            $query->where('created_at','>= ',$from);
        } elseif (!$from && $to) {
            $query->where('created_at','<= ',$to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }
    protected static function booted()
    {
        static::updated(
            fn (Book $book) => cache()->forget('book:'. $book->id)
        );
        static::deleted(
            fn (Book $book) => cache()->forget('book:'. $book->id)
        );
    }
    
    
}
