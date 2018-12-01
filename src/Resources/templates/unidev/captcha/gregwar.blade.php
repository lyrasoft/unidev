{{-- Part of earth project. --}}
<div id="{{ $attrs['id'] }}-wrapper" class="c-gregwar-captcha d-flex">
    <img class="c-gregwar-captcha__image" src="{{ $router->to('_captcha_image', ['t' => time(), 'profile' => $options['profile']]) }}" alt="Captcha"
        data-captcha-image data-src="{{ $router->to('_captcha_image', ['profile' => $options['profile']]) }}">
    <button type="button" class="btn btn-link c-gregwar-captcha__button" data-captcha-refresh>
        <span class="fa fa-sync"></span>
        @lang('unidev.captcha.gregwar.button.refresh')
    </button>
    {!! new \Windwalker\Dom\HtmlElement('input', null, $attrs) !!}
</div>
