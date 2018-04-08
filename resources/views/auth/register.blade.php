@extends('layouts.app') @section('content')

<main class="section">
    <section class="container">
    {{ Breadcrumbs::render('register') }}
        <div class="columns">
            <div class="column is-half-desktop is-offset-one-quarter-desktop has-text-centered">
                <h2 class="title is-2">Register</h2>
                @if (!\App\Http\Middleware\RedirectIfNoAdmin::adminsAreAlreadySet())
                <div class="notification is-info">Thanks for installing Dirtiki! This first account will become the <em>de facto</em> admin.</div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <dirtiki-input :should-hard-validate-on-blur="true" :required="true" label="Name" name="name" value="{{ old('name') }}"  
                    @if ($errors->has('name'))
                     :error-messages="['{{$errors->first('name')}}']" 
                    @endif
                    >
                    </dirtiki-input>

                    <dirtiki-input type="email" :should-hard-validate-on-blur="true"  :required="true"  label="Email address" name="email" value="{{ old('email') }}"
                        @if ($errors->has('email')) 
                        :error-messages="['{{$errors->first('email')}}']" 
                        @endif
                        
                        @if (Setting::get('users.signup_domains', null))
                            @php
                                $signupDomains = preg_replace('/\s+/', '', Setting::get('users.signup_domains', ""));
                                $signupDomains = explode(",", $signupDomains);
                            @endphp
                            :must-end-with="[@foreach($signupDomains as $domain)'{{ $domain }}'@endforeach
                            ]"
                        @endif
                        >
                    </dirtiki-input>

                    <dirtiki-input label="Password" name="password"  :required="true"  type="password"
                    @if ($errors->has('password')) 
                    :error-messages="['{{$errors->first('password')}}']"
                     @endif >
                    </dirtiki-input>

                    <dirtiki-input label="Confirm Password" name="password_confirmation"  :required="true"  type="password" 
                    @if ($errors->has('password_confirmation')) 
                    :error-messages="['{{$errors->first('password_confirmation')}}']" 
                    @endif >
                    </dirtiki-input>

                    <div class="field">
                        <p class="control">
                            <button type="submit" class="button is-primary">
                                Register
                            </button>
                        </p>
                    </div>


                </form>

            </div>
        </div>
    </section>
</main>
@endsection