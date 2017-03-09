@if (!empty($limeLightOrderView))

    {{ Form::open(['route' => 'updateSubscription']) }}

    <div class="row">
        <div class="col-xl-12">

            <div>
                <h4>Billing Information</h4>
            </div>

            @if (session('paymentSuccess'))
                <div class="alert alert-success">
                    {{ session('paymentSuccess') }}
                </div>
            @endif

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('phone', 'Phone *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-phone text-primary"></i></span>
                         {{ Form::text('phone', $limeLightOrderView['customers_telephone'], ['id' => 'phone', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('phone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('address', 'Address *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('address', $limeLightOrderView['billing_street_address'], ['id' => 'address', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('address'))
                    <span class="help-block">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('city', 'City *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('city', $limeLightOrderView['billing_city'], ['id' => 'city', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('city'))
                    <span class="help-block">
                        <strong>{{ $errors->first('city') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('state', 'State *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::select('state', config('limelight-payments.states'), $limeLightOrderView['billing_state'], ['id' => 'state', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('state'))
                    <span class="help-block">
                        <strong>{{ $errors->first('state') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('cc-number', 'CC Number *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{
                            Form::text('cc-number',
                                '',
                                [
                                    'id' => 'cc-number',
                                    'class' => 'form-control',
                                    'placeholder' => $limeLightOrderView['credit_card_number']
                                ]
                            )
                        }}
                    </div>
                </div>
                @if ($errors->has('cc-number'))
                    <span class="help-block">
                        <strong>{{ $errors->first('cc-number') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('cc-expires', 'CC Expires *', ['class' => 'form-control-label']) }}
                </div>

                <div class="col-xs-6 col-xl-3 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('cc-expires-month', $limeLightOrderView['cc_expires_month'], ['id' => 'cc-expires-month', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-xs-6 col-xl-3 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('cc-expires-year', $limeLightOrderView['cc_expires_year'], ['id' => 'cc-expires-year', 'class' => 'form-control']) }}
                    </div>
                </div>

                @if ($errors->has('cc-expires-month'))
                    <span class="help-block">
                        <strong>{{ $errors->first('cc-expires-month') }}</strong>
                    </span>
                @endif
                @if ($errors->has('cc-expires-year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('cc-expires-year') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('cc-cvv', 'CVV *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('cc-cvv', '', ['id' => 'cc-cvv', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('cc-cvv'))
                    <span class="help-block">
                        <strong>{{ $errors->first('cc-cvv') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-3 text-lg-right">
                    {{ Form::label('zip-code', 'Zip Code *', ['class' => 'form-control-label']) }}
                </div>
                <div class="col-xs-12 col-xl-6 col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plus text-primary"></i></span>
                        {{ Form::text('zip-code', $limeLightOrderView['billing_postcode'], ['id' => 'zip-code', 'class' => 'form-control']) }}
                    </div>
                </div>
                @if ($errors->has('zip-code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('zip-code') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-lg-9 push-lg-3">
                    {{ Form::submit('Update Payment', ['class' => 'btn btn-primary']) }}
                </div>
            </div>

        </div>
    </div>
    {{ Form::close() }}
@endif