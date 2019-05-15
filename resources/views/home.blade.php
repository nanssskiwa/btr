@extends('layouts.app')
@section('content')
    @if(Auth::check())
        @if($return)
            @if($return!=='True')
                <div class="alert alert-danger" role="alert">
                    {{ $return }}
                </div>
            @else
                <div class="alert alert-success" role="alert">
                    Action was executed correctly
                </div>
            @endif
        @endif
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        <form method="post" action="{{ route('execute') }}" id="form">
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="value">Value</label>
                <input type="text" id="value" class="form-control" name="value"
                       placeholder="Value"/>
            </div>
            <button type="submit" name="action" class="btn btn-primary" value="add">Add</button>
            <button type="submit" name="action" class="btn btn-danger" value="remove">Remove</button>
        </form>
        @if($tree)
            <table cellpadding="0" cellspacing="0" width="100%" border="0">
                @for($i = 0; $i < $deep; $i++)
                    <tr>
                        @for($j = $deep - $i - 1; $j< 2 * $deep; $j++)
                            <td class="node">{{$tree[0]->value}} - {{$tree[0]->username}}</td>
                            @if($j>2*$i+1)
                                @php break; @endphp
                            @endif
                        @endfor
                    </tr>
                @endfor
            </table>
        @endif
    @else
        <div class="alert alert-danger" role="alert">
            You must be logged in
        </div>
        <a href="{{route('login')}}">Login</a>
        <a href="{{route('register')}}">Register</a>
    @endif
@endsection
