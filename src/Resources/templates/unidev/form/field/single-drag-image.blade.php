{{-- Part of virtualset project. --}}
<?php
$packageName = $app->packageResolver->getAlias(\Lyrasoft\Unidev\UnidevPackage::class);
$defaultImage = isset($defaultImage) ? $defaultImage : $asset->path . '/' . $packageName . '/images/default-img.png';
$image = $attrs['value'] ? $attrs['value'] . '#' . uniqid() : e($defaultImage);
?>
<div id="{{ $attrs['id'] }}-wrap">

    <div class="sid-row">
        <div class="sid-left-col">
            <img class="sid-preview img-responsive img-fluid"
                {{-- TextField has escaped value, so we don't need to escape again --}}
                src="{!! $image !!}"
                alt="Preview">
        </div>
        <div class="sid-right-col">
            <div class="sid-area filedrag">
                <button class="btn btn-success btn-sm btn-xs sid-file-select-button" type="button">
                    @translate('unidev.field.single.image.button.select')
                </button>
                @translate('unidev.field.single.image.drop.desc')
                <img src="{{ $asset->path . '/' . $packageName }}/images/ajax-loader.gif" id="{{ $attrs['id'] . '-loader' }}" class="sid-loader" alt="Lading" style="display: none;">
            </div>
            <div class="checkbox checkbox-primary mt-2" style="">
                <input type="checkbox" id="{{ $attrs['id'] }}-delete-image" name="{{ $attrs['id'] }}-delete-image" class="sid-delete-image" />
                <label for="{{ $attrs['id'] }}-delete-image">
                    @translate('unidev.field.single.image.delete')
                </label>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <input type="text" id="{{ $attrs['id'] }}-data" class="sid-data" name="{{ $attrs['id'] }}-data" value="" />
    </div>

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
                        <div class="cropit-image-preview-container center-block" style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px; margin: 0 auto;">
                            <div class="cropit-image-preview" style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px;"></div>
                        </div>

                        <!-- This range input controls zoom -->
                        <div class="slider-wrapper text-center" style="margin-top: 25px;">
                            <span class="fa fa-picture-o small-image" style="font-size: 15px"></span>
                            <input type="range" class="cropit-image-zoom-input custom" style="width: 130px; display: inline;">
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
