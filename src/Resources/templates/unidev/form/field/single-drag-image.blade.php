{{-- Part of virtualset project. --}}
<?php
/**
 * @var $field    \Lyrasoft\Unidev\Field\SingleImageDragField
 * @var $crop     bool
 * @var $attrs    array
 * @var $version  int
 * @var $options  array
 */

$packageName = $app->packageResolver->getAlias(\Lyrasoft\Unidev\UnidevPackage::class);
$defaultImage = isset($defaultImage) ? $defaultImage : $asset->path . '/' . $packageName . '/images/default-img.png';
$image = $attrs['value'] ? $attrs['value'] : e($defaultImage);
$suffix = $field->get('version_suffix', '?');

if ($suffix === '?' && strpos($image, '?') !== false) {
    $suffix = '&';
}

$image .= $suffix . uniqid();
?>
@if (WINDWALKER_DEBUG && $version === 1 && !$field->get('force_v1', false))
    <div class="alert alert-warning">
        You are using Single Image Drag v1, please convert to v2 soon.
    </div>
@endif
<div id="{{ $attrs['id'] }}-wrap">

    <div class="sid-row">
        <div class="sid-left-col">
            <img class="sid-preview img-responsive img-fluid"
                 {{-- TextField has escaped value, so we don't need to escape again --}}
                 src="{!! $image !!}"
                 alt="Preview">
        </div>
        @if (!$field->get('readonly') && !$field->get('disabled'))
            <div class="sid-right-col">
                <div class="sid-area filedrag">
                    <p class="sid-upload-actions">
                        <button class="btn btn-success btn-sm btn-xs sid-file-select-button" type="button">
                            <span class="fa fa-picture-o"></span>
                            @translate('unidev.field.single.image.button.select')
                        </button>
                    </p>
                    <div class="sid-upload-desc">
                        @translate('unidev.field.single.image.drop.desc')
                    </div>
                    @if ($field->get('show_size_notice', false))
                        @if ($options['crop'])
                            <div class="sid-size-info">
                                @sprintf('unidev.field.single.image.crop.size.desc', $options['width'], $options['height'])
                            </div>
                        @elseif ($options['max_width'] || $options['max_height'] || $options['min_width'] || $options['min_height'])
                            <div class="sid-size-info">
                                @if ($options['max_width'] || $options['max_height'])
                                    <div class="max-size">
                                        @if ($options['max_width'] !== null && $options['max_height'] !== null)
                                            @sprintf('unidev.field.single.image.max.width.height', $options['max_width'],
                                            $options['max_height'])
                                        @elseif ($options['max_width'] !== null)
                                            @sprintf('unidev.field.single.image.max.width', $options['max_width'])
                                        @elseif ($options['max_height'] !== null)
                                            @sprintf('unidev.field.single.image.max.height', $options['max_height'])
                                        @endif
                                    </div>
                                @endif

                                @if ($options['min_width'] || $options['min_height'])
                                    <div class="min-size">
                                        @if ($options['min_width'] !== null && $options['min_height'] !== null)
                                            @sprintf('unidev.field.single.image.min.width.height', $options['min_width'],
                                            $options['min_height'])
                                        @elseif ($options['min_width'] !== null)
                                            @sprintf('unidev.field.single.image.min.width', $options['min_width'])
                                        @elseif ($options['min_height'] !== null)
                                            @sprintf('unidev.field.single.image.min.height', $options['min_height'])
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                    <img src="{{ $asset->path . '/' . $packageName }}/images/ajax-loader.gif"
                         id="{{ $attrs['id'] . '-loader' }}" class="sid-loader" alt="Lading" style="display: none;">
                </div>

                @if (!$field->get('required'))
                    <div class="checkbox checkbox-primary mt-2" style="">
                        <input type="checkbox" id="{{ $attrs['id'] }}-delete-image" name="{{ $attrs['id'] }}-delete-image"
                               class="sid-delete-image"/>
                        <label for="{{ $attrs['id'] }}-delete-image">
                            @translate('unidev.field.single.image.delete')
                        </label>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if ($version === 1)
        <div style="display: none;">
            <input type="text" id="{{ $attrs['id'] }}-data" class="sid-data" name="{{ $attrs['id'] }}-data" value=""/>
        </div>
    @else
        {!! new \Windwalker\Dom\HtmlElement('input', null, $attrs) !!}
    @endif

    {{-- Push this modal to page bottom --}}
    @assetTemplate('single-image-upload@' . $attrs['id'])
    <div class="modal fade sid-modal" id="{{ $attrs['id'] }}-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        @translate('unidev.field.single.image.crop')
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="{{ $attrs['id'] }}-cropper" class="sid-cropper">

                        <!-- preview image -->
                        <div class="cropit-image-preview-container center-block"
                             style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px; margin: 0 auto;">
                            <div class="cropit-image-preview"
                                 style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px;"></div>
                        </div>

                        <!-- This range input controls zoom -->
                        <div class="slider-wrapper text-center" style="margin-top: 25px;">
                            <span class="fa fa-picture-o small-image" style="font-size: 15px"></span>
                            <input type="range" class="cropit-image-zoom-input custom"
                                   style="width: 130px; display: inline;">
                            <span class="fa fa-picture-o large-image" style="font-size: 25px"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-outline-secondary" data-dismiss="modal">
                        @translate('unidev.field.single.image.close')
                    </button>
                    <button type="button" class="btn btn-primary sid-save-button">
                        @translate('unidev.field.single.image.ok')
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endTemplate()
</div>
