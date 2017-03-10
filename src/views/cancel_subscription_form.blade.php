<?php
/**
 * Created by PhpStorm.
 * User: camronwood
 * Date: 3/8/17
 * Time: 2:37 PM
 */
?>

@if (!empty($limeLightOrderView))
    <div>
        <h4>Subscription Information</h4>
    </div>

    {{ Form::open(array('url' => '/subscription/cancel')) }}

    {{ csrf_field() }}

    <div class="form-group row">
        <div class="col-xs-12 col-lg-9 push-lg-3">
            {{ Form::submit('Cancel Subscription', ['id' => 'submit2', 'class' => 'btn btn-danger']) }}
        </div>

        @if (session('unsubscribeSuccess'))
            <div class="alert alert-success">
                {{ session('unsubscribeSuccess') }}
            </div>
        @endif

        @if ($errors->has('unsubscribeError'))
            <span class="help-block">
                <strong>{{ $errors->get('unsubscribeError')[0] }}</strong>
             </span>
        @endif
    </div>
    {{ Form::close() }}
@endif
