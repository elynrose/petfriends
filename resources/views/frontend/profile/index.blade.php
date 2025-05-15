                        <div class="form-group">
                            @if(auth()->user()->canUseSmsNotifications())
                                <div>
                                    <input type="hidden" name="sms_notifications" value="0">
                                    <input type="checkbox" name="sms_notifications" id="sms_notifications" value="1" {{ old('sms_notifications', $user->sms_notifications) == 1 ? 'checked' : '' }}>
                                    <label for="sms_notifications">{{ trans('cruds.user.fields.sms_notifications') }}</label>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-crown"></i> SMS notifications are a Premium feature. 
                                    <a href="{{ route('frontend.subscription.index') }}" class="alert-link">Upgrade to Premium</a> to enable SMS notifications!
                                </div>
                            @endif
                            @if($errors->has('sms_notifications'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('sms_notifications') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.sms_notifications_helper') }}</span>
                        </div> 