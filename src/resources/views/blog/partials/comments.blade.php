{{-- blog/partials/comments.blade.php --}}
@foreach($comments as $comment)
<div class="border-l-2 border-gray-200 pl-4">
    <div class="flex items-start space-x-3">
        <img src="{{ $comment->author_avatar }}" 
             alt="{{ $comment->author_name }}"
             class="w-10 h-10 rounded-full">
        <div class="flex-1">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium">{{ $comment->author_name }}</h4>
                    <time class="text-sm text-gray-500">{{ $comment->formatted_date }}</time>
                </div>
                <p class="text-gray-700">{{ $comment->content }}</p>
            </div>
            
            <div class="mt-2 flex items-center space-x-4">
                <button onclick="voteComment({{ $comment->id }}, 'like')" 
                        class="text-sm text-gray-500 hover:text-green-600">
                    <i class="far fa-thumbs-up"></i>
                    <span id="like-count-{{ $comment->id }}">{{ $comment->likes }}</span>
                </button>
                <button onclick="voteComment({{ $comment->id }}, 'dislike')" 
                        class="text-sm text-gray-500 hover:text-red-600">
                    <i class="far fa-thumbs-down"></i>
                    <span id="dislike-count-{{ $comment->id }}">{{ $comment->dislikes }}</span>
                </button>
                <button onclick="replyToComment({{ $comment->id }}, '{{ $comment->author_name }}')" 
                        class="text-sm text-blue-600 hover:underline">
                    Trả lời
                </button>
            </div>
            
            @if($comment->approvedReplies->count() > 0)
            <div class="mt-4 space-y-4">
                @include('blog.partials.comments', ['comments' => $comment->approvedReplies])
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach