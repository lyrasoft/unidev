{{-- Part of earth project. --}}
<?php
/**
 * @var $attrs   array
 * @var $options array
 */
$route = isset($options['route']) ? $options['route'] : null;

$inputClass = 'c-gregwar-captcha__input';
$attrs['class'] = isset($attrs['class']) ? $attrs['class'] .= ' ' . $inputClass : $inputClass;
$attrs['data-captcha-input'] = true;
?>
<div id="{{ $attrs['id'] }}-wrapper" class="c-gregwar-captcha d-flex">
    <img class="c-gregwar-captcha__image" src="{{ $route ?: $router->to('_captcha_image', ['t' => time(), 'profile' => $options['profile']]) }}" alt="Captcha"
        data-captcha-image data-image="{{ $route ?: $router->to('_captcha_image', ['profile' => $options['profile']]) }}">
    <button type="button" class="btn btn-link c-gregwar-captcha__button" data-captcha-refresh>
        <span class="fa fa-sync c-gregwar-captcha__refresh-icon" data-refresh-icon></span>
        @lang('unidev.captcha.gregwar.button.refresh')
    </button>
    {!! new \Windwalker\Dom\HtmlElement('input', null, $attrs) !!}
</div>
