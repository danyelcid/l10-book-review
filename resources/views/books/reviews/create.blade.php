@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">
        Add a review for {{$book->title}}
    </h1>
    <form action="{{route('books.reviews.store' , $book)}}" method="POST">
        @csrf

        <label for="review">Review</label>
        <textarea name="review" id="review" required class="input mb-4"></textarea>
        @error('review')
            <p class="error"> {{$message}} </p>
        @enderror
        <label for="rating">Select a rating</label>
        <select name="rating" id="rating" class="input mb-4">
                        @for ($i = 1; $i<= 5; $i++)
                <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>
        @error('rating')
            <p class="error"> {{$message}} </p>
        @enderror
        <button type="submit" class="btn">Send</button>
    </form>

@endsection