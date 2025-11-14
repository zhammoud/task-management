<span>A new comment was added on task: <b>{{$task->name}}</b>, ID: <b>{{$task->id}}</b></span>
<br>
<br>
Comment preview:<br>
<pre>{{substr($comment->body, 0,1024)}}{{strlen($comment->body) > 1024 ? '...' : ''}}</pre>
<br>
Thanks,<br>
{{ config('app.name') }}
